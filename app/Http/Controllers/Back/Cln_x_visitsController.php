<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\back\cln_x_visits;
use App\Models\back\gnr_m_clinics;
use App\Models\back\gnr_m_patients;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class Cln_x_visitsController extends Controller
{

    public function getDateASNmber(string $date1,string $date2){
        $result = DB::table('cln_x_visits')
            ->select(DB::raw('UNIX_TIMESTAMP("' . $date1 . '") AS date1 ,UNIX_TIMESTAMP("' . $date2 . '") AS date2',))
            ->first();
        return [$result->date1,$result->date2];
    }
    public function getDateLastMounth(){
        $last =Carbon::now()->format('Y-m-d');
        $first = Carbon::createFromFormat('Y-m-d', $last)->subMonth()->format('Y-m-d');
        return $result = [$first,$last];
    }


    /*
     $start ="2020-07-01 00:00:00";
                    $end = "2020-07-30 11:59:00";
     * */
    public function index()
    {
        $this->authorize('viewAny', cln_x_visits::class);
        $visits = DB::table('cln_x_visits')
            ->join('cln_x_prev_com', function (JoinClause $join) {
                //not
                $request = request();
                $user = User::find(Auth::id());
                $doctor = $user->doctor->id;
               if ($request->mounth == null && $request->day == null && ($request->between1 ==null || $request->between2 == null)){
                   $time = $this->getDateLastMounth();
                   $start =$time[0];
                   $end = $time[1];
               }elseif ($request->mounth !== null){
                   $start = $request->mounth."-01 00:00:00";
                   $end = $request->mounth."-30 11:59:00";
               }elseif ($request->day !== null){
                   $start = $request->day." 00:00:00";
                   $end = $request->day." 16:59:00";
               }elseif ($request->between1 !== null && $request->between2 !== null){
                   $start = $request->between1;
                   $end = $request->between2;
               }


                $result = $this->getDateASNmber($start,$end);
                //not
                $join->on('cln_x_visits.id', '=', 'cln_x_prev_com.visit')
                    ->where('cln_x_prev_com.doc', '=', $doctor)
                    ->whereBetween('cln_x_visits.d_start', [$result[0],$result[1]]);


            })
            ->join('gnr_m_patients', function (JoinClause $join){
                $join->on('gnr_m_patients.id', '=', 'cln_x_visits.patient');
            })
            ->selectRaw('cln_x_visits.id,DATE_FORMAT(FROM_UNIXTIME(cln_x_visits.d_start), "%Y-%m-%d") AS date
            ,gnr_m_patients.f_name,cln_x_visits.clinic,cln_x_visits.note'.(Schema::hasColumn('cln_x_visits', 'price') ? ',cln_x_visits.price' : ''))
            ->distinct('cln_x_visits.id')

            ->paginate(30);

        $price = Schema::hasColumn('cln_x_visits', 'price') ? $visits->sum('price') : null;

        return view('back.visits.show', compact('visits','price'));
    }


    public function show($patient)
    {
        $patientModel = gnr_m_patients::findOrFail($patient);
        $this->authorize('view', $patientModel);
        $clinics = gnr_m_clinics::all();
        $visits = cln_x_visits::with(['gnr_m_clinics', 'invoice'])->where('patient','=',$patient)->get();
        return view('back.visits.index', compact('patient','visits','clinics'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => ['required', 'integer', 'exists:gnr_m_patients,id'],
            'clinic' => ['required', 'integer', 'exists:gnr_m_clinics,id'],
            'note' => ['nullable', 'string', 'max:2000'],
            'price' => [Rule::excludeIf(!Schema::hasColumn('cln_x_visits', 'price')), 'nullable', 'numeric', 'min:0'],
        ]);
        $patient = gnr_m_patients::findOrFail($validated['user_id']);
        $this->authorize('createForPatient', [cln_x_visits::class, $patient]);
        //dd($request);
        try {
            DB::transaction(function () use ($request) {
                if($request->clinic != null){
                    $data = ['patient' => $request->user_id, 'clinic' => $request->clinic, 'type' => 1, 'status' => 0, 'note' => $request->note, 'd_start' => time()];
                    if (Schema::hasColumn('cln_x_visits', 'price')) $data['price'] = $request->price;
                    DB::table('cln_x_visits')->insert($data);
                }

            });
            DB::commit();
            if($request->clinic != null) {
                return Redirect("visits/$request->user_id")->with('success', ' Saved!');
            }else{
                return Redirect("visits/$request->user_id")->with('success', ' You have to chose clinic!');
            }
        } catch (\Exception $ex) {
            DB::rollback();
            //return Redirect("patients")->with('error', $ex);

        }

    }

    public function edit(string $id)
    {
        $clinics = gnr_m_clinics::all();
        $visit = cln_x_visits::findOrFail($id);
        $this->authorize('update', $visit);
        return view('back.visits.edit',compact('visit','clinics'));
    }

    public function update(string $id,Request $request)
    {
        $visit = cln_x_visits::findOrFail($id);
        $this->authorize('update', $visit);
        $request->validate([
            'clinic' => ['required', 'integer', 'exists:gnr_m_clinics,id'],
            'note' => ['nullable', 'string', 'max:2000'],
            'price' => [Rule::excludeIf(!Schema::hasColumn('cln_x_visits', 'price')), 'nullable', 'numeric', 'min:0'],
        ]);
        try {
            DB::transaction(function () use ($id,$request) {
                if($request->clinic != null){
                    $data = ['clinic' => $request->clinic, 'note' => $request->note];
                    if (Schema::hasColumn('cln_x_visits', 'price')) $data['price'] = $request->price;
                    DB::table('cln_x_visits')->where('id', $id)->update($data);
                   }

            });
            DB::commit();
            if($request->clinic != null) {
                return Redirect("visits/$request->user_id")->with('success', ' Updated!');
            }else{
                return Redirect("visits/$request->user_id")->with('success', ' You have to chose clinic!');
            }
        } catch (\Exception $ex) {
            DB::rollback();
        }
    }

    public function destroy(Request $request)
    {
        $request->validate(['input' => ['required', 'integer', 'exists:cln_x_visits,id']]);
        $visit = cln_x_visits::findOrFail($request->input);
        $this->authorize('delete', $visit);
        $services = $visit->cln_m_services;
        $com = $visit->cln_x_prev_com;
        try {
            DB::transaction(function () use ($request,$services,$com) {
                if ($services->isNotEmpty() == null && $com->isNotEmpty() == null){
                    DB::table('cln_x_visits')->where('id', '=',$request->input)->delete();
                }

            });
            DB::commit();
            if ($services->isNotEmpty() == null && $com->isNotEmpty() == null) {
                return Redirect()->back()->with('success', ' deleted!');
            }elseif ($services->isNotEmpty() !== null || $com->isNotEmpty() !== null){
                return Redirect()->back()->with('success', ' We Can not Delete This Visit!');
            }

        } catch (\Exception $ex) {
            DB::rollback();
        }
    }
}
