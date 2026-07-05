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

// public function famous_doctors(): JsonResponse
// {
//     $famous_doctors = $this->DoctorRepository->getFamousDoctors();

//     if ($famous_doctors->isEmpty()) {
//         return $this->returnSuccess("D01", "There are no Famous Doctors..");
//     }

//     // بدلاً من استخدام map فقط للأسماء، سنقوم بتحويل الكوليكشن إلى مصفوفة (Array)
//     // لضمان إعادة بناء هيكل الـ JSON بشكل سليم من قبل لارفل
//     $data = $famous_doctors->map(function ($doctor) {
//         return [
//             'id' => $doctor->id,
//             'name_ar' => preg_replace('/[\x00-\x1F\x7F]/u', '', $doctor->name_ar),
//             'specialization_ar' => preg_replace('/[\x00-\x1F\x7F]/u', '', $doctor->specialization_ar),
//             'famous' => (int) $doctor->famous, // فرض تحويل القيمة لرقم صحيح لضمان سلامة الـ JSON
//             'act' => (int) $doctor->act,
//             // أضيفي هنا أي حقول أخرى ضرورية تظهر في تطبيقك
//         ];
//     });

//     return response()->json([
//         'status' => 'success',
//         'data' => $data
//     ], 200, [], JSON_UNESCAPED_UNICODE);
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
            // لا تضعي أي شيء إضافي يسبب خطأ في الـ JSON
        ];
    });

    return response()->json([
        'status' => 'success',
        'data' => $data
    ], 200);
}

}
