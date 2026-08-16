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


public function appointment_store(Request $request, \App\Repositories\Appointments\AppointmentRepository $appointmentRepo): JsonResponse
{
    $user = auth()->user();
    $role = $user->primarySystemRole();
    $userId = $user->id;

    if ($role === 'patient') {
        $validator = Validator::make($request->all(), [
            'appointment_with' => 'required',
            'appointment_date' => 'required',
            'available_slot' => 'required',
        ]);
    } elseif ($role === 'doctor') {
        $validator = Validator::make($request->all(), [
            'appointment_for' => 'required',
            'appointment_date' => 'required',
            'available_slot' => 'required',
        ]);
    }

    if ($validator->fails()) {
        return $this->returnError("V00", $validator->errors());
    }

    // 🔴 الحل السحري: نأخذ الرقم من الفلاتر كما هو للمريض، ولا نبحث عنه لتجنب التداخل الكارثي!
    if ($role === 'doctor') {
        $doctorRecord = \App\Models\back\doctors::where('user_id', $userId)->first();
        $doctorId = $doctorRecord ? $doctorRecord->id : $userId;
        $request->merge(['appointment_with' => $doctorId]);
    }
    
    $doctorId = $request->appointment_with;

    // 🔴 === المطابقة الذكية للوقت والتاريخ === 🔴
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

    $input = $request->all();
    try {
        $appointment = $appointmentRepo->store($input);
        
        if (!$appointment) {
            return $this->returnError("D01", "Something went wrong..!");
        } else {
            return $this->returnSuccess("D00", "Appointment successfully");
        }
    } catch (\Exception $e) {
        report($e);
        return $this->returnError("D01", "Unable to create appointment.");
    }
}
    
    public function appointment_update(Request $request): JsonResponse
    {
        $user = auth()->user();
        $role = $user->primarySystemRole();
        $userId = $user->id;
        if ($role === 'patient') {
            $validator = Validator::make($request->all(), [
                'appointment_with' => 'required',
                'appointment_date' => 'required',
                'available_slot' => 'required',
                'appointment' => 'required',
            ]);
        } elseif ($role === 'doctor') {
            $validator = Validator::make($request->all(), [
                'appointment_for' => 'required',
                'appointment_date' => 'required',
                'available_slot' => 'required',
                'appointment' => 'required',
            ]);
        } else {
            abort(403);
        }
        if ($validator->fails()) {
            return $this->returnError("V00", $validator->errors());
        }
        $appointment = Appointment::find($request->appointment);
        if (!$appointment) {
            return $this->returnError("D01", "Appointment not exist..");
        }
        $this->authorize('update', $appointment);
        $input = $request->all();
        if ($role === 'patient') {
            $input['appointment_for'] = $userId;
        } elseif ($role === 'doctor') {
            $input['appointment_with'] = $userId;
        }
        try {
            $this->AppointmentRepository->update($input, $appointment);
            return $this->returnSuccess("D00", "Appointment Updated successfully");
        } catch (\Throwable $e) {
            report($e);
            return $this->returnError("D01", "Unable to update appointment.");
        }
    }

 
public function pat_appoints(): JsonResponse
{
    abort_unless(auth()->user()->hasSystemRole('patient'), 403);
    $appointments = $this->AppointmentRepository->pat_appoints(); 

    if ($appointments->isEmpty()) {
        return response()->json(['status' => 'success', 'data' => []], 200);
    }

    $data = $appointments->map(function ($appointment) {
        // 🔴 البحث المباشر والصارم حصراً باستخدام الـ id الأساسي لجدول الأطباء
        $doctorRecord = \App\Models\back\doctors::where('id', $appointment->appointment_with)->first();
        
        // كاحتياطي أخير: إذا لمჩيجدها بالـ id، يبحث بالـ user_id
        if (!$doctorRecord) {
            $doctorRecord = \App\Models\back\doctors::where('user_id', $appointment->appointment_with)->first();
        }

$correctDoctorName = $appointment->doctor_name_override ?? ($appointment->doctor ? $appointment->doctor->name_ar : 'طبيب غير محدد');
        return [
            'id' => $appointment->id,
            'appointment_date' => $appointment->appointment_date,
            'doctor_name' => $correctDoctorName, // الاسم الصحيح 100%
            'time' => $appointment->time ? \Carbon\Carbon::parse($appointment->time)->format('H:i') : '--:--',
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
        abort_unless(auth()->user()->hasSystemRole('doctor'), 403);
        $appointments = $this->AppointmentRepository->doc_today_appoints();
        if (!$appointments) {
            return $this->returnError("D01", "There are no appointments..");
        } else {
            return $this->returnData("Appointments", $appointments, "", "D00");
        }
    }

    public function pat_canceled_appoints(): JsonResponse
    {
        abort_unless(auth()->user()->hasSystemRole('patient'), 403);
        $appointments = $this->AppointmentRepository->pat_canceled_appoints();
        if (!$appointments) {
            return $this->returnError("D01", "There are no appointments..");
        } else {
            return $this->returnData("Appointments", $appointments, "", "D00");
        }
    }

    public function pat_previos_appoints():JsonResponse{
        abort_unless(auth()->user()->hasSystemRole('patient'), 403);
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
            $this->authorize('cancel', $appointment);
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
        $this->authorize('delete', $appointment);
        $appoint = $this->AppointmentRepository->destroy($appointment->id);
        return $this->returnSuccess("D00", "appointment deleted successfully..");
    }


public function reject_appointment(Request $request, \App\Repositories\Appointments\AppointmentRepository $appointmentRepo)
    {
        $validated = $request->validate(['appointment_id' => ['required', 'integer', 'exists:appointments,id']]);
        $appointment = Appointment::findOrFail($validated['appointment_id']);
        $this->authorize('cancel', $appointment);
        $appointmentId = $appointment->id;
        
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
    $validated = $request->validate(['appointment_id' => ['required', 'integer', 'exists:appointments,id']]);
    $appointment = Appointment::findOrFail($validated['appointment_id']);
    $this->authorize('accept', $appointment);
    $appointmentId = $appointment->id;
    
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
    abort_unless(auth()->user()->hasSystemRole('doctor'), 403);
    $appointments = $this->AppointmentRepository->doc_appoints();
    
    if (!$appointments || count($appointments) == 0) {
        return $this->returnError("D01", "There are no appointments..");
    } else {
        return $this->returnData("Appointments", $appointments, "", "D00");
    }
}
}
