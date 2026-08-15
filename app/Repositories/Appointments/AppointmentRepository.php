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

    // public function pat_appoints()
    // {
    //     $user = auth()->user();
    //     if (!$user) {
    //     return response()->json(['error' => 'Unauthenticated'], 401);
    // }
    //     $user_id = $user->id;
    //     \Log::info("جاري جلب مواعيد المستخدم ID: " . $user_id);
    //     $today = Carbon::today()->format('Y-m-d');
    //     return Appointment::with('doctor', 'timeSlot')
    //         ->where('appointment_for', $user_id)
    //         // ->whereDate('appointment_date', '>=', $today)
            
    //         ->where('is_deleted', 0)
    //         ->orderBy('id', 'DESC')->get();
            
    //         // \Log::info("عدد المواعيد التي تم العثور عليها: " . $results->count());
    // }
    public function pat_appoints()
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }
        $user_id = $user->id;
        
        $today = Carbon::today()->format('Y-m-d');
        
        return Appointment::with('doctor', 'timeSlot')
            ->where('appointment_for', $user_id)
            ->whereDate('appointment_date', '>=', $today)
            ->where('is_deleted', 0)
            // 🔴 التعديل هنا: جلب المواعيد المعلقة (0) والمؤكدة (1) فقط!
            ->whereIn('status', [0, 1]) 
            ->orderBy('id', 'DESC')
            ->get();
    }
//    public function pat_appoints()
//     {
//         $user = auth()->user();
//         if (!$user) {
//             return response()->json(['error' => 'Unauthenticated'], 401);
//         }
//         $user_id = $user->id;
        
//         $today = Carbon::today()->format('Y-m-d');
        
//         return Appointment::with('doctor', 'timeSlot')
//             ->where('appointment_for', $user_id)
//             ->whereDate('appointment_date', '>=', $today) // جلب المواعيد القادمة وتاريخ اليوم
//             ->where('is_deleted', 0)
//             ->orderBy('id', 'DESC')
//             ->get();
//     }
    // الشغال لحد28 الشهر
    // public function pat_appoints()
    // {
    //     $user = auth()->user();
    //     if (!$user) {
    //         return response()->json(['error' => 'Unauthenticated'], 401);
    //     }
    //     $user_id = $user->id;
    //     \Log::info("جاري جلب مواعيد المستخدم ID: " . $user_id);
        
    //     $today = Carbon::today()->format('Y-m-d');
        
    //     // 1. جلب المواعيد وحفظها في متغير أولاً
    //     $appointments = Appointment::with('doctor', 'timeSlot')
    //         ->where('appointment_for', $user_id)
    //         // ->whereDate('appointment_date', '>=', $today)
    //         ->where('is_deleted', 0)
    //         ->orderBy('id', 'DESC')
    //         ->get();

    //     // 2. فحص اللوقات قبل الخروج من الدالة
    //     foreach($appointments as $app) {
    //         \Log::info("Appointment ID: {$app->id} | Doctor ID in appointment: {$app->appointment_with} | Doctor Name Found: " . optional($app->doctor)->name_ar);
    //     }

    //     \Log::info("عدد المواعيد التي تم العثور عليها: " . $appointments->count());

    //     // 3. إرجاع النتائج نهائياً
    //     return $appointments;
    // }
//old run
    // public function doc_appoints()
    // {
    //     $user = auth()->user();
    //     $user_id = $user->id;
    //     $today = Carbon::today()->format('Y/m/d');
    //     $doctor = doctors::where('user_id', $user_id)->first();
    //     $doc_id = $doctor->id;
    //     return Appointment::with('patient', 'timeSlot')
    //         ->where('appointment_with', $user_id)
    //         ->whereDate('appointment_date', '>', $today)
    //         ->where('is_deleted', 0)
    //         ->orderBy('id', 'DESC')->get();
    // }

    // public function doc_today_appoints()
    // {
    //     $user = auth()->user();
    //     $user_id = $user->id;
    //     $today = Carbon::today()->format('Y/m/d');
    //     $doctor = doctors::where('user_id', $user_id)->first();
    //     $doc_id = $doctor->id;
    //     return Appointment::with('patient', 'timeSlot')
    //         ->where('appointment_with', $user_id)
    //         ->whereDate('appointment_date', '=', $today)
    //         ->where('is_deleted', 0)
    //         ->orderBy('id', 'DESC')->get();
    // }
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
// شغالة 30
    // public function doc_appoints()
    // {
    //     $user = auth()->user();
        
    //     // 1. جلب بيانات الطبيب وحماية الكود من الانهيار إذا كان فارغاً
    //     $doctor = \App\Models\back\doctors::where('user_id', $user->id)->first();
    //     if (!$doctor) {
    //         return []; // إرجاع مصفوفة فارغة بدلاً من الخطأ 500
    //     }

    //     // 2. توحيد صيغة التاريخ
    //     $today = \Carbon\Carbon::today()->format('Y-m-d');
        
    //     return Appointment::with('patient', 'timeSlot')
    //         // 3. البحث باستخدام $doctor->id (الذي صلحناه سابقاً) بدلاً من user_id
    //         ->where('appointment_with', $doctor->id)
    //         ->whereDate('appointment_date', '>', $today)
    //         ->where('is_deleted', 0)
    //         ->orderBy('id', 'DESC')->get();
    // }
    public function doc_today_appoints()
    {
        $user = auth()->user();
        
        $doctor = \App\Models\back\doctors::where('user_id', $user->id)->first();
        if (!$doctor) {
            \Log::error("DEBUG: الحساب رقم " . $user->id . " غير مربوط بأي طبيب في الداتابيز!");
            return [];
        }

        $today = \Carbon\Carbon::today()->format('Y-m-d');
        
        $appointments = Appointment::with('patient', 'timeSlot')
            ->where('appointment_with', $doctor->id)
            ->whereDate('appointment_date', '=', $today)
            ->where('is_deleted', 0)
            ->orderBy('id', 'DESC')->get();
            
        // 🔴 هذا السطر سيكشف لنا السر!
        \Log::info("DEBUG: الطبيب رقم: " . $doctor->id . " | يبحث عن مواعيد بتاريخ: " . $today . " | النتيجة: وجد " . $appointments->count() . " مواعيد");

        return $appointments;
    }
    
