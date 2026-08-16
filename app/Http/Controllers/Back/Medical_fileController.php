<?php

namespace App\Http\Controllers\Back;

use App\Enums\AppointmentStatus;
use App\Enums\VisitStatus;
use App\Http\Controllers\Controller;
use App\Models\Prescription;
use App\Models\VisitVital;
use App\Services\InvoiceService;
use App\Models\back\cln_m_icd10;
use App\Models\back\cln_m_medical_his;
use App\Models\back\cln_m_medical_his_cats;
use App\Models\back\cln_m_services;
use App\Models\back\cln_x_prev_icd10;
use App\Models\back\cln_x_visits;
use App\Models\back\Appointment;
use App\Models\back\gnr_m_patients;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;

class Medical_fileController extends Controller
{
    public function startConsultation(Appointment $appointment)
    {
        $doctor = $this->currentDoctorFor($appointment);
        abort_unless((int) $appointment->status === AppointmentStatus::Confirmed->value, 422, 'يجب تأكيد الموعد قبل بدء المعاينة.');

        $patient = gnr_m_patients::where('user_id', $appointment->appointment_for)->firstOrFail();
        $visitTimestamp = strtotime($appointment->appointment_date.' '.($appointment->time ?: '00:00:00'));
        $visit = cln_x_visits::query()->where('appointment_id', $appointment->id)->first();
        if (!$visit) {
            $visit = cln_x_visits::query()
                ->whereNull('appointment_id')
                ->where('patient', $patient->id)
                ->where('d_start', $visitTimestamp)
                ->first();
            if ($visit) {
                DB::table('cln_x_visits')->where('id', $visit->id)->update([
                    'appointment_id' => $appointment->id,
                    'doctor_id' => $doctor->id,
                    'updated_at' => now(),
                ]);
                $visit->appointment_id = $appointment->id;
                $visit->doctor_id = $doctor->id;
            }
        }
        $clinicId = (int) ($doctor->subgrp ?: $visit?->clinic);
        abort_if($clinicId <= 0, 422, 'لا توجد عيادة مرتبطة بملف الطبيب.');

        if (!$visit) {
            $visit = new cln_x_visits();
            $visit->timestamps = false;
            $visit->patient = $patient->id;
            $visit->appointment_id = $appointment->id;
            $visit->clinic = $clinicId;
            $visit->doctor_id = $doctor->id;
            $visit->type = 1;
            $visit->status = VisitStatus::Draft->value;
            $visit->d_start = $visitTimestamp ?: time();
            $visit->updated_at = now();
            $visit->save();
        } elseif ((int) $visit->clinic !== $clinicId && (int) $visit->status === VisitStatus::Draft->value) {
            DB::table('cln_x_visits')->where('id', $visit->id)->update(['clinic' => $clinicId, 'doctor_id' => $doctor->id, 'updated_at' => now()]);
            $visit->clinic = $clinicId;
        }

        return redirect()->route('consultations.edit', $visit);
    }

    public function editConsultation(cln_x_visits $visit)
    {
        $this->authorize('writeMedicalFile', $visit);

        return view('back.consultations.edit', $this->consultationData($visit));
    }

    public function searchDiagnoses(Request $request)
    {
        abort_unless($request->user()?->hasSystemRole('doctor'), 403);
        $term = trim((string) $request->query('q'));

        $rows = cln_m_icd10::query()
            ->when($term !== '', fn ($query) => $query->where(function ($nested) use ($term) {
                $nested->where('name_ar', 'like', "%{$term}%")
                    ->orWhere('name_en', 'like', "%{$term}%")
                    ->orWhere('code', 'like', "{$term}%");
            }))
            ->orderBy('code')
            ->limit(30)
            ->get(['id', 'code', 'name_ar']);

        return response()->json(['results' => $rows->map(fn ($row) => [
            'id' => $row->id,
            'text' => trim($row->code.' — '.$row->name_ar),
        ])]);
    }

