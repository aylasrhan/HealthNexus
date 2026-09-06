<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\back\gnr_m_areas;
use App\Models\back\gnr_m_clinics;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportsController extends Controller
{
    public function index(Request $request)
    {
        $filters = $this->validatedFilters($request);
        $visitsQuery = $this->visitsQuery($filters);

        $summary = [
            'patients' => $this->patientsQuery($filters)->count(),
            'visits' => (clone $visitsQuery)->count(),
            'appointments' => $this->appointmentsQuery($filters)->distinct()->count('appointments.id'),
            'clinics' => (clone $visitsQuery)->distinct()->count('cln_x_visits.clinic'),
        ];

        $topClinics = (clone $visitsQuery)
            ->leftJoin('gnr_m_clinics', 'gnr_m_clinics.id', '=', 'cln_x_visits.clinic')
            ->selectRaw('cln_x_visits.clinic, COALESCE(gnr_m_clinics.name_ar, gnr_m_clinics.name_en, ?) as clinic_name, COUNT(*) as total', ['غير محددة'])
            ->groupBy('cln_x_visits.clinic', 'gnr_m_clinics.name_ar', 'gnr_m_clinics.name_en')
            ->orderByDesc('total')->limit(8)->get();

        $topDiagnoses = DB::table('cln_x_prev_dia')
            ->join('cln_x_visits', 'cln_x_visits.id', '=', 'cln_x_prev_dia.visit')
            ->join('gnr_m_patients', 'gnr_m_patients.id', '=', 'cln_x_visits.patient')
            ->when($filters['area'], fn (Builder $q, $area) => $q->where('gnr_m_patients.p_area', $area))
            ->when($filters['clinic'], fn (Builder $q, $clinic) => $q->where('cln_x_visits.clinic', $clinic))
            ->when($filters['from_ts'], fn (Builder $q, $from) => $q->where('cln_x_visits.d_start', '>=', $from))
            ->when($filters['to_ts'], fn (Builder $q, $to) => $q->where('cln_x_visits.d_start', '<=', $to))
            ->whereNotNull('cln_x_prev_dia.val')->where('cln_x_prev_dia.val', '<>', '')
            ->selectRaw('cln_x_prev_dia.val as diagnosis, COUNT(*) as total')
            ->groupBy('cln_x_prev_dia.val')->orderByDesc('total')->limit(8)->get();

        $recentVisits = (clone $visitsQuery)
            ->leftJoin('gnr_m_clinics', 'gnr_m_clinics.id', '=', 'cln_x_visits.clinic')
            ->select('cln_x_visits.id', 'cln_x_visits.d_start', 'cln_x_visits.status', 'gnr_m_patients.f_name', 'gnr_m_patients.l_name', 'gnr_m_clinics.name_ar as clinic_name')
            ->orderByDesc('cln_x_visits.d_start')->limit(10)->get();

        return view('back.reports.index', [
            'areas' => gnr_m_areas::orderBy('name')->get(),
            'clinics' => gnr_m_clinics::orderBy('name_ar')->get(),
            'filters' => $filters,
            'summary' => $summary,
            'topClinics' => $topClinics,
            'topDiagnoses' => $topDiagnoses,
            'recentVisits' => $recentVisits,
        ]);
    }

    public function store(Request $request): StreamedResponse
    {
        $filters = $this->validatedFilters($request);
        $rows = $this->visitsQuery($filters)
            ->leftJoin('gnr_m_clinics', 'gnr_m_clinics.id', '=', 'cln_x_visits.clinic')
            ->select('cln_x_visits.id', 'cln_x_visits.d_start', 'cln_x_visits.status', 'gnr_m_patients.f_name', 'gnr_m_patients.l_name', 'gnr_m_clinics.name_ar as clinic_name')
            ->orderByDesc('cln_x_visits.d_start')->limit(10000)->cursor();

        return response()->streamDownload(function () use ($rows) {
            $output = fopen('php://output', 'wb');
            fwrite($output, "\xEF\xBB\xBF");
            fputcsv($output, ['رقم الزيارة', 'المريض', 'العيادة', 'التاريخ', 'الحالة']);
            foreach ($rows as $row) {
                fputcsv($output, [
                    $row->id,
                    trim($row->f_name.' '.$row->l_name),
                    $row->clinic_name ?: 'غير محددة',
                    $row->d_start ? Carbon::createFromTimestamp((int) $row->d_start)->format('Y-m-d H:i') : '',
                    (string) $row->status === '1' ? 'مكتملة' : 'مفتوحة',
                ]);
            }
            fclose($output);
        }, 'wecare-report-'.now()->format('Y-m-d-His').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function validatedFilters(Request $request): array
    {
        $data = $request->validate([
            'area' => ['nullable', 'integer', 'exists:gnr_m_areas,id'],
            'clinic' => ['nullable', 'integer', 'exists:gnr_m_clinics,id'],
            'date_from' => ['nullable', 'date_format:Y-m-d'],
            'date_to' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:date_from'],
        ]);

        return [
            'area' => $data['area'] ?? null,
            'clinic' => $data['clinic'] ?? null,
            'date_from' => $data['date_from'] ?? null,
            'date_to' => $data['date_to'] ?? null,
            'from_ts' => isset($data['date_from']) ? Carbon::parse($data['date_from'])->startOfDay()->timestamp : null,
            'to_ts' => isset($data['date_to']) ? Carbon::parse($data['date_to'])->endOfDay()->timestamp : null,
        ];
    }

    private function patientsQuery(array $filters): Builder
    {
        return DB::table('gnr_m_patients')
            ->when($filters['area'], fn (Builder $q, $area) => $q->where('p_area', $area));
    }

    private function visitsQuery(array $filters): Builder
    {
        return DB::table('cln_x_visits')
            ->join('gnr_m_patients', 'gnr_m_patients.id', '=', 'cln_x_visits.patient')
            ->when($filters['area'], fn (Builder $q, $area) => $q->where('gnr_m_patients.p_area', $area))
            ->when($filters['clinic'], fn (Builder $q, $clinic) => $q->where('cln_x_visits.clinic', $clinic))
            ->when($filters['from_ts'], fn (Builder $q, $from) => $q->where('cln_x_visits.d_start', '>=', $from))
            ->when($filters['to_ts'], fn (Builder $q, $to) => $q->where('cln_x_visits.d_start', '<=', $to));
    }

    private function appointmentsQuery(array $filters): Builder
    {
        return DB::table('appointments')
            ->leftJoin('doctors', function ($join) {
                $join->on('doctors.id', '=', 'appointments.appointment_with')
                    ->orOn('doctors.user_id', '=', 'appointments.appointment_with');
            })
            ->leftJoin('gnr_m_patients', 'gnr_m_patients.user_id', '=', 'appointments.appointment_for')
            ->where(fn (Builder $q) => $q->where('appointments.is_deleted', 0)->orWhereNull('appointments.is_deleted'))
            ->when($filters['area'], fn (Builder $q, $area) => $q->where('gnr_m_patients.p_area', $area))
            ->when($filters['clinic'], fn (Builder $q, $clinic) => $q->where('doctors.subgrp', $clinic))
            ->when($filters['date_from'], fn (Builder $q, $from) => $q->whereDate('appointments.appointment_date', '>=', $from))
            ->when($filters['date_to'], fn (Builder $q, $to) => $q->whereDate('appointments.appointment_date', '<=', $to));
    }
}
