<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\back\cln_x_visits;
use App\Models\back\gnr_m_patients;
use App\Traits\ResponseTrait;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiVisitsController extends Controller
{
    use ResponseTrait;


    // public function pat_visits():JsonResponse{

    //     // $user = auth()->user();
    //     $userID = $user->id;
    //     // $patient = gnr_m_patients::where('user_id',$userID)->first();
    //   $patient = gnr_m_patients::find(1); 

    // $visits = cln_x_visits::with('gnr_m_clinics')->where('patient', '=', $patient->id)->get();
    //     if (!$patient){
    //         return $this->returnError("D01","there are no visits");
    //     }else{
    //         $visits = cln_x_visits::with('gnr_m_clinics')->where('patient','=',$patient->id)->get();
    //         if ($visits->count() == 0){
    //             return $this->returnError("D01","there are no visits");
    //         }else{
    //             foreach ($visits as $visit){
    //                 $newdate = Carbon::parse($visit->d_start)->format('Y-m-d الساعة: h:i A');
    //                 $visit->d_start = $newdate;
    //             }
    //             return $this->returnData("visits",$visits,"hi");
    //         }
    //     }
    // }
    public function pat_visits(): JsonResponse 
{
    // 1. جلب المستخدم الذي سجل دخوله
    $user = auth()->user();
    
    if (!$user) {
        return $this->returnError("D01", "غير مصرح لك بالدخول");
    }

    // 2. البحث عن المريض المرتبط بهذا المستخدم
    $patient = gnr_m_patients::where('user_id', $user->id)->first();

    if (!$patient) {
        return $this->returnError("D01", "بيانات المريض غير موجودة لهذا المستخدم");
    }

    // 3. جلب الزيارات الخاصة بهذا المريض
    $visits = cln_x_visits::with('gnr_m_clinics')->where('patient', '=', $patient->id)->get();

    if ($visits->count() == 0) {
        return $this->returnError("D01", "لا توجد زيارات حالياً");
    }

    // 4. تنسيق التواريخ وإرجاع البيانات
    foreach ($visits as $visit) {
        $newdate = Carbon::parse($visit->d_start)->format('Y-m-d الساعة: h:i A');
        $visit->d_start = $newdate;
    }

    return $this->returnData("visits", $visits, "تم جلب البيانات بنجاح");
}

//    public function fucking_database(){
//        $visits = cln_x_visits::all();
//        if (!$visits){
//            return"fuck off";
//        }else{
//            foreach ($visits as $visit){
//                $newdate = Carbon::parse($visit->d_start)->format('Y-m-d الساعة: h:i A');
//                $visit->d_start = $newdate;
//                $visit->save();
//            }
//            return "DataBase Fucked Successfully...";
//        }
//    }
}