    public function searchMedicalHistory(Request $request)
    {
        abort_unless($request->user()?->hasSystemRole('doctor'), 403);
        $validated = $request->validate(['cat' => ['required', 'integer', 'exists:cln_m_medical_his_cats,id']]);
        $term = trim((string) $request->query('q'));

        $rows = cln_m_medical_his::query()
            ->where('cat', $validated['cat'])
            ->when($term !== '', fn ($query) => $query->where(function ($nested) use ($term) {
                $nested->where('name_ar', 'like', "%{$term}%")
                    ->orWhere('name_en', 'like', "%{$term}%");
            }))
            ->orderBy('name_ar')->limit(30)->get(['id', 'name_ar']);

        return response()->json(['results' => $rows->map(fn ($row) => ['id' => $row->id, 'text' => $row->name_ar])]);
    }

    public function saveConsultation(Request $request, cln_x_visits $visit)
    {
        $this->authorize('writeMedicalFile', $visit);
        abort_if((int) $visit->status === VisitStatus::Completed->value, 409, 'المعاينة مكتملة. يجب إعادة فتحها قبل التعديل.');

        $validated = $request->validate([
            'chief_complaint' => ['nullable', 'string', 'max:4000'],
            'history' => ['nullable', 'string', 'max:8000'],
            'clinical_exam' => ['nullable', 'string', 'max:8000'],
            'doctor_notes' => ['nullable', 'string', 'max:8000'],
            'diagnoses' => ['nullable', 'array'],
            'diagnoses.*' => ['integer', 'exists:cln_m_icd10,id'],
            'services' => ['nullable', 'array'],
            'services.*' => [
                'integer',
                Rule::exists('cln_m_services', 'id')->where(fn ($query) => $query->where('clinic', $visit->clinic)),
            ],
            'medical_history' => ['nullable', 'array'],
            'medical_history.*' => ['nullable', 'array'],
            'medical_history.*.*' => ['integer', 'exists:cln_m_medical_his,id'],
            'history_notes' => ['nullable', 'array'],
            'history_notes.*' => ['nullable', 'string', 'max:2000'],
            'completion_status' => ['required', Rule::in(['draft', 'complete'])],
            'vitals' => ['nullable', 'array'],
            'vitals.temperature' => ['nullable', 'numeric', 'between:30,45'],
            'vitals.systolic_pressure' => ['nullable', 'integer', 'between:50,300'],
            'vitals.diastolic_pressure' => ['nullable', 'integer', 'between:30,200'],
            'vitals.pulse' => ['nullable', 'integer', 'between:20,250'],
            'vitals.respiratory_rate' => ['nullable', 'integer', 'between:5,80'],
            'vitals.oxygen_saturation' => ['nullable', 'numeric', 'between:40,100'],
            'vitals.weight' => ['nullable', 'numeric', 'between:1,500'],
            'vitals.height' => ['nullable', 'numeric', 'between:30,250'],
            'vitals.blood_sugar' => ['nullable', 'numeric', 'between:20,1000'],
            'prescription_notes' => ['nullable', 'string', 'max:4000'],
            'prescription_items' => ['nullable', 'array', 'max:30'],
            'prescription_items.*.medication_name' => ['nullable', 'string', 'max:255'],
            'prescription_items.*.dosage' => ['nullable', 'string', 'max:120'],
            'prescription_items.*.frequency' => ['nullable', 'string', 'max:120'],
            'prescription_items.*.duration' => ['nullable', 'string', 'max:120'],
            'prescription_items.*.route' => ['nullable', 'string', 'max:120'],
            'prescription_items.*.instructions' => ['nullable', 'string', 'max:2000'],
            'follow_up_date' => ['nullable', 'date', 'after_or_equal:today'],
            'follow_up_time' => ['nullable', 'date_format:H:i', 'required_with:follow_up_date'],
        ]);

        $doctorId = (int) auth()->user()->doctor->id;
        DB::transaction(function () use ($visit, $validated, $doctorId) {
            $textSections = [
                'cln_x_prev_com' => $validated['chief_complaint'] ?? null,
                'cln_x_prev_str' => $validated['history'] ?? null,
                'cln_x_prev_cln' => $validated['clinical_exam'] ?? null,
                'cln_x_prev_not' => $validated['doctor_notes'] ?? null,
            ];

            foreach ($textSections as $table => $value) {
                DB::table($table)->where('visit', $visit->id)->where('doc', $doctorId)->delete();
                if (filled($value)) {
                    DB::table($table)->insert([
                        'visit' => $visit->id,
                        'patient' => $visit->patient,
                        'doc' => $doctorId,
                        'val' => trim($value),
                    ]);
                }
            }

            DB::table('cln_x_prev_icd10')->where('visit', $visit->id)->where('doc', $doctorId)->delete();
            foreach (array_unique($validated['diagnoses'] ?? []) as $diagnosisId) {
                DB::table('cln_x_prev_icd10')->insert([
                    'visit' => $visit->id,
                    'patient' => $visit->patient,
                    'opr_id' => $diagnosisId,
                    'doc' => $doctorId,
                ]);
            }

            DB::table('cln_x_medical_his')->where('patient', $visit->patient)->delete();
            foreach (($validated['medical_history'] ?? []) as $categoryId => $medicalIds) {
                $allowedIds = cln_m_medical_his::query()->where('cat', (int) $categoryId)
                    ->whereIn('id', array_unique($medicalIds ?? []))->pluck('id');
                foreach ($allowedIds as $medicalId) {
                    DB::table('cln_x_medical_his')->insert([
                        'cat' => (int) $categoryId,
                        'med_id' => $medicalId,
                        'patient' => $visit->patient,
                        'doc' => $doctorId,
                        'note' => trim((string) ($validated['history_notes'][$categoryId] ?? '')) ?: null,
                    ]);
                }
            }

            DB::table('cln_x_visits_services')->where('visit_id', $visit->id)->delete();
            foreach (array_unique($validated['services'] ?? []) as $serviceId) {
                DB::table('cln_x_visits_services')->insert([
                    'visit_id' => $visit->id,
                    'clinic' => $visit->clinic,
                    'service' => $serviceId,
                    'status' => 0,
                    'patient' => $visit->patient,
                    'd_start' => time(),
                    'srv_type' => 0,
                ]);
            }

            $vitals = array_filter($validated['vitals'] ?? [], fn ($value) => $value !== null && $value !== '');
            if ($vitals) {
                $height = isset($vitals['height']) ? (float) $vitals['height'] : null;
                $weight = isset($vitals['weight']) ? (float) $vitals['weight'] : null;
                $vitals['bmi'] = $height && $weight ? round($weight / (($height / 100) ** 2), 2) : null;
                VisitVital::updateOrCreate(['visit_id' => $visit->id], $vitals + [
                    'recorded_by' => auth()->id(),
                    'measured_at' => now(),
                ]);
            } else {
                VisitVital::where('visit_id', $visit->id)->delete();
            }

            $prescriptionItems = collect($validated['prescription_items'] ?? [])
                ->filter(fn ($item) => filled($item['medication_name'] ?? null))->values();
            if ($prescriptionItems->isNotEmpty() || filled($validated['prescription_notes'] ?? null)) {
                foreach ($prescriptionItems as $item) {
                    if ($validated['completion_status'] === 'complete' && (!filled($item['dosage'] ?? null) || !filled($item['frequency'] ?? null) || !filled($item['duration'] ?? null))) {
                        abort(422, 'يجب إدخال الجرعة والتكرار والمدة لكل دواء.');
                    }
                }
                $prescription = Prescription::updateOrCreate(['visit_id' => $visit->id], [
                    'patient_id' => $visit->patient,
                    'doctor_id' => $doctorId,
                    'status' => $validated['completion_status'] === 'complete' ? 'issued' : 'draft',
                    'issued_at' => $validated['completion_status'] === 'complete' ? now() : null,
                    'notes' => $validated['prescription_notes'] ?? null,
                ]);
                $prescription->items()->delete();
                foreach ($prescriptionItems as $index => $item) {
                    $prescription->items()->create($item + ['sort_order' => $index]);
                }
            } else {
                Prescription::where('visit_id', $visit->id)->delete();
            }

            if (!empty($validated['follow_up_date'])) {
                $patientUserId = gnr_m_patients::whereKey($visit->patient)->value('user_id');
                Appointment::updateOrCreate([
                    'appointment_for' => $patientUserId,
                    'appointment_with' => $doctorId,
                    'appointment_date' => $validated['follow_up_date'],
                    'time' => $validated['follow_up_time'].':00',
                ], ['status' => AppointmentStatus::Pending->value, 'is_deleted' => 0]);
            }

            DB::table('cln_x_visits')->where('id', $visit->id)->update([
                'status' => $validated['completion_status'] === 'complete' ? VisitStatus::Completed->value : VisitStatus::Draft->value,
                'doctor_id' => $doctorId,
                'completed_at' => $validated['completion_status'] === 'complete' ? now() : null,
                'updated_at' => now(),
            ]);
        });

        if ($validated['completion_status'] === 'complete') {
            app(InvoiceService::class)->syncFromVisit($visit->fresh(), auth()->id());
        }

        $message = $validated['completion_status'] === 'complete'
            ? 'تم حفظ المعاينة وإنهاؤها بنجاح.'
            : 'تم حفظ مسودة المعاينة.';

        if ($request->expectsJson()) {
            return response()->json(['message' => $message, 'saved_at' => now()->toIso8601String()]);
        }

        return redirect()->route('consultations.edit', $visit)->with('success', $message);
    }

