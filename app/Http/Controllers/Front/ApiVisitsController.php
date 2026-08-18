<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\back\cln_x_visits;
use App\Models\back\gnr_m_patients;
use App\Traits\ResponseTrait;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
//explain
class ApiVisitsController extends Controller
{
    use ResponseTrait;


   

public function add_diagnosis(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'visit_id' => 'required',
                'patient_id' => 'required',
                'diagnosis' => 'required|string',
            ]);

            $user = auth()->user();
            if (!$user) {
                return $this->returnError("D01", "غير مصرح لك بالدخول");
            }

            $doctorId = $user->doctor->id ?? $user->id;
            $timestamp = time();

            // 🔴 التعديل السحري: جلب الـ ID الحقيقي للمريض ليتوافق مع الجداول
            $realPatient = \App\Models\back\gnr_m_patients::where('id', $request->patient_id)
                ->orWhere('user_id', $request->patient_id)
                ->first();
            
            // إذا وجد المريض نأخذ الـ id الأساسي، وإلا نستخدم الرقم القادم من فلاتر
            $actualPatientId = $realPatient ? $realPatient->id : $request->patient_id;

            \DB::beginTransaction();

            if ($request->filled('complaint')) {
                \DB::table('cln_x_prev_com')->insert([
                    'visit' => $request->visit_id,
                    'patient' => $actualPatientId, // 🔴 الحفظ بالرقم الحقيقي
                    'doc' => $doctorId,
                    'val' => $request->complaint,
                    'date' => $timestamp
                ]);
            }

            \DB::table('cln_x_prev_dia')->insert([
                'visit' => $request->visit_id,
                'patient' => $actualPatientId, // 🔴 الحفظ بالرقم الحقيقي
                'doc' => $doctorId,
                'val' => $request->diagnosis,
                'date' => $timestamp
            ]);

            \DB::commit();
            return $this->returnSuccess("تم إضافة التشخيص والشكاية بنجاح");

        } catch (\Exception $ex) {
            \DB::rollBack();
            return $this->returnError("E500", "حدث خطأ أثناء الحفظ: " . $ex->getMessage());
        }
    }
public function pat_visits(): JsonResponse 
{
    // 1. جلب المستخدم
    $user = auth()->user();
    if (!$user) {
        return $this->returnError("D01", "غير مصرح لك بالدخول");
    }

    // 2. البحث عن المريض
    $patient = gnr_m_patients::where('user_id', $user->id)->first();
    if (!$patient) {
        return $this->returnError("D01", "بيانات المريض غير موجودة لهذا المستخدم");
    }

    // 3. جلب جميع الزيارات
    $visits = cln_x_visits::with(['gnr_m_clinics', 'cln_x_prev_not', 'cln_x_prev_dia', 'vitals', 'issuedPrescription.items','invoice.items'])
                ->where('patient', '=', $patient->id)
                ->orderBy('d_start', 'DESC')
                ->get();
                
    // 4. تنسيق التواريخ
    foreach ($visits as $visit) {
        if ($visit->d_start) {
             $visit->d_start = Carbon::createFromTimestamp((int) $visit->d_start)->format('Y-m-d \الساعة: h:i A');
        }
    }

    // 5. إرجاع البيانات كامِلة
    return response()->json([
        "success" => true,
        "visits" => $visits
    ]);
}

// public function pat_visits(): JsonResponse 
// {
//     // 1. جلب المستخدم
//     $user = auth()->user();
//     if (!$user) {
//         return $this->returnError("D01", "غير مصرح لك بالدخول");
//     }

//     // 2. البحث عن المريض
//     $patient = gnr_m_patients::where('user_id', $user->id)->first();
//     if (!$patient) {
//         return $this->returnError("D01", "بيانات المريض غير موجودة لهذا المستخدم");
//     }

//     \Log::info("جاري جلب زيارات المستخدم ID: " . $patient->id);

//     // 3. جلب جميع الزيارات بدون فلترة أو حذف المتكرر
//     $visits = cln_x_visits::with(['gnr_m_clinics', 'cln_x_prev_not', 'cln_x_prev_dia', 'vitals', 'issuedPrescription.items'])
//                   ->where('patient', '=', $patient->id)
//                   ->orderBy('d_start', 'DESC')
//                   ->get();
                  
//     \Log::info("عدد الزيارات الكلي التي تم جلبها: " . $visits->count());

//     // 4. تنسيق التواريخ
//     foreach ($visits as $visit) {
//         if ($visit->d_start) {
//              $visit->d_start = Carbon::createFromTimestamp((int) $visit->d_start)->format('Y-m-d \الساعة: h:i A');
//         }
//     }

//     // 5. إرجاع البيانات كامِلة
//     return response()->json([
//         "success" => true,
//         "visits" => $visits
//     ]);
// }
}
