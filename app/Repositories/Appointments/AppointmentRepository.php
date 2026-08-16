<?php

namespace App\Repositories\Appointments;

use App\Models\back\Appointment;
use App\Models\back\DoctorAvailableDay;
use App\Models\back\DoctorAvailableSlot;
use App\Models\back\doctors;
use App\Models\User;
use App\Traits\ResponseTrait;
use App\Traits\UploadFileTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AppointmentRepository implements IAppointmentRepository
{
    use UploadFileTrait;
    use ResponseTrait;

    public $Appointment;

    public function __construct(Appointment $appointment)
    {
        $this->Appointment = $appointment;
    }

    public function index(){
        $request = request();
        $user = auth()->user();
        $query = Appointment::query()->with('doctor','patient','timeSlot')
            ->where('is_deleted', 0)
            ->orderBy('id', 'DESC');

        if ($d_name = $request->query('d_name')) {
            $query->whereHas('doctor', function ($doctorQuery) use ($d_name) {
                $doctorQuery->where('name_ar', 'LIKE', "%{$d_name}%");
            });
        }
        if ($p_name = $request->query('p_name')) {
            $query->whereHas('patient', function ($patientQuery) use ($p_name) {
                $patientQuery->where('name', 'LIKE', "%{$p_name}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', (int) $request->query('status'));
        }

        if ($request->filled('date')) {
            $query->whereDate('appointment_date', $request->query('date'));
        }

        if ($request->query('period') === 'today') {
            $query->whereDate('appointment_date', Carbon::today());
        } elseif ($request->query('period') === 'upcoming') {
            $query->whereDate('appointment_date', '>', Carbon::today());
        }

        if ($user->hasSystemRole('doctor')) {
            $doctorId = optional($user->doctor)->id;
            $query->whereIn('appointment_with', array_filter([$user->id, $doctorId]));
        }

        return $query->get();
    }

    public function patient_appoi($id){
        return Appointment::with('doctor','timeSlot')
            ->where('appointment_for',$id)
            ->where('is_deleted',0)
            ->orderBy('id', 'DESC')->get();
    }

   
   public function pat_appoints()
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }
        $user_id = $user->id;
        
        $today = Carbon::today()->format('Y-m-d');
        
        // جلب المواعيد الخاصة بالمريض حصراً
        $appointments = Appointment::with('timeSlot')
            ->where('appointment_for', $user_id)
            ->whereDate('appointment_date', '>=', $today)
            ->where('is_deleted', 0)
            ->whereIn('status', [0, 1]) 
            ->orderBy('id', 'DESC')
            ->get();

        // 🔴 الحقن المباشر والدقيق لاسم الطبيب الحقيقي من جدول doctors بناءً على appointment_with
        foreach ($appointments as $appointment) {
            $doctorRecord = DB::table('doctors')->where('id', $appointment->appointment_with)->first();
            
            // حقن اسم الطبيب الصحيح مباشرة في كائن الموعد
            $appointment->doctor_name_override = $doctorRecord ? $doctorRecord->name_ar : 'طبيب غير محدد';
        }

        return $appointments;
    }
   


    public function doc_appoints()
{
    $user = auth()->user();
    
    // استخدام اسم الموديل الصحيح الذي اكتشفناه
    $doctor = \App\Models\back\doctors::where('user_id', $user->id)->first();
    if (!$doctor) {
        return [];
    }
    
    // جلب كل المواعيد الخاصة بالطبيب، وترتيبها من الأقرب للأبعد
    $appointments = Appointment::with('patient', 'timeSlot')
        ->where('appointment_with', $doctor->id)
        ->where('is_deleted', 0)
        ->orderBy('appointment_date', 'ASC')
        ->orderBy('time', 'ASC')
        ->get();
        
    return $appointments;
}

   
    
    
    
  public function doc_today_appoints()
{
    $user = auth()->user();
    
    // 1. جلب الطبيب المرتبط بهذا المستخدم
    $doctor = \App\Models\back\doctors::where('user_id', $user->id)->first();
    
    // 2. طباعة للتأكد من المعرفات
    \Log::info("DEBUG: User ID: " . $user->id . " | Doctor ID found: " . ($doctor ? $doctor->id : 'NOT FOUND'));

    if (!$doctor) {
        return [];
    }
    
    // 3. جلب المواعيد باستخدام ID الطبيب الذي وجدناه
    $appointments = Appointment::with('patient', 'timeSlot')
        ->where('appointment_with', $doctor->id) // هذا يجب أن يكون هو الرقم 7
        ->where('is_deleted', 0)
        ->get();

    \Log::info("DEBUG: Appointments count for Doctor ID " . $doctor->id . " is: " . $appointments->count());

    return $appointments;
}
   
   
    public function pat_canceled_appoints()
    {
        $user = auth()->user();
        $user_id = $user->id;
        return Appointment::with('doctor', 'timeSlot')
            ->where('appointment_for', $user_id)
            ->where('status', 2)
            ->where('is_deleted',0)
            ->orderBy('id', 'DESC')->get();
    }

    public function pat_previos_appoints()
    {
        $user = auth()->user();
        $user_id = $user->id;
        $today = Carbon::today()->format('Y/m/d');
        return Appointment::with('doctor', 'timeSlot')
            ->where('appointment_for', $user_id)
            ->whereDate('appointment_date', '<', $today)
            ->where('is_deleted',0)
            ->orderBy('id', 'DESC')->get();
    }

    public function show($appointment)
    {
        return $this->Appointment::with('patient', 'doctor');
    }

    public function doctor_available_days($doc)
    {
        $doctor = doctors::with('available_days')->find($doc);
        $days = DoctorAvailableDay::where('doctor_id', $doctor->id)->first();
        return $days;
    }

    public function slots($doc, $dates)
    {

        $appointment_slot = DoctorAvailableSlot::with(['appointment' => function ($re) use ($dates) {
            $re->where('appointment_date', $dates);
        }])->where('doctor_id', $doc)->get();
        $slots[] = null;
        $i = 0;
        foreach ($appointment_slot as $slot) {
            if ($slot->appointment->count() == 0) {
                $slots[$i] = $slot;
                $i++;
            }
        }
        return $slots;
    }

    public function update($input, Appointment $appointment)
    {
        $date = Carbon::parse($input['appointment_date'])->format('Y-m-d');

        return DB::transaction(function () use ($input, $appointment, $date) {
            $appointment->appointment_for = $input['appointment_for'] ?? $appointment->appointment_for;
            $appointment->appointment_with = $input['appointment_with'] ?? $appointment->appointment_with;
            $appointment->appointment_date = $date;
            $appointment->available_slot = $input['available_slot'] ?? $appointment->available_slot;
            $appointment->time = $input['time'] ?? $input['available_slot'] ?? $appointment->time;
            $appointment->save();

            return $appointment->fresh(['patient', 'doctor', 'timeSlot']);
        });
    }

   


    public function store($input)
    {
        $user = auth()->user();
        try {
            $date = $input['appointment_date'];
            $newDate = \Carbon\Carbon::parse($date)->format('Y-m-d');
            
            $time = isset($input['time']) ? $input['time'] : $input['available_slot'];
            if (strlen($time) == 5) {
                $time .= ':00'; 
            }

            // 🔴 الاعتماد المباشر والصريح على الـ ID القادم من التطبيق دون أي بحث يغيره!
            $doctorId = $input['appointment_with'];

            if ($user->hasSystemRole('patient')) {
                $apointment = Appointment::create([
                    'appointment_for' => $user->id,
                    'appointment_with' => $doctorId, // حفظ الـ ID المباشر كما أرسله التطبيق تماماً
                    'appointment_date' => $newDate,
                    'time' => $time, 
                ]);
            } elseif ($user->hasSystemRole('doctor')) {
                $doctorRecord = \App\Models\back\doctors::where('user_id', $user->id)->first();
                $docRealId = $doctorRecord ? $doctorRecord->id : $user->id;
                
                $apointment = Appointment::create([
                    'appointment_for' => $input['appointment_for'],
                    'appointment_with' => $docRealId,
                    'appointment_date' => $newDate,
                    'time' => $time,
                ]);
            } elseif ($user->hasSystemRole('secretary')) {
                $apointment = Appointment::create([
                    'appointment_for' => $input['appointment_for'],
                    'appointment_with' => $doctorId,
                    'appointment_date' => $newDate,
                    'time' => $time,
                ]);
            }
            return $apointment;
            
        } catch (\Exception $ex) {
            \Log::error("فشل حفظ الموعد: " . $ex->getMessage());
            throw $ex; 
        }
    }
    public function destroy($appointment)
    {
        $appoint = Appointment::find($appointment);
        $appoint->is_deleted = 1;
        $appoint->save();
    }

    public function cancel_appoint($appointment)
    {
        $appoint = Appointment::find($appointment);
        if (!$appoint) {
            return null;
        }
        $appoint->status = 2;
        $appoint->save();
        return $appoint;
    }
// 29 الشهر



// public function accept_appoint($appointmentId)
// {
//     $appoint = Appointment::find($appointmentId);
//     if (!$appoint) {
//         return null;
//     }

//     $appoint->status = 1; 
//     $appoint->save();

//     $patientProfile = \App\Models\back\gnr_m_patients::where('user_id', $appoint->appointment_for)->first();
//     $realPatientId = $patientProfile ? $patientProfile->id : $appoint->appointment_for;

//     $visit = new \App\Models\back\cln_x_visits();
//     $visit->timestamps = false; 
//     $visit->patient = $realPatientId; 
//     $visit->clinic = $appoint->clinic_id ?? 1; 
    
//     // 🔴 الحل السحري هنا: تحويل التاريخ والوقت إلى Timestamp
//     $time = $appoint->time ?? $appoint->available_slot ?? '00:00:00';
//     $dateTimeString = $appoint->appointment_date . ' ' . $time;
//     $visit->d_start = strtotime($dateTimeString); 
    
//     $visit->status = 0; 
//     $visit->type = 1; 

//     $visit->save(); 

//     return $appoint;
// }
public function accept_appoint($appointmentId)
{
    return DB::transaction(function () use ($appointmentId) {
        $appoint = Appointment::query()->lockForUpdate()->find($appointmentId);
        if (!$appoint) {
            return null;
        }

        $patientProfile = \App\Models\back\gnr_m_patients::where('user_id', $appoint->appointment_for)->firstOrFail();
        $doctor = \App\Models\back\doctors::query()
            ->whereKey($appoint->appointment_with)
            ->orWhere('user_id', $appoint->appointment_with)
            ->firstOrFail();
        $clinicId = (int) $doctor->subgrp;
        if ($clinicId <= 0) {
            throw new \RuntimeException('لا توجد عيادة مرتبطة بالطبيب.');
        }

        $visitTimestamp = strtotime($appoint->appointment_date.' '.($appoint->time ?: '00:00:00'));
        $visit = \App\Models\back\cln_x_visits::query()
            ->where('patient', $patientProfile->id)
            ->where('d_start', $visitTimestamp)
            ->lockForUpdate()
            ->first();
        if (!$visit) {
            $visit = new \App\Models\back\cln_x_visits();
            $visit->timestamps = false;
            $visit->patient = $patientProfile->id;
            $visit->clinic = $clinicId;
            $visit->type = 1;
            $visit->status = 0;
            $visit->d_start = $visitTimestamp;
            $visit->save();
        }

        $appoint->status = 1;
        $appoint->save();

        return $appoint;
    });
}
}