    public function reopenConsultation(Request $request, cln_x_visits $visit)
    {
        $this->authorize('writeMedicalFile', $visit);
        abort_unless((int) $visit->status === VisitStatus::Completed->value, 422, 'المعاينة ليست مكتملة.');
        $validated = $request->validate(['reason' => ['required', 'string', 'min:5', 'max:2000']]);

        DB::transaction(function () use ($visit, $validated) {
            DB::table('visit_reopen_logs')->insert([
                'visit_id' => $visit->id,
                'reopened_by' => auth()->id(),
                'reason' => $validated['reason'],
                'created_at' => now(),
            ]);
            DB::table('cln_x_visits')->where('id', $visit->id)->update([
                'status' => VisitStatus::Draft->value,
                'completed_at' => null,
                'updated_at' => now(),
            ]);
            Prescription::where('visit_id', $visit->id)->update(['status' => 'draft', 'issued_at' => null]);
        });

        return back()->with('success', 'تمت إعادة فتح المعاينة وتسجيل السبب.');
    }

    private function currentDoctorFor(Appointment $appointment)
    {
        $user = auth()->user();
        abort_unless($user?->hasSystemRole('doctor') && $user->doctor, 403);
        abort_unless(in_array((int) $appointment->appointment_with, [(int) $user->id, (int) $user->doctor->id], true), 403);

        return $user->doctor;
    }

