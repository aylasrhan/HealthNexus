<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\back\Appointment;
use App\Models\back\doctors;
use App\Repositories\Appointments\IAppointmentRepository;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class ApiAppointmentController extends Controller
{
    use ResponseTrait;

    public $appointment;

    public function __construct(IAppointmentRepository $appointmentRepository)
    {
        $this->AppointmentRepository = $appointmentRepository;
    }

    public function doctor_available_days(Request $request): JsonResponse
    {
        $doc = $request->doctor;
        $doctor = doctors::find($doc);
        if (!$doctor) {
            return $this->returnError("D00", "Doctor not found");
        } else {
            $available_days = $this->AppointmentRepository->doctor_available_days($doc);
            return $this->returnData("available_days", $available_days);
        }
    }

    public function slots_by_day(Request $request): JsonResponse
    {

        $doc = $request->doctor;
        $doctor = doctors::find($doc);
        if (!$doctor) {
            return $this->returnError("D00", "Doctor not found");
        } else {
            $date = $request->date;
            $dates = Carbon::createFromFormat('m/d/Y', $date)->format('Y-m-d');
            $slots = $this->AppointmentRepository->slots($doc, $dates);
            if (!$slots) {
                return $this->returnError("D00", "Slots not found");
            } else {
                return $this->returnData("slots", $slots);
            }
        }
    }

public function get_booked_times(Request $request): JsonResponse
{
    $doctorId = $request->doctor_id;
    $date = $request->date;

    // جلب المواعيد المؤكدة للطبيب في هذا اليوم
    $appointments = \App\Models\back\Appointment::where('appointment_with', $doctorId)
        ->where('appointment_date', $date)
        ->where('is_deleted', 0)
        ->where('status', '!=', 2)
        ->get();

    // استخراج الأوقات فقط وتنسيقها (H:i)
    $bookedTimes = $appointments->map(function ($app) {
        if($app->time) {
            return \Carbon\Carbon::parse($app->time)->format('H:i');
        }
        return null;
    })->filter()->toArray();

    return response()->json([
        'status' => 'success',
        'booked_times' => array_values(array_unique($bookedTimes))
    ], 200);
}

// public function appointment_store(Request $request): JsonResponse
// {
//     $user = auth()->user();
//     $role = $user->roles_name;
//     $userId = $user->id;

//     if ($role == 'Patient') {
//         $validator = Validator::make($request->all(), [
//             'appointment_with' => 'required',
//             'appointment_date' => 'required',
//             'available_slot' => 'required',
//         ]);
//     } elseif ($role == 'Doctor') {
//         $validator = Validator::make($request->all(), [
//             'appointment_for' => 'required',
//             'appointment_date' => 'required',
//             'available_slot' => 'required',
//         ]);
//     }

//     if ($validator->fails()) {
//         return $this->returnError("V00", $validator->errors());
//     }

//     // 🔴 === بداية الكود الجديد للمطابقة الذكية للوقت === 🔴
//     $doctorId = ($role == 'Patient') ? $request->appointment_with : $userId;

//     // 1. جلب جميع مواعيد الطبيب في هذا اليوم فقط (عددها سيكون قليل جداً)
//     $existingAppointments = \App\Models\back\Appointment::where('appointment_with', $doctorId)
//         ->where('appointment_date', $request->appointment_date)
//         ->get();

//     // 2. المطابقة باستخدام Carbon لتخطي اختلافات SQL
//     $isBooked = $existingAppointments->contains(function ($app) use ($request) {
//         if (!$app->time) return false;
        
//         // توحيد الصيغة بشكل قاطع (H:i)
//         $storedTime = \Carbon\Carbon::parse($app->time)->format('H:i');
//         $requestedTime = \Carbon\Carbon::parse($request->available_slot)->format('H:i');
        
//         // تحقق من التطابق، وأن الموعد غير محذوف وغير ملغى
//         // نستخدم is_null لتفادي المشاكل إذا كان العمود يقبل القيمة Null
//         $isNotDeleted = ($app->is_deleted == 0 || is_null($app->is_deleted));
//         $isNotCancelled = ($app->status != 2);

//         return ($storedTime == $requestedTime) && $isNotDeleted && $isNotCancelled;
//     });

//     if ($isBooked) {
//         return $this->returnError("V01", "عذراً، هذا الموعد تم حجزه مسبقاً، يرجى اختيار وقت آخر.");
//     }
//     // 🔴 === نهاية الكود الجديد === 🔴

//     $input = $request->all();
//     try {
//         $appointment = $this->AppointmentRepository->store($input);
//         if (!$appointment) {
//             return $this->returnError("D01", "Something went wrong..!");
//         } else {
//             return $this->returnSuccess("D00", "Appointment successfully");
//         }
//     } catch (\Exception $e) {
//         return $this->returnError("D01", $e->getMessage());
//     }
// }
// 1. أضفنا الـ Repository هنا في القوسين
public function appointment_store(Request $request, \App\Repositories\Appointments\AppointmentRepository $appointmentRepo): JsonResponse
{
    $user = auth()->user();
    $role = $user->roles_name;
    $userId = $user->id;

    if ($role == 'Patient') {
        $validator = Validator::make($request->all(), [
            'appointment_with' => 'required',
            'appointment_date' => 'required',
            'available_slot' => 'required',
        ]);
    } elseif ($role == 'Doctor') {
        $validator = Validator::make($request->all(), [
            'appointment_for' => 'required',
            'appointment_date' => 'required',
            'available_slot' => 'required',
        ]);
    }

    if ($validator->fails()) {
        return $this->returnError("V00", $validator->errors());
    }

    // 🔴 === بداية الكود الجديد للمطابقة الذكية للوقت === 🔴
    $doctorId = ($role == 'Patient') ? $request->appointment_with : $userId;

    $existingAppointments = \App\Models\back\Appointment::where('appointment_with', $doctorId)
        ->where('appointment_date', $request->appointment_date)
        ->get();

    $isBooked = $existingAppointments->contains(function ($app) use ($request) {
        if (!$app->time) return false;
        
        $storedTime = \Carbon\Carbon::parse($app->time)->format('H:i');
        $requestedTime = \Carbon\Carbon::parse($request->available_slot)->format('H:i');
        
        $isNotDeleted = ($app->is_deleted == 0 || is_null($app->is_deleted));
        $isNotCancelled = ($app->status != 2);

        return ($storedTime == $requestedTime) && $isNotDeleted && $isNotCancelled;
    });

    if ($isBooked) {
        return $this->returnError("V01", "عذراً، هذا الموعد تم حجزه مسبقاً، يرجى اختيار وقت آخر.");
    }
    // 🔴 === نهاية الكود الجديد === 🔴

    $input = $request->all();
    try {
        // 2. التعديل الأهم: استخدمنا المتغير الجديد للحفظ بدلاً من $this
        $appointment = $appointmentRepo->store($input);
        
        if (!$appointment) {
            return $this->returnError("D01", "Something went wrong..!");
        } else {
            return $this->returnSuccess("D00", "Appointment successfully");
        }
    } catch (\Exception $e) {
        return $this->returnError("D01", $e->getMessage());
    }
}
    // public function appointment_store(Request $request): JsonResponse
    // {
    //     $user = auth()->user();
    //     $role = $user->roles_name;
    //     $userId = $user->id;
    //     if ($role == 'Patient') {
    //         $validator = Validator::make($request->all(), [
    //             'appointment_with' => 'required',
    //             'appointment_date' => 'required',
    //             'available_slot' => 'required',
    //         ]);
    //     } elseif ($role == 'Doctor') {
    //         $validator = Validator::make($request->all(), [
    //             'appointment_for' => 'required',
    //             'appointment_date' => 'required',
    //             'available_slot' => 'required',
    //         ]);
    //     }
    //     if ($validator->fails()) {
    //         return $this->returnError("V00", $validator->errors());
    //     }
    //     $isBooked = \App\Models\back\Appointment::where('appointment_with', $request->appointment_with)
    //     ->where('appointment_date', $request->appointment_date)
    //     ->where('time', $request->available_slot) // تأكد إذا كنت تستقبل الوقت باسم available_slot أو time
    //     ->where('is_deleted', 0)
    //     ->where('status', '!=', 2) // تجاهل المواعيد الملغاة (حسب أرقام الـ status عندك)
    //     ->exists();

    // if ($isBooked) {
    //     // إذا كان الموعد موجود، نرجع رسالة خطأ واضحة للتطبيق
    //     return $this->returnError("V01", "عذراً، هذا الموعد تم حجزه مسبقاً، يرجى اختيار وقت آخر.");
    // }
    //     $input = $request->all();
    //     try {
    //         $appointment = $this->AppointmentRepository->store($input);
    //         if (!$appointment) {
    //             return $this->returnError("D01", "Something went wrong..!");
    //         } else {
    //             return $this->returnSuccess("D00", "Appointment successfully");
    //         }
    //     } catch (Exception $e) {
    //         return $this->returnError("D01", $e->getMessage());
    //     }
    // }

    public function appointment_update(Request $request): JsonResponse
    {
        $user = auth()->user();
        $role = $user->roles_name;
        $userId = $user->id;
        if ($role == 'Patient') {
            $validator = Validator::make($request->all(), [
                'appointment_with' => 'required',
                'appointment_date' => 'required',
                'available_slot' => 'required',
                'appointment' => 'required',
            ]);
        } elseif ($role == 'Doctor') {
            $validator = Validator::make($request->all(), [
                'appointment_for' => 'required',
                'appointment_date' => 'required',
                'available_slot' => 'required',
                'appointment' => 'required',
            ]);
        }
        if ($validator->fails()) {
            return $this->returnError("V00", $validator->errors());
        }
        $appointment = Appointment::find($request->appointment);
        if (!$appointment) {
            return $this->returnError("D01", "Appointment not exist..");
        }
        $input = $request->all();
        if ($role == 'Patient') {
            $input['appointment_for'] = $userId;
        } elseif ($role == 'Doctor') {
            $input['appointment_with'] = $userId;
        }
        try {
            $this->AppointmentRepository->update($input, $appointment);
            return $this->returnSuccess("D00", "Appointment Updated successfully");
        } catch (Exception $e) {
            return $this->returnError("D01", $e->getMessage());
        }
    }

    // public function pat_appoints(): JsonResponse
    // {
    //     $appointments = $this->AppointmentRepository->pat_appoints();
    //     if (!$appointments) {
    //         return $this->returnError("D01", "There are no appointments..");
    //     } else {
    //         return $this->returnData("upcoming_Appointment", $appointments, "", "D00");
    //     }
    // }
// public function pat_appoints(): JsonResponse
// {
//     $appointments = $this->AppointmentRepository->pat_appoints(); // هذه تجلب البيانات مع الـ doctor

//     if ($appointments->isEmpty()) {
//         return response()->json(['status' => 'success', 'data' => []], 200);
//     }

//     // هنا نقوم بعمل map للبيانات لتكون واضحة للتطبيق
//     $data = $appointments->map(function ($appointment) {
//         return [
//             'id' => $appointment->id,
//             'appointment_date' => $appointment->appointment_date,
//             // جلب اسم الطبيب من العلاقة التي جلبتها في الـ Repository
// //   'doctor_name' => $appointment->doctor ? $appointment->doctor->name : 'طبيب غير محدد',            // جلب الوقت من العلاقة timeSlot إذا كان متاحاً
// 'doctor_name' => $appointment->doctor ? $appointment->doctor->name_ar : 'طبيب غير محدد',
// 'time' => $appointment->time ? \Carbon\Carbon::parse($appointment->time)->format('H:i') : '--:--',        ];
//     });

//     return response()->json([
//         'status' => 'success',
//         'upcoming_Appointment' => $data
//     ], 200);
// }
//     public function doc_appoints(): JsonResponse
//     {
//         $appointments = $this->AppointmentRepository->doc_appoints();
//         if ($appointments->count()==0) {
//             return $this->returnError("D01", "There are no appointments..");
//         } else {
//             return $this->returnData("Appointments", $appointments, "", "D00");
//         }
//     }
public function pat_appoints(): JsonResponse
{
    $appointments = $this->AppointmentRepository->pat_appoints(); // تجلب البيانات مع الـ doctor

    if ($appointments->isEmpty()) {
        return response()->json(['status' => 'success', 'data' => []], 200);
    }

    // هنا نقوم بعمل map للبيانات لتكون واضحة للتطبيق
    $data = $appointments->map(function ($appointment) {
        return [
            'id' => $appointment->id,
            'appointment_date' => $appointment->appointment_date,
            'doctor_name' => $appointment->doctor ? $appointment->doctor->name_ar : 'طبيب غير محدد',
            'time' => $appointment->time ? \Carbon\Carbon::parse($appointment->time)->format('H:i') : '--:--',
            // 🔴 التعديل هنا: إضافة سطر إرسال الحالة إلى فلاتر
            'status' => $appointment->status, 
        ];
    });

    return response()->json([
        'status' => 'success',
        'upcoming_Appointment' => $data
    ], 200);
}
    public function doc_today_appoints(): JsonResponse
    {
        $appointments = $this->AppointmentRepository->doc_today_appoints();
        if (!$appointments) {
            return $this->returnError("D01", "There are no appointments..");
        } else {
            return $this->returnData("Appointments", $appointments, "", "D00");
        }
    }

    public function pat_canceled_appoints(): JsonResponse
    {
        $appointments = $this->AppointmentRepository->pat_canceled_appoints();
        if (!$appointments) {
            return $this->returnError("D01", "There are no appointments..");
        } else {
            return $this->returnData("Appointments", $appointments, "", "D00");
        }
    }

    public function pat_previos_appoints():JsonResponse{
        $appointments = $this->AppointmentRepository->pat_previos_appoints();
        if (!$appointments) {
            return $this->returnError("D01", "There are no appointments..");
        } else {
            return $this->returnData("Appointments", $appointments, "", "D00");
        }
    }

    public function cancel_appoint(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'appointment' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->returnError("V00", $validator->errors());
        }
        $appointment = Appointment::find($request->appointment);
        if (!$appointment) {
            return $this->returnError("D01", "Appointment Dos not Exist. . ");
        } else {
            $appoint = $appointment->id;
            $success = $this->AppointmentRepository->cancel_appoint($appoint);
            if (!$success) {
                return $this->returnError("D01", "something went wrong.. pleas try again");
            }
            return $this->returnSuccess("D00", "appointment canceled successfully..");
        }
    }

    public function appointment_delete(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'appointment' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->returnError("V00", $validator->errors());
        }
        $appointment = Appointment::find($request->appointment);
        if (!$appointment) {
            return $this->returnError("D01", "appointment not exist..");
        }
        $appoint = $this->AppointmentRepository->destroy($appointment->id);
        return $this->returnSuccess("D00", "appointment deleted successfully..");
    }

