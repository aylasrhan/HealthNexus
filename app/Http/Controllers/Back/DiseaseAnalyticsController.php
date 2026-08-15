<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DiseaseAnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $filters = $this->filters($request);
        $lookups = $this->lookups();
        $selectedDiseaseId = (int) ($filters['disease'] ?: optional($lookups['diseases']->first())->id);
        $filters['disease'] = $selectedDiseaseId ?: null;
        $selectedDisease = $lookups['diseases']->firstWhere('id', $selectedDiseaseId);

        if (!$selectedDiseaseId) {
            return view('back.analytics.diseases', array_merge($lookups, compact('filters', 'selectedDisease'), $this->emptyResults()));
        }

        $query = $this->diagnosesQuery($selectedDiseaseId, $filters);
        $uniquePatients = (clone $query)->distinct()->count('diagnosis.patient');
        $population = $this->populationQuery($filters)->count();

        $summary = [
            'patients' => $uniquePatients,
            'records' => (clone $query)->count(),
            'cities' => (clone $query)->where('patient.p_city', '>', 0)->distinct()->count('patient.p_city'),
            'prevalence' => $population ? round($uniquePatients * 100 / $population, 2) : 0,
            'population' => $population,
        ];

        $cityDistribution = (clone $query)
            ->leftJoin('gnr_m_cities as city', 'city.id', '=', 'patient.p_city')
            ->leftJoinSub(DB::table('gnr_m_patients')->selectRaw('p_city, COUNT(*) population')->groupBy('p_city'), 'city_population', 'city_population.p_city', '=', 'patient.p_city')
            ->select('patient.p_city as city_id')
            ->selectRaw("COALESCE(NULLIF(TRIM(city.name), ''), 'غير محددة') location_name")
            ->selectRaw('COUNT(DISTINCT diagnosis.patient) patients_count')
            ->selectRaw('MAX(COALESCE(city_population.population, 0)) population')
            ->groupBy('patient.p_city', 'city.name')->orderByDesc('patients_count')->get()
            ->each(fn ($row) => $row->rate_per_1000 = $row->population ? round($row->patients_count * 1000 / $row->population, 2) : 0);

        $areaDistribution = (clone $query)
            ->leftJoin('gnr_m_areas as area', 'area.id', '=', 'patient.p_area')
            ->leftJoin('gnr_m_cities as city', 'city.id', '=', 'patient.p_city')
            ->selectRaw("COALESCE(NULLIF(TRIM(area.name), ''), 'غير محددة') area_name")
            ->selectRaw("COALESCE(NULLIF(TRIM(city.name), ''), 'غير محددة') city_name")
            ->selectRaw('COUNT(DISTINCT diagnosis.patient) patients_count')
            ->groupBy('patient.p_area', 'area.name', 'city.name')->orderByDesc('patients_count')->limit(12)->get();

        $genderDistribution = (clone $query)
            ->selectRaw("CASE WHEN patient.sex = 1 THEN 'ذكر' WHEN patient.sex = 2 THEN 'أنثى' ELSE 'غير محدد' END gender")
            ->selectRaw('COUNT(DISTINCT diagnosis.patient) patients_count')
            ->groupBy('patient.sex')->orderByDesc('patients_count')->get();

        $ageDistribution = (clone $query)
            ->selectRaw("CASE WHEN TIMESTAMPDIFF(YEAR, patient.birth_date, CURDATE()) < 13 THEN 'أطفال (0–12)' WHEN TIMESTAMPDIFF(YEAR, patient.birth_date, CURDATE()) < 25 THEN 'شباب (13–24)' WHEN TIMESTAMPDIFF(YEAR, patient.birth_date, CURDATE()) < 45 THEN 'بالغون (25–44)' WHEN TIMESTAMPDIFF(YEAR, patient.birth_date, CURDATE()) < 65 THEN 'متوسطو العمر (45–64)' ELSE 'كبار السن (65+)' END age_group")
            ->selectRaw('COUNT(DISTINCT diagnosis.patient) patients_count')
            ->groupBy('age_group')->orderByDesc('patients_count')->get();

        $mapDetails = $this->mapDetails($query, $cityDistribution, $uniquePatients, $selectedDiseaseId, $filters);

        $trend = (clone $query)->whereRaw($this->eventTime().' > 0')
            ->selectRaw("DATE_FORMAT(FROM_UNIXTIME({$this->eventTime()}), '%Y-%m') period")
            ->selectRaw('COUNT(DISTINCT diagnosis.patient) patients_count')
            ->groupBy('period')->orderByDesc('period')->limit(24)->get()->sortBy('period')->values();

        $comparison = $this->comparison($selectedDiseaseId, $filters, $uniquePatients);
        $quality = $this->qualityMetrics();

        return view('back.analytics.diseases', array_merge($lookups, compact(
            'filters', 'selectedDisease', 'summary', 'cityDistribution', 'areaDistribution',
            'genderDistribution', 'ageDistribution', 'mapDetails', 'trend', 'comparison', 'quality'
        )));
    }

    public function export(Request $request): StreamedResponse
    {
        $filters = $this->filters($request);
        abort_unless($filters['disease'], 422, 'يجب اختيار المرض.');
        $rows = $this->diagnosesQuery((int) $filters['disease'], $filters)
            ->leftJoin('cln_m_icd10 as disease', 'disease.id', '=', 'diagnosis.opr_id')
            ->leftJoin('gnr_m_cities as city', 'city.id', '=', 'patient.p_city')
            ->leftJoin('gnr_m_areas as area', 'area.id', '=', 'patient.p_area')
            ->select('disease.code', 'disease.name_ar', 'city.name as city_name', 'area.name as area_name')
            ->selectRaw('COUNT(DISTINCT diagnosis.patient) patients_count')
            ->groupBy('disease.code', 'disease.name_ar', 'city.name', 'area.name')
            ->havingRaw('COUNT(DISTINCT diagnosis.patient) >= 5')
            ->orderByDesc('patients_count')->cursor();

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'wb'); fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['رمز المرض', 'المرض', 'المحافظة', 'المنطقة', 'عدد المرضى']);
            foreach ($rows as $row) fputcsv($out, [$row->code, $row->name_ar, trim($row->city_name ?? 'غير محددة'), $row->area_name ?? 'غير محددة', $row->patients_count]);
            fclose($out);
        }, 'disease-analysis-'.now()->format('Y-m-d-His').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function filters(Request $request): array
    {
        return array_merge(['disease'=>null,'city'=>null,'area'=>null,'sex'=>null,'age_group'=>null,'date_from'=>null,'date_to'=>null], $request->validate([
            'disease'=>['nullable','integer','exists:cln_m_icd10,id'], 'city'=>['nullable','integer','exists:gnr_m_cities,id'],
            'area'=>['nullable','integer','exists:gnr_m_areas,id'],
            'sex'=>['nullable','in:1,2'], 'age_group'=>['nullable','in:child,youth,adult,middle,senior'],
            'date_from'=>['nullable','date_format:Y-m-d'], 'date_to'=>['nullable','date_format:Y-m-d','after_or_equal:date_from'],
        ]));
    }

    private function lookups(): array
    {
        return [
            'diseases'=>DB::table('cln_m_icd10 as d')->join('cln_x_prev_icd10 as x','x.opr_id','=','d.id')->select('d.id','d.code','d.name_ar','d.name_en')->selectRaw('COUNT(*) uses_count')->groupBy('d.id','d.code','d.name_ar','d.name_en')->orderByDesc('uses_count')->get(),
            'cities'=>DB::table('gnr_m_cities')->select('id','name')->orderBy('name')->get()
                ->filter(fn ($city) => in_array(trim((string) $city->name), $this->syrianGovernorates(), true))
                ->values(),
            'areas'=>DB::table('gnr_m_areas')->select('id','city','name')->orderBy('name')->get(),
        ];
    }

    private function diagnosesQuery(int $diseaseId, array $filters, ?array $period = null): Builder
    {
        $fromDate = $period['from'] ?? $filters['date_from']; $toDate = $period['to'] ?? $filters['date_to'];
        $from = $fromDate ? Carbon::parse($fromDate)->startOfDay()->timestamp : null;
        $to = $toDate ? Carbon::parse($toDate)->endOfDay()->timestamp : null;
        return DB::table('cln_x_prev_icd10 as diagnosis')->join('gnr_m_patients as patient','patient.id','=','diagnosis.patient')->leftJoin('cln_x_visits as visit','visit.id','=','diagnosis.visit')
            ->where('diagnosis.opr_id',$diseaseId)
            ->when($filters['city'],fn(Builder $q,$v)=>$q->where('patient.p_city',$v))->when($filters['area'],fn(Builder $q,$v)=>$q->where('patient.p_area',$v))
            ->when($filters['sex'],fn(Builder $q,$v)=>$q->where('patient.sex',$v))
            ->when($filters['age_group'],fn(Builder $q,$v)=>$this->applyAge($q,$v))
            ->when($from,fn(Builder $q,$v)=>$q->whereRaw($this->eventTime().' >= ?',[$v]))->when($to,fn(Builder $q,$v)=>$q->whereRaw($this->eventTime().' <= ?',[$v]));
    }

    private function populationQuery(array $filters): Builder
    {
        return DB::table('gnr_m_patients as patient')->when($filters['city'],fn(Builder $q,$v)=>$q->where('p_city',$v))->when($filters['area'],fn(Builder $q,$v)=>$q->where('p_area',$v))->when($filters['sex'],fn(Builder $q,$v)=>$q->where('sex',$v))->when($filters['age_group'],fn(Builder $q,$v)=>$this->applyAge($q,$v));
    }

    private function applyAge(Builder $query, string $group): Builder
    {
        $ranges=['child'=>[0,12],'youth'=>[13,24],'adult'=>[25,44],'middle'=>[45,64],'senior'=>[65,150]];
        return $query->whereBetween(DB::raw('TIMESTAMPDIFF(YEAR, patient.birth_date, CURDATE())'),$ranges[$group]);
    }

    private function comparison(int $diseaseId, array $filters, int $current): array
    {
        if (!$filters['date_from'] || !$filters['date_to']) return ['available'=>false,'previous'=>0,'change'=>null];
        $from=Carbon::parse($filters['date_from']); $to=Carbon::parse($filters['date_to']); $days=$from->diffInDays($to)+1;
        $previousTo=$from->copy()->subDay(); $previousFrom=$previousTo->copy()->subDays($days-1);
        $previous=$this->diagnosesQuery($diseaseId,$filters,['from'=>$previousFrom->toDateString(),'to'=>$previousTo->toDateString()])->distinct()->count('diagnosis.patient');
        return ['available'=>true,'previous'=>$previous,'change'=>$previous ? round(($current-$previous)*100/$previous,1) : ($current ? 100 : 0),'from'=>$previousFrom->toDateString(),'to'=>$previousTo->toDateString()];
    }

    private function qualityMetrics(): array
    {
        $patients=DB::table('gnr_m_patients')->count(); $coded=DB::table('cln_x_prev_icd10')->count(); $free=DB::table('cln_x_prev_dia')->whereNotNull('val')->where('val','<>','')->count();
        return ['missing_location'=>DB::table('gnr_m_patients')->where(fn($q)=>$q->whereNull('p_city')->orWhere('p_city','<=',0)->orWhereNull('p_area')->orWhere('p_area','<=',0))->count(), 'patients'=>$patients, 'missing_dates'=>DB::table('cln_x_prev_icd10 as d')->leftJoin('cln_x_visits as v','v.id','=','d.visit')->whereRaw('COALESCE(NULLIF(d.date, 0), v.d_start) <= 0')->count(), 'free_text'=>$free, 'coding_rate'=>($coded+$free) ? round($coded*100/($coded+$free),1) : 0];
    }

    private function mapDetails(Builder $query, $cities, int $totalPatients, int $diseaseId, array $filters): array
    {
        $details = [];
        foreach ($cities->values() as $index => $city) {
            $details[(string) $city->city_id] = [
                'id' => (string) $city->city_id,
                'name' => trim((string) $city->location_name),
                'patients' => (int) $city->patients_count,
                'population' => (int) $city->population,
                'rate' => (float) $city->rate_per_1000,
                'share' => $totalPatients ? round($city->patients_count * 100 / $totalPatients, 1) : 0,
                'rank' => $index + 1,
                'areas' => [], 'genders' => [], 'ages' => [], 'previous' => null, 'change' => null,
            ];
        }

        $areas = (clone $query)
            ->leftJoin('gnr_m_areas as map_area', 'map_area.id', '=', 'patient.p_area')
            ->where('patient.p_city', '>', 0)
            ->select('patient.p_city as city_id')
            ->selectRaw("COALESCE(NULLIF(TRIM(map_area.name), ''), 'غير محددة') label")
            ->selectRaw('COUNT(DISTINCT diagnosis.patient) patients_count')
            ->groupBy('patient.p_city', 'patient.p_area', 'map_area.name')
            ->orderByDesc('patients_count')->get()->groupBy('city_id');

        $genders = (clone $query)->where('patient.p_city', '>', 0)
            ->select('patient.p_city as city_id')
            ->selectRaw("CASE WHEN patient.sex = 1 THEN 'ذكر' WHEN patient.sex = 2 THEN 'أنثى' ELSE 'غير محدد' END label")
            ->selectRaw('COUNT(DISTINCT diagnosis.patient) patients_count')
            ->groupBy('patient.p_city', 'patient.sex')->get()->groupBy('city_id');

        $ages = (clone $query)->where('patient.p_city', '>', 0)
            ->select('patient.p_city as city_id')
            ->selectRaw("CASE WHEN TIMESTAMPDIFF(YEAR, patient.birth_date, CURDATE()) < 13 THEN 'أطفال' WHEN TIMESTAMPDIFF(YEAR, patient.birth_date, CURDATE()) < 25 THEN 'شباب' WHEN TIMESTAMPDIFF(YEAR, patient.birth_date, CURDATE()) < 45 THEN 'بالغون' WHEN TIMESTAMPDIFF(YEAR, patient.birth_date, CURDATE()) < 65 THEN 'متوسطو العمر' ELSE 'كبار السن' END label")
            ->selectRaw('COUNT(DISTINCT diagnosis.patient) patients_count')
            ->groupBy('patient.p_city', 'label')->get()->groupBy('city_id');

        $previousByCity = collect();
        if ($filters['date_from'] && $filters['date_to']) {
            $from = Carbon::parse($filters['date_from']); $to = Carbon::parse($filters['date_to']);
            $days = $from->diffInDays($to) + 1; $previousTo = $from->copy()->subDay(); $previousFrom = $previousTo->copy()->subDays($days - 1);
            $previousByCity = $this->diagnosesQuery($diseaseId, $filters, ['from'=>$previousFrom->toDateString(), 'to'=>$previousTo->toDateString()])
                ->where('patient.p_city', '>', 0)->select('patient.p_city as city_id')
                ->selectRaw('COUNT(DISTINCT diagnosis.patient) patients_count')->groupBy('patient.p_city')->pluck('patients_count', 'city_id');
        }

        foreach ($details as $id => &$detail) {
            $detail['areas'] = collect($areas->get($id, collect()))->take(5)->map(fn ($row) => ['label'=>$row->label, 'count'=>(int)$row->patients_count])->values()->all();
            $detail['genders'] = collect($genders->get($id, collect()))->map(fn ($row) => ['label'=>$row->label, 'count'=>(int)$row->patients_count])->values()->all();
            $detail['ages'] = collect($ages->get($id, collect()))->map(fn ($row) => ['label'=>$row->label, 'count'=>(int)$row->patients_count])->values()->all();
            if ($filters['date_from'] && $filters['date_to']) {
                $detail['previous'] = (int) $previousByCity->get($id, 0);
                $detail['change'] = $detail['previous'] ? round(($detail['patients'] - $detail['previous']) * 100 / $detail['previous'], 1) : ($detail['patients'] ? 100 : 0);
            }
        }

        return $details;
    }

    private function eventTime(): string { return 'COALESCE(NULLIF(diagnosis.date, 0), visit.d_start)'; }
    private function syrianGovernorates(): array { return ['دمشق','ريف دمشق','القنيطرة','درعا','السويداء','حمص','حماة','طرطوس','اللاذقية','إدلب','حلب','الرقة','دير الزور','الحسكة']; }
    private function emptyResults(): array { return ['summary'=>['patients'=>0,'records'=>0,'cities'=>0,'prevalence'=>0,'population'=>0],'cityDistribution'=>collect(),'areaDistribution'=>collect(),'genderDistribution'=>collect(),'ageDistribution'=>collect(),'mapDetails'=>[],'trend'=>collect(),'comparison'=>['available'=>false,'previous'=>0,'change'=>null],'quality'=>['missing_location'=>0,'patients'=>0,'missing_dates'=>0,'free_text'=>0,'coding_rate'=>0]]; }
}