// new
    // public function doc_today_appoints()
    // {
    //     $user = auth()->user();
        
    //     $doctor = \App\Models\back\doctors::where('user_id', $user->id)->first();
    //     if (!$doctor) {
    //         return [];
    //     }

    //     $today = \Carbon\Carbon::today()->format('Y-m-d');
        
    //     return Appointment::with('patient', 'timeSlot')
    //         ->where('appointment_with', $doctor->id)
    //         ->whereDate('appointment_date', '=', $today) // '=' تعني مواعيد اليوم فقط
    //         ->where('is_deleted', 0)
    //         ->orderBy('id', 'DESC')->get();
    // }
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

    // public function store($input)
    // {
    //     $user = auth()->user();
    //     try {
    //         $date = $input['appointment_date'];
    //         $newDate = Carbon::createFromFormat('m/d/Y', $date)->format('Y-m-d');
    //         if ($user->roles_name == 'Patient') {
    //             $id_doc = $input['appointment_with'];
    //             $doctor = doctors::find($id_doc);
    //             $apointment = Appointment::create([
    //                 'appointment_for' => $user->id,
    //                 'appointment_with' => $doctor->user_id,
    //                 'appointment_date' => $newDate,
    //                 'available_slot' => $input['available_slot'],
    //             ]);
    //         } elseif ($user->roles_name == 'Doctor') {
    //             $apointment = Appointment::create([
    //                 'appointment_for' => $input['appointment_for'],
    //                 'appointment_with' => $user->id,
    //                 'appointment_date' => $newDate,
    //                 'available_slot' => $input['available_slot'],
    //             ]);
    //         } elseif ($user->roles_name == 'Reception') {
    //             $id_doc = $input['appointment_with'];
    //             $doctor = doctors::find($id_doc);
    //             $apointment = Appointment::create([
    //                 'appointment_for' => $input['appointment_for'],
    //                 'appointment_with' => $doctor->user_id,
    //                 'appointment_date' => $newDate,
    //                 'available_slot' => $input['available_slot'],
    //             ]);
    //         }
    //         return $apointment;
    //     } catch (\Exception $ex) {
    //         return $ex;
    //     }
    // }

    public function store($input)
    {
        $user = auth()->user();
        try {
            $date = $input['appointment_date'];
            
            // تحويل التاريخ بأمان
            $newDate = \Carbon\Carbon::parse($date)->format('Y-m-d');
            
            // تجهيز الوقت بصيغة H:i:s
            $time = isset($input['time']) ? $input['time'] : $input['available_slot'];
            if (strlen($time) == 5) {
                $time .= ':00'; 
            }

            $id_doc = $input['appointment_with'];

            // 🔴 التعديل هنا: قمنا بحذف سطر 'available_slot' من أوامر الحفظ 
            // لأن الداتابيز لديك ترفضه وتطلب عمود 'time' فقط
            
            if ($user->hasSystemRole('patient')) {
                $apointment = Appointment::create([
                    'appointment_for' => $user->id,
                    'appointment_with' => $id_doc,
                    'appointment_date' => $newDate,
                    'time' => $time, 
                ]);
            } elseif ($user->hasSystemRole('doctor')) {
                $apointment = Appointment::create([
                    'appointment_for' => $input['appointment_for'],
                    'appointment_with' => $user->id,
                    'appointment_date' => $newDate,
                    'time' => $time,
                ]);
            } elseif ($user->hasSystemRole('secretary')) {
                $apointment = Appointment::create([
                    'appointment_for' => $input['appointment_for'],
                    'appointment_with' => $id_doc,
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
    // 1. جلب الموعد (هنا لارافل سيتعرف على الموديل تلقائياً)
    $appoint = Appointment::find($appointmentId);
    if (!$appoint) {
        return null;
    }

    if ((int) $appoint->status === 1) {
        return $appoint;
    }

    $appoint->status = 1;
    $appoint->save();

    // 2. جلب رقم المريض الحقيقي
    $patientProfile = \App\Models\back\gnr_m_patients::where('user_id', $appoint->appointment_for)->first();
    $realPatientId = $patientProfile ? $patientProfile->id : $appoint->appointment_for;

    // 3. جلب بيانات الطبيب لمعرفة العيادة
    $doctor = \App\Models\back\doctors::find($appoint->appointment_with);
    
    // 4. تحديد العيادة (رقم عيادة الطبيب الحقيقية)
    $clinicId = $appoint->clinic_id ?? ($doctor->clinic ?? ($doctor->clinic_id ?? 1));

    // 5. إنشاء الزيارة
    $visit = new \App\Models\back\cln_x_visits();
    $visit->timestamps = false; 
    $visit->patient = $realPatientId; 
    $visit->clinic = $clinicId; // 👈 هنا سيتم حفظ العيادة الحقيقية
    
    $time = $appoint->time ?? $appoint->available_slot ?? '00:00:00';
    $dateTimeString = $appoint->appointment_date . ' ' . $time;
    $visit->d_start = strtotime($dateTimeString); 
    
    $visit->status = 0; 
    $visit->type = 1; 
    $visit->save(); 

    return $appoint;
}
}
