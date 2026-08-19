<?php

use App\Http\Controllers\Auth\ApiAuthController;
use App\Http\Controllers\Front\ApiAppointmentController;
use App\Http\Controllers\Front\ApiMedical_fileController;
use App\Http\Controllers\Front\ApiPatientController;
use App\Http\Controllers\Front\ApiDoctorController;
use App\Http\Controllers\Front\ApiQuestionController;
use App\Http\Controllers\Front\ApiVisitsController;
use App\Http\Controllers\Front\ApiPrescriptionController;
use App\Http\Controllers\Front\ApiInvoiceController;
use App\Http\Controllers\VerificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ReviewController; 
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

##################################### Auth Apis #########################################
Route::post('register', [ApiAuthController::class, 'register'])->middleware('throttle:5,1');
Route::post('Api_login', [ApiAuthController::class, 'login'])->middleware('throttle:10,1');
Route::post('areas', [ApiPatientController::class, 'areas']);
Route::get('get-nationalities', [ApiAuthController::class, 'getNationalities']);
Route::get('cities', [ApiAuthController::class, 'getCities']);
Route::get('clinics', [ApiAuthController::class, 'getClinics']);
    Route::get('famous_doctors', [ApiPatientController::class, 'famous_doctors']);
Route::get('/booked-times', [ApiAppointmentController::class, 'get_booked_times']);
Route::middleware('auth:api')->group(function () {
    Route::post('accept-appointment', [ApiAppointmentController::class, 'accept_appointment'])->middleware('throttle:20,1');
    Route::post('reject-appointment', [ApiAppointmentController::class, 'reject_appointment'])->middleware('throttle:20,1');
    Route::post('email/verify', [ApiAuthController::class, 'verify'])->middleware('throttle:5,1');
    Route::post('email/resend', [ApiAuthController::class, 'resend'])->middleware('throttle:3,1');
    Route::post('logout', [ApiAuthController::class, 'logout']);
    Route::get('home', [ApiAuthController::class, 'home']);
    Route::get('profile', [ApiAuthController::class, 'profile']);
});
#########################################################################################
Route::middleware('auth:api')->group(function () {
    Route::get('departments', [ApiPatientController::class, 'departments']);
    // Route::get('famous_doctors', [ApiPatientController::class, 'famous_doctors']);
    Route::post('add-diagnosis', [ApiVisitsController::class, 'add_diagnosis']);
    Route::get('patient/profile', [ApiPatientController::class, 'patientProfile']);
    Route::post('doctors_by_department', [ApiDoctorController::class, 'dep_doctor']);
    Route::post('search', [ApiDoctorController::class, 'search']);
    Route::post('review', [ApiDoctorController::class, 'review']);
    Route::post('/doctor/rate', [ReviewController::class, 'store']); // <====== أضيفي المسار الجديد هنا
    Route::get('visits', [ApiVisitsController::class,'pat_visits']);
    Route::get('visits/{visit}/prescription', [ApiPrescriptionController::class, 'show']);
    Route::get('visits/{visit}/prescription/pdf', [ApiPrescriptionController::class, 'pdf']);
    Route::get('invoices', [ApiInvoiceController::class, 'index']);
    Route::get('invoices/{invoice}', [ApiInvoiceController::class, 'show']);
    Route::get('invoices/{invoice}/pdf', [ApiInvoiceController::class, 'pdf']);
    Route::post('medical-info', [ApiMedical_fileController::class, 'medical_info']);

    ################################# Appointment Apis ########################################
    Route::post('doctor_available_days', [ApiAppointmentController::class, 'doctor_available_days']);
    Route::post('slots', [ApiAppointmentController::class, 'slots_by_day']);
    Route::post('appointment-store', [ApiAppointmentController::class, 'appointment_store']);
    Route::post('appointment-update', [ApiAppointmentController::class, 'appointment_update']);
    Route::get('patient-appointments', [ApiAppointmentController::class, 'pat_appoints']);
    Route::get('doctor-appointments', [ApiAppointmentController::class, 'doc_appoints']);
    Route::get('patient-canceled-appointments', [ApiAppointmentController::class, 'pat_canceled_appoints']);
    Route::get('patient-previos-appointments', [ApiAppointmentController::class, 'pat_previos_appoints']);
    Route::get('doctor-today-appointments', [ApiAppointmentController::class, 'doc_today_appoints']);
    Route::post('cancel-appointment', [ApiAppointmentController::class, 'cancel_appoint']);
    Route::post('appointment-delete', [ApiAppointmentController::class, 'appointment_delete']);
    #############################################################################################

    ############################### Questions Routes ############################################
    Route::get('departments', [ApiQuestionController::class, 'deps']);
    Route::post('ask', [ApiQuestionController::class, 'store']);
    Route::get('patient_questions', [ApiQuestionController::class, 'pat_quests']);
    Route::get('doctor_questions', [ApiQuestionController::class, 'doc_quests']);
    Route::post('answer', [ApiQuestionController::class, 'answer']);

    #############################################################################################

});


