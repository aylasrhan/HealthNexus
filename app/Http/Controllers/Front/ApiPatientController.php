<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Repositories\Doctors\IDoctorRepository;
use App\Repositories\Patients\IPatientRepository;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Repositories\Clinics\IClinicRepository;

class ApiPatientController extends Controller
{
    use ResponseTrait;

    public IClinicRepository $ClinicRepository;
    private IDoctorRepository $DoctorRepository;


    public function __construct(IClinicRepository $clinic, IDoctorRepository $DoctorRepository, IPatientRepository $patientRepository)
    {
        $this->ClinicRepository = $clinic;
        $this->DoctorRepository = $DoctorRepository;
        $this->PatientRepository = $patientRepository;

    }

    public function departments(): JsonResponse
    {

        $departments = $this->ClinicRepository->index();
        if (!$departments) {
            return $this->returnSuccess("There are no departments..");
        } else
            return $this->returnData("departments", $departments);

    }

    public function cities(): JsonResponse
    {

        $cities = $this->PatientRepository->cities();
        if (!$cities) {
            return $this->returnError("D01", "There are no cities..");
        } else
            return $this->returnData("cities", $cities, "", "D00");
    }

    public function areas(Request $request): JsonResponse
    {
        $citie = $request->citie;
        $areas = $this->PatientRepository->areas($citie);
        if ($areas->count() == 0) {
            return $this->returnError("D01", "There are no areas..");
        } else {
            return $this->returnData("areas", $areas, "", "D00");
        }
    }
public function patientProfile(Request $request): JsonResponse
{
    try {
        $patientId = $request->patient_id;

        if (!$patientId) {
            return $this->returnError("P00", "رقم المريض مطلوب");
        }

        // ملاحظة: استخدمنا المسار الكامل للموديل هنا عشان ما تضطري تعملي import فوق وتصير خربطة
        // رح نبحث عن المريض إما عن طريق الـ id الأساسي أو عن طريق الـ user_id
        $patient = \App\Models\back\gnr_m_patients::with([
            'gnr_m_patients_medical_info',
            'cln_m_medical_his',
            'cln_x_prev_com',
            'cln_x_prev_dia'
        ])
        ->where('id', $patientId)
        ->orWhere('user_id', $patientId)
        ->first();

        if (!$patient) {
            return $this->returnError("P01", "المريض غير موجود بقاعدة البيانات");
        }

        // حساب العمر بطريقة آمنة (إذا كان تاريخ الميلاد غير موجود ما بيضرب الكود)
        $age = "غير محدد";
        if (!empty($patient->birth_date)) {
            try {
                $age = $patient->age();
            } catch (\Exception $e) {}
        }

        $data = [
            'id' => $patient->id,
            'full_name' => ($patient->f_name ?? '') . ' ' . ($patient->l_name ?? ''),
            'age' => $age,
            'sex' => $patient->sex == 1 ? 'ذكر' : 'أنثى',
            'blood' => $patient->blood ?? 'غير متوفرة',
            'mobile' => $patient->mobile ?? 'لا يوجد',
            'medical_info' => $patient->gnr_m_patients_medical_info,
            'medical_history' => $patient->cln_m_medical_his,
            'complaints' => $patient->cln_x_prev_com,
            'diagnoses' => $patient->cln_x_prev_dia,
        ];

        return $this->returnData("patient_profile", $data, "تم جلب البيانات بنجاح", "D00");

    } catch (\Exception $ex) {
        // إذا ضرب أي خطأ، رح يرجعلك السيرفر سبب الخطأ الحقيقي بدل شاشة 500
        return $this->returnError("E500", "خطأ في السيرفر: " . $ex->getMessage());
    }
}
// public function famous_doctors(): JsonResponse
// {
//     $famous_doctors = $this->DoctorRepository->getFamousDoctors();

//     if ($famous_doctors->isEmpty()) {
//         return response()->json(['status' => 'success', 'data' => []], 200);
//     }

//     $data = $famous_doctors->map(function ($doctor) {
//         return [
//             'id' => $doctor->id,
//             'name_ar' => $doctor->name_ar,
//             'specialization_ar' => $doctor->specialization_ar ?? 'طبيب',
//             // لا تضعي أي شيء إضافي يسبب خطأ في الـ JSON
//         ];
//     });

//     return response()->json([
//         'status' => 'success',
//         'data' => $data
//     ], 200);
// }
public function famous_doctors(): JsonResponse
{
    $famous_doctors = $this->DoctorRepository->getFamousDoctors();

    if ($famous_doctors->isEmpty()) {
        return response()->json(['status' => 'success', 'data' => []], 200);
    }

    $data = $famous_doctors->map(function ($doctor) {
        return [
            'id' => $doctor->id,
            'name_ar' => $doctor->name_ar,
            'specialization_ar' => $doctor->specialization_ar ?? 'طبيب',
            // إضافة الحقول المفقودة هنا ليرسلها السيرفر لتطبيق Flutter
            'from_time' => $doctor->from_time,
            'to_time' => $doctor->to_time,
            'slot_time' => $doctor->slot_time,
        ];
    });

    return response()->json([
        'status' => 'success',
        'data' => $data
    ], 200);
}
}