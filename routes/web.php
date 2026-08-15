<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Back\AdsController;
use App\Http\Controllers\Back\AppointmentController;
use App\Http\Controllers\Back\Cln_m_medical_hisController;
use App\Http\Controllers\Back\Cln_m_servicesController;
use App\Http\Controllers\Back\Cln_x_prev_clnController;
use App\Http\Controllers\Back\Cln_x_prev_noteController;
use App\Http\Controllers\Back\Cln_x_prev_comController;
use App\Http\Controllers\Back\Cln_x_prev_diaController;
use App\Http\Controllers\Back\Cln_x_prev_icd10Controller;
use App\Http\Controllers\Back\Cln_x_visitsController;
use App\Http\Controllers\Back\DoctorsController;
use App\Http\Controllers\Back\DiseaseAnalyticsController;
use App\Http\Controllers\Back\Gnr_m_clinicsController;
use App\Http\Controllers\Back\Gnr_m_patientsController;
use App\Http\Controllers\Back\Cln_x_prev_strController;
use App\Http\Controllers\Back\Gnr_m_patientsInfoController;
use App\Http\Controllers\Back\Medical_fileController;
use App\Http\Controllers\Back\QuestionsController;
use App\Http\Controllers\Back\ReportsController;
use App\Http\Controllers\Back\ReviewsController;
use App\Http\Controllers\Back\RoleController;
use App\Http\Controllers\Back\UserController;
use App\Http\Controllers\Back\InvoiceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::get('/login', function () {
    return view('auth.login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard');

Route::middleware('auth')->group(callback: function () {


    Route::get('/profile1', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile1', [ProfileController::class, 'update'])->name('profile.update');

    Route::resource('roles', RoleController::class)->middleware('role:super_admin');
    Route::resource('users', UserController::class)->middleware('role:super_admin');

    Route::resource('departments', Gnr_m_clinicsController::class)->middleware('staff');

    //Route::get('/doctors/{doctors}/edit/section',  [DoctorsController::class, 'edit'])->name("doctors.edit");
    Route::resource('doctors', DoctorsController::class)->middleware('staff');
    Route::resource('patients', Gnr_m_patientsController::class);
    Route::resource('visits', Cln_x_visitsController::class);
    // Route::get('visits/MyVisits',  [Cln_x_visitsController::class, 'getVisitForDoctor']);

    // Route::get('/services/{id}/{clinic}/edit', [Cln_m_servicesController::class])->name("services.edit");
    Route::get('medical-files', [Medical_fileController::class, 'index'])->name('medical-files.index');
    Route::get('consultations/appointments/{appointment}/start', [Medical_fileController::class, 'startConsultation'])->name('consultations.start');
    Route::get('consultations/diagnoses/search', [Medical_fileController::class, 'searchDiagnoses'])->name('consultations.diagnoses.search');
    Route::get('consultations/medical-history/search', [Medical_fileController::class, 'searchMedicalHistory'])->name('consultations.medical-history.search');
    Route::get('consultations/{visit}/edit', [Medical_fileController::class, 'editConsultation'])->name('consultations.edit');
    Route::put('consultations/{visit}', [Medical_fileController::class, 'saveConsultation'])->name('consultations.update');
    Route::post('consultations/{visit}/reopen', [Medical_fileController::class, 'reopenConsultation'])->name('consultations.reopen');
    Route::get('services/{service}', [Cln_m_servicesController::class, 'show'])->name('services.show');
    Route::resource('services', Cln_m_servicesController::class)->except(['index', 'show'])->middleware('medical.visit');
    Route::resource('medical', Cln_m_medical_hisController::class)->middleware('medical.visit');
    Route::resource('com', Cln_x_prev_comController::class)->middleware('medical.visit');
    Route::resource('str', Cln_x_prev_strController::class)->middleware('medical.visit');
    Route::resource('cln', Cln_x_prev_clnController::class)->middleware('medical.visit');
    Route::resource('dia', Cln_x_prev_icd10Controller::class)->middleware('medical.visit');
    Route::resource('note', Cln_x_prev_noteController::class)->middleware('medical.visit');
    Route::resource('patients_info', Gnr_m_patientsInfoController::class)->middleware('medical.visit');
    Route::resource('report', ReportsController::class)->only(['index', 'store'])->middleware('staff');
    Route::get('disease-analytics', [DiseaseAnalyticsController::class, 'index'])->name('analytics.diseases')->middleware('staff');
    Route::post('disease-analytics/export', [DiseaseAnalyticsController::class, 'export'])->name('analytics.diseases.export')->middleware('staff');
    Route::get('wallet', fn () => redirect()->route('invoices.index'))->name('wallet.index')->middleware('staff');
    Route::middleware('staff')->group(function () {
        Route::get('invoices', [InvoiceController::class, 'index'])->name('invoices.index');
        Route::get('invoices-settings/service-prices', [InvoiceController::class, 'servicePrices'])->name('invoices.service-prices');
        Route::put('invoices-settings/service-prices', [InvoiceController::class, 'updateServicePrices'])->name('invoices.service-prices.update');
        Route::post('invoices/visits/{visit}', [InvoiceController::class, 'store'])->name('invoices.store');
        Route::get('invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
        Route::put('invoices/{invoice}', [InvoiceController::class, 'update'])->name('invoices.update');
        Route::post('invoices/{invoice}/payments', [InvoiceController::class, 'payment'])->name('invoices.payments.store');
        Route::get('invoices/{invoice}/pdf', [InvoiceController::class, 'pdf'])->name('invoices.pdf');
    });
    Route::resource('ads', AdsController::class)->middleware('role:super_admin');
    Route::resource('review', ReviewsController::class)->middleware('role:super_admin');
    Route::resource('questions', QuestionsController::class);
    Route::get('/questions/{section}/answer', [QuestionsController::class,'answerTheQ'])->name("questions.answer");
    Route::get('/questions/user/{user}', [QuestionsController::class,'userQuestions'])->name("questions.user");

###################################### Appointment Routes ######################################################
    Route::get('appointments', [AppointmentController::class, 'index']);
    Route::post('appointment-status/{id}', [AppointmentController::class, 'appointment_status']);
    Route::get('getMonthlyAppointments', [AppointmentController::class, 'getMonthlyAppointments']);
    Route::post('/doctor-by-day-time', [AppointmentController::class, 'doctor_by_day_time'])->name('doctor_by_day_time');
    Route::post('/appointment-time-by-appointment-slot', [AppointmentController::class, 'time_by_slot'])->name('timeBySlot');
    Route::get('appointment-create', [AppointmentController::class, 'appointment_create']);
    Route::post('appointment-store', [AppointmentController::class, 'store']);
    Route::get('/cal-appointment-show', [AppointmentController::class, 'cal_appointment_show']);
    Route::get('pending-appointment', [AppointmentController::class, 'pending_appointment']);
    Route::get('upcoming-appointment', [AppointmentController::class, 'upcoming_appointment']);
    Route::patch('appointments/{appointment}/confirm', [AppointmentController::class, 'confirm'])->name('appointments.confirm');
    Route::patch('appointments/{appointment}/cancel', [AppointmentController::class, 'cancel'])->name('appointments.cancel');
    Route::get('today-appointment', [AppointmentController::class, 'today_appointment']);
    Route::get('patient-appointments/{id}', [AppointmentController::class, 'patient_appointments']);
    Route::post('filter_App', [AppointmentController::class, 'filter']);
####################################################################################################################

    //zRoute::get('/user/{id}', [UserController::class, 'show']);
    //Route::get('/MedicalFile/create/{visit}/{clinic}/{patient}', [Medical_fileController::class])->name("MedicalFile.create");;
    Route::resource('MedicalFile', Medical_fileController::class);

    Route::get('/{page}', [AdminController::class, 'index'])->middleware('staff');

});
if (false) Route::get('/fix-password', function () {
    $user = \App\Models\User::where('email', 'alissar37alhajj@gmail.com')->first();
    if ($user) {
        $user->password = \Illuminate\Support\Facades\Hash::make('12345678');
        $user->save();
        return "تم تحديث كلمة السر بنجاح، يمكنك الآن الدخول بكلمة 12345678";
    }
    return "المستخدم غير موجود";
});
require __DIR__ . '/auth.php';