    private function consultationData(cln_x_visits $visit): array
    {
        $doctorId = (int) auth()->user()->doctor->id;
        $text = fn (string $table) => (string) DB::table($table)
            ->where('visit', $visit->id)->where('doc', $doctorId)->value('val');

        return [
            'visit' => $visit->load('gnr_m_clinics'),
            'patientProfile' => gnr_m_patients::findOrFail($visit->patient),
            'chiefComplaint' => $text('cln_x_prev_com'),
            'history' => $text('cln_x_prev_str'),
            'clinicalExam' => $text('cln_x_prev_cln'),
            'doctorNotes' => $text('cln_x_prev_not'),
            'selectedDiagnosisRows' => cln_m_icd10::query()->whereIn('id', DB::table('cln_x_prev_icd10')->where('visit', $visit->id)->where('doc', $doctorId)->pluck('opr_id'))->get(['id', 'name_ar', 'code']),
            'services' => cln_m_services::query()->where('clinic', $visit->clinic)->orderBy('name_ar')->get(['id', 'name_ar', 'price']),
            'selectedServices' => DB::table('cln_x_visits_services')->where('visit_id', $visit->id)->pluck('service')->map(fn ($id) => (int) $id)->all(),
            'medicalHistoryCategories' => cln_m_medical_his_cats::query()->orderBy('ord')->get(['id', 'name_ar']),
            'selectedMedicalHistory' => cln_m_medical_his::query()
                ->whereIn('id', DB::table('cln_x_medical_his')->where('patient', $visit->patient)->pluck('med_id'))
                ->get(['id', 'cat', 'name_ar'])->groupBy('cat'),
            'medicalHistoryNotes' => DB::table('cln_x_medical_his')->where('patient', $visit->patient)
                ->selectRaw('cat, MAX(note) note')->groupBy('cat')->pluck('note', 'cat'),
            'vitals' => VisitVital::where('visit_id', $visit->id)->first(),
            'prescription' => Prescription::with('items')->where('visit_id', $visit->id)->first(),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        abort_unless($user->hasSystemRole('super_admin', 'secretary', 'doctor'), 403);

        $query = DB::table('cln_x_visits as visits')
            ->join('gnr_m_patients as patients', 'patients.id', '=', 'visits.patient')
            ->leftJoin('gnr_m_clinics as clinics', 'clinics.id', '=', 'visits.clinic')
            ->select([
                'visits.id', 'visits.patient', 'visits.clinic', 'visits.d_start', 'visits.status',
                'patients.f_name', 'patients.l_name', 'clinics.name_ar as clinic_name',
            ]);

        if ($user->hasSystemRole('doctor')) {
            $doctorId = (int) $user->doctor?->id;
            abort_if($doctorId <= 0, 403, 'لا يوجد ملف طبيب مرتبط بهذا الحساب.');
            $doctorIdentifiers = array_values(array_unique(array_filter([$doctorId, (int) $user->id])));
            $query->whereExists(function ($appointments) use ($doctorIdentifiers) {
                $appointments->selectRaw('1')
                    ->from('appointments')
                    ->whereColumn('appointments.appointment_for', 'patients.user_id')
                    ->whereIn('appointments.appointment_with', $doctorIdentifiers)
                    ->where('appointments.is_deleted', 0);
            });
        }

        if ($search = trim((string) request('search'))) {
            $query->where(function ($builder) use ($search) {
                $builder->where('patients.f_name', 'like', "%{$search}%")
                    ->orWhere('patients.l_name', 'like', "%{$search}%")
                    ->orWhere('visits.id', $search);
            });
        }

        $visits = $query->distinct()->orderByDesc('visits.d_start')->paginate(20)->withQueryString();

        return view('back.medical-files.index', compact('visits'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $request->validate([
            'patient' => ['required', 'integer', 'exists:gnr_m_patients,id'],
            'visit' => ['required', 'integer', 'exists:cln_x_visits,id'],
            'clinic' => ['required', 'integer', 'exists:gnr_m_clinics,id'],
        ]);
        $visitModel = cln_x_visits::findOrFail($request->integer('visit'));
        abort_unless((int) $visitModel->patient === $request->integer('patient'), 422, 'بيانات المريض لا تطابق الزيارة.');
        $this->authorize('writeMedicalFile', $visitModel);
        //var
        $patient = $request->patient;
        $visit = $request->visit;
        $clinic = $request->clinic;

        //relation
        $cln_m_medical_his_cats = cln_m_medical_his_cats::all();
        $patientinfo = gnr_m_patients::find($patient);
        $patientM = $patientinfo->cln_m_medical_his;
        $patientInfoExiste = $patientinfo->gnr_m_patients_medical_info;


        /*$icd10_1 = cln_m_icd10::where('cat','=','1')->get();
        $icd10_2 = cln_m_icd10::where('cat','=','2')->get();
        $icd10_3 = cln_m_icd10::where('cat','=','3')->get();
        $icd10_4 = cln_m_icd10::where('cat','=','4')->get();
        $icd10_5 = cln_m_icd10::where('cat','=','5')->get();
        $icd10_6 = cln_m_icd10::where('cat','=','6')->get();
        $icd10_7 = cln_m_icd10::where('cat','=','7')->get();
        $icd10_8 = cln_m_icd10::where('cat','=','8')->get();
        $icd10_9 = cln_m_icd10::where('cat','=','9')->get();
        $icd10_10 = cln_m_icd10::where('cat','=','10')->get();
        $icd10_11 = cln_m_icd10::where('cat','=','11')->get();
        $icd10_12 = cln_m_icd10::where('cat','=','12')->get();
        $icd10_13 = cln_m_icd10::where('cat','=','13')->get();
        $icd10_14 = cln_m_icd10::where('cat','=','14')->get();
        $icd10_15 = cln_m_icd10::where('cat','=','15')->get();
        $icd10_16 = cln_m_icd10::where('cat','=','16')->get();
        $icd10_17 = cln_m_icd10::where('cat','=','17')->get();
        $icd10_18 = cln_m_icd10::where('cat','=','18')->get();
        $icd10_19 = cln_m_icd10::where('cat','=','19')->get();
        $icd10_20 = cln_m_icd10::where('cat','=','20')->get();
        $icd10_21 = cln_m_icd10::where('cat','=','21')->get();
        $icd10_22 = cln_m_icd10::where('cat','=','22')->get();*/

        $services = cln_m_services::where('clinic', '=', $clinic)->get();
        $medicalH = cln_m_medical_his::select('id', 'cat', 'name_ar')->get();
        return view('back.services.create', compact(
            'cln_m_medical_his_cats', 'patientInfoExiste', 'patientinfo', 'patientM'
            , 'visit', 'patient', 'clinic', 'services', 'medicalH'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'patient' => ['required', 'integer', 'exists:gnr_m_patients,id'],
            'visit' => ['required', 'integer', 'exists:cln_x_visits,id'],
            'clinic' => ['required', 'integer', 'exists:gnr_m_clinics,id'],
            'height' => ['nullable', 'numeric', 'min:20', 'max:300'],
            'birth_date' => ['nullable', 'date'],
            'sex' => ['nullable', 'integer', 'in:1,2'],
            'services.*' => ['integer', 'exists:cln_m_services,id'],
            'icd10SelectedID.*' => ['integer', 'exists:cln_m_icd10,id'],
        ]);
        $visitModel = cln_x_visits::findOrFail($request->integer('visit'));
        abort_unless((int) $visitModel->patient === $request->integer('patient'), 422, 'بيانات المريض لا تطابق الزيارة.');
        abort_unless((int) $visitModel->clinic === $request->integer('clinic'), 422, 'بيانات العيادة لا تطابق الزيارة.');
        $this->authorize('writeMedicalFile', $visitModel);
        //dd($request);

        $user = User::find(Auth::id());
        $doctor = $user->doctor;

        /// cln_M_Medical_his
        if (!empty($request->med1)) {
            foreach ($request->med1 as $med1) {
                if (DB::table('cln_x_medical_his')->where('patient', '=', $request->patient)->where('med_id', '=', $med1)->count() == 0) {
                    DB::insert('insert into cln_x_medical_his (cat ,med_id,patient,note) values (?,?,?,?)', [1, $med1, $request->patient, $request->note1]);
                }
            }
        }
        if (!empty($request->med2)) {
            foreach ($request->med2 as $med2) {
                if (DB::table('cln_x_medical_his')->where('patient', '=', $request->patient)->where('med_id', '=', $med2)->count() == 0) {
                    DB::insert('
insert into cln_x_medical_his (cat ,med_id,patient,note)
 values (?,?,?,?)', [2, $med2, $request->patient, $request->note2]);
                }
            }
        }
        if (!empty($request->med3)) {
            foreach ($request->med3 as $med3) {
                if (DB::table('cln_x_medical_his')->where('patient', '=', $request->patient)->where('med_id', '=', $med3)->count() == 0) {
                    DB::insert(' insert into cln_x_medical_his (cat ,med_id,patient,note) values (?,?,?,?)', [3, $med3, $request->patient, $request->note3]);
                }
            }
        }
        if (!empty($request->med4)) {
            foreach ($request->med4 as $med4) {
                if (DB::table('cln_x_medical_his')->where('patient', '=', $request->patient)->where('med_id', '=', $med4)->count() == 0) {
                    DB::insert(' insert into cln_x_medical_his (cat ,med_id,patient,note) values (?,?,?,?)', [4, $med4, $request->patient, $request->note4]);
                }
            }
        }
        if (!empty($request->med5)) {
            foreach ($request->med5 as $med5) {
                if (DB::table('cln_x_medical_his')->where('patient', '=', $request->patient)->where('med_id', '=', $med5)->count() == 0) {
                    DB::insert(' insert into cln_x_medical_his (cat ,med_id,patient,note) values (?,?,?,?)', [5, $med5, $request->patient, $request->note5]);
                }
            }
        }

        //cln_x_prev_com
        if ($request->com != null) {
            foreach ($request->com as $com1) {
                if (DB::table('cln_x_prev_com')->where('visit', '=', $request->visit)->where('val', '=', $com1)->count() == 0 && $com1 != null) {
                    DB::insert('insert into cln_x_prev_com (visit ,patient,doc,val) values (?,?,?,?)', [$request->visit, $request->patient, $doctor->id, $com1]);
                }
            }
        }
        // Cln_X_prev_str
        if ($request->str != null) {
            foreach ($request->str as $com1) {
                if (DB::table('cln_x_prev_str')->where('visit', '=', $request->visit)->where('val', '=', $com1)->count() == 0 && $com1 != null) {
                    DB::insert('insert into cln_x_prev_str (visit ,patient,doc,val) values (?,?,?,?)', [$request->visit, $request->patient, $doctor->id, $com1]);
                }
            }
        }
        //cln_x_prev_cln
        if ($request->cln != null) {
            foreach ($request->cln as $com1) {
                if (DB::table('cln_x_prev_cln')->where('visit', '=', $request->visit)->where('val', '=', $com1)->count() == 0 && $com1 != null) {
                    DB::insert('insert into cln_x_prev_cln (visit ,patient,doc,val) values (?,?,?,?)', [$request->visit, $request->patient, $doctor->id, $com1]);
                }
            }
        }
        //cln_x_prev_icd10
        if ($request->icd10SelectedID != null) {
            foreach ($request->icd10SelectedID as $com1) {
                if (DB::table('cln_x_prev_icd10')->where('visit', '=', $request->visit)->where('opr_id', '=', $com1)->count() == 0) {
                    DB::insert('insert into cln_x_prev_icd10 (visit ,patient,opr_id,doc) values (?,?,?,?)', [$request->visit, $request->patient, $com1, $doctor->id]);
                }
            }
        }
        //cln_x_prev_note
        if ($request->note != null) {
            foreach ($request->note as $com1) {
                if (DB::table('cln_x_prev_not')->where('visit', '=', $request->visit)->where('val', '=', $com1)->count() == 0 && $com1 != null) {
                    DB::insert('insert into cln_x_prev_not (visit ,patient,doc,val) values (?,?,?,?)', [$request->visit, $request->patient, $doctor->id, $com1]);
                }
            }
        }
        //patient info
        if ($request->height !== null) {
            if (DB::table('gnr_m_patients_medical_info')->where('patient', '=', $request->patient)->count() == 0) {
                DB::insert('insert into gnr_m_patients_medical_info (patient,birth_date,sex,height,father_height,mother_height) values (?,?,?,?,?,?)',
                    [$request->patient, $request->birth_date, $request->sex, $request->height, $request->father_height, $request->mother_height]);
            }
        }
        //services
        if ($request->services != null) {
            DB::table('cln_x_visits_services')->where('visit_id', '=', $request->visit)->delete();
            foreach ($request->services as $serv) {
                DB::insert('insert into cln_x_visits_services (visit_id ,clinic,service,status,patient ,d_start ,srv_type)
                                          values (?, ?,?,?,?,?,?)', [$request->visit, $request->clinic, $serv, "0", $request->patient, '0', '0']);

            }
        }
        return redirect()->route('services.show', $request->visit);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