// 29 الشهر
// public function accept_appointment(Request $request, \App\Repositories\Appointments\AppointmentRepository $appointmentRepo)
//     {
//         $appointmentId = $request->input('appointment_id');
        
//         // استخدمنا المتغير الجديد $appointmentRepo بدلاً من $this
//         $updatedAppointment = $appointmentRepo->accept_appoint($appointmentId);

//         if (!$updatedAppointment) {
//             return response()->json([
//                 'success' => false,
//                 'msg' => 'الموعد غير موجود'
//             ], 404);
//         }

//         return response()->json([
//             'success' => true,
//             'msg' => 'تم قبول الموعد بنجاح',
//             'data' => $updatedAppointment
//         ], 200);
//     }
public function reject_appointment(Request $request, \App\Repositories\Appointments\AppointmentRepository $appointmentRepo)
    {
        $appointmentId = $request->input('appointment_id');
        
        // نستخدم دالة الإلغاء الجاهزة لديك في الـ Repository
        $updatedAppointment = $appointmentRepo->cancel_appoint($appointmentId);

        if (!$updatedAppointment) {
            return response()->json([
                'success' => false,
                'msg' => 'الموعد غير موجود'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'msg' => 'تم رفض الموعد بنجاح',
            'data' => $updatedAppointment
        ], 200);
    }
public function accept_appointment(\Illuminate\Http\Request $request, \App\Repositories\Appointments\AppointmentRepository $appointmentRepo)
{
    $appointmentId = $request->input('appointment_id');
    
    // إرسال الطلب إلى ملف الـ Repository
    $updatedAppointment = $appointmentRepo->accept_appoint($appointmentId);

    if (!$updatedAppointment) {
        return response()->json([
            'success' => false,
            'msg' => 'الموعد غير موجود'
        ], 404);
    }

    return response()->json([
        'success' => true,
        'msg' => 'تم قبول الموعد بنجاح',
        'data' => $updatedAppointment
    ], 200);
}
//     public function doc_appoints()
// {
//     // 1. استخدام DB مباشرة للبحث في جدول الأطباء وتجاوز مشكلة اسم الموديل
//     $doctor = \Illuminate\Support\Facades\DB::table('gnr_m_doctors')
//                 ->where('user_id', auth()->user()->id)
//                 ->first();
    
//     if(!$doctor){
//         return response()->json([
//             'success' => false, 
//             'error' => 'D01', 
//             'msg' => 'لم يتم العثور على ملف الطبيب'
//         ]);
//     }

//     // 2. جلب كل المواعيد الخاصة بهذا الطبيب
//     $appointments = \App\Models\Front\Appointment::with('patient')
//         ->where('appointment_with', $doctor->id)
//         ->orderBy('appointment_date', 'asc') // ترتيب حسب التاريخ
//         ->orderBy('time', 'asc')             // ثم الترتيب حسب الوقت
//         ->get();

//     // 3. إرسال البيانات
//     return response()->json([
//         'success' => true,
//         'error' => 'D00',
//         'Appointments' => $appointments,
//         'msg' => ''
//     ]);
// }
public function doc_appoints(): \Illuminate\Http\JsonResponse
{
    $appointments = $this->AppointmentRepository->doc_appoints();
    
    if (!$appointments || count($appointments) == 0) {
        return $this->returnError("D01", "There are no appointments..");
    } else {
        return $this->returnData("Appointments", $appointments, "", "D00");
    }
}
}
