<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\back\doctors;
use App\Models\back\gnr_m_areas;
use App\Models\back\gnr_m_patients;
use App\Models\User;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use App\Mail\VerificationCodeMail;
use App\Models\back\gnr_m_nationality;
use App\Models\back\gnr_m_cities;
use App\Models\back\gnr_m_clinics;
class ApiAuthController extends Controller
{
    use ResponseTrait;


    // public function register(Request $request): JsonResponse
    // {
    //     $validator = Validator::make($request->all(), [
    //         'name' => 'required',
    //         'email' => 'required|email|unique:users',
    //         'password' => 'required',
    //         'c_password' => 'required|same:password',
    //         'mother_name' => 'required',
    //         'mobile' => 'required',
    //         'birth_date' => 'required',
    //         'sex' => 'required',
    //         'blood' => 'required',
    //         'p_city' => 'required',
    //         'nationality' => 'required',
    //         'address' => 'required',
    //     ]);

    //     if ($validator->fails()) {
    //         return $this->returnError("V01", $validator->errors());
    //     }
    //     $newDate = Carbon::createFromFormat('m/d/Y', $request->birth_date)->format('Y-m-d');
    //     $input = $request->all();
    //     $input['password'] = bcrypt($input['password']);
    //     $verificationCode = mt_rand(1000, 9999);//Str::random_int(4);
    //     $input['verification_code'] = $verificationCode;
    //     $input['roles_name'] = 'Patient';
    //     $input['Status'] = 'مفعل';
    //     try {
    //         DB::transaction(function () use ($input, $newDate) {
    //             $user = User::create([
    //                 'name' => $input['name'],
    //                 'email' => $input['email'],
    //                 'password' => $input['password'],
    //                 'verification_code' => $input['verification_code'],
    //                 'roles_name' => $input['roles_name'],
    //                 'Status' => $input['Status'],
    //             ]);

    //             $patient = gnr_m_patients::create([

    //                 'f_name' => $input['name'],
    //                 'mother_name' => $input['mother_name'],
    //                 'mobile' => $input['mobile'],
    //                 'birth_date' => $newDate,
    //                 'sex' => $input['sex'],
    //                 'blood' => $input['blood'],
    //                 'p_city' => $input['p_city'],
    //                 'nationality' => $input['nationality'],
    //                 'address' => $input['address'],
    //                 'user_id' => $user->id,
    //             ]);
    //         });
    //         DB::commit();
    //         $user = User::where('email', $input['email'])->first();
    //         if ($request->phone != null) {
    //             $user->phone = $input['phone'];
    //             $user->save();
    //         }
    //         if ($request->date != null) {
    //             $user->date = $input['date'];
    //             $user->save();
    //         }
    //         $token = $user->createToken('Personal Access Token')->accessToken;
    //         $this->sendMail($user, $input['verification_code']);
    //         return $this->returnData("user_token", $token, 'registered successfully.,check your email to get verification code', "D00");
    //     } catch (\Exception $ex) {
    //         DB::rollback();
    //         return $this->returnError("D01", $ex->getMessage());
    //     }

    // }
   public function register(Request $request): JsonResponse
{
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255|unique:users,email',
        'password' => 'required|string|min:8',
        'c_password' => 'required|same:password',
        'roles_name' => 'nullable|in:patient',

        // حقول المريض (مطلوبة فقط إذا كان الدور Patient)
        'mother_name' => 'required|string|max:255',
        'mobile' => 'nullable|string|max:30',
        'birth_date' => 'required|date_format:m/d/Y',
        'sex' => 'required|integer|in:1,2',
        'blood' => 'required|string|max:10',
        'p_city' => 'required|integer|exists:gnr_m_cities,id',
        'nationality' => 'required|integer|exists:gnr_m_nationality,id',
        'address' => 'required|string|max:500',

        // حقول الطبيب (مطلوبة فقط إذا كان الدور Doctor)
    ]);

    if ($validator->fails()) {
        return $this->returnError("V01", $validator->errors());
    }

    $input = $validator->validated();
    $input['roles_name'] = ['patient'];
    $input['password'] = bcrypt($input['password']);
    $verificationCode = (string) random_int(1000, 9999);
    // $verificationCode = (string) random_int(100000, 999999);
    $input['verification_code'] = Hash::make($verificationCode);
    $input['Status'] = 'مفعل';

    try {
        DB::transaction(function () use ($input, $request) {
            // إنشاء المستخدم
            $user = User::create([
                'name' => $input['name'],
                'email' => $input['email'],
                'password' => $input['password'],
                'verification_code' => $input['verification_code'],
                'roles_name' => $input['roles_name'],
                'Status' => $input['Status'],
            ]);

            // إذا كان الدور مريض
            $user->assignRole('patient');

            if ($user->hasSystemRole('patient')) {
                $newDate = Carbon::createFromFormat('m/d/Y', $input['birth_date'])->format('Y-m-d');
                gnr_m_patients::create([
                    'f_name' => $input['name'],
                    'mother_name' => $input['mother_name'],
                    'mobile' => $input['mobile'],
                    'birth_date' => $newDate,
                    'sex' => $input['sex'],
                    'blood' => $input['blood'],
                    'p_city' => $input['p_city'],
                    'nationality' => $input['nationality'],
                    'address' => $input['address'],
                    'user_id' => $user->id,
                ]);
            }
            // إذا كان الدور طبيب
            elseif ($user->hasSystemRole('doctor')) {
                \App\Models\back\doctors::create([
                    'name_ar' => $input['name'],
                    'specialization_ar' => $input['specialization'],
                    'phone_number' => $input['mobile'] ?? null,
                    'user_id' => $user->id,
                    'act' => 1,
                    'famous' => 0,
                ]);
            }
        });

        $user = User::where('email', $input['email'])->first();
        $token = $user->createToken('Personal Access Token')->accessToken;
        Cache::put("email_verification_expires:{$user->id}", true, now()->addMinutes(10));
        $this->sendMail($user, $verificationCode);

        return $this->returnData("user_token", $token, 'Registered successfully, check your email.', "D00");
    } catch (\Throwable $ex) {
    report($ex);
    return $this->returnError("D01", $ex->getMessage()); // اطبع رسالة الخطأ الحقيقية
}
}

    // public function login(Request $request): JsonResponse
    // {
    //     \Log::info("الدور المستلم هو: " . $request->role); // هذا السطر سيحفظ المعلومة في ملف laravel.log
    //     $validator = Validator::make($request->all(), [
    //         'email' => 'required|email',
    //         'password' => 'required',
    //     ]);

    //     if ($validator->fails()) {
    //         return $this->returnError("V00", $validator->errors());
    //     }
    //     if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
    //         $user = Auth::user();
    //         $token = $user->createToken('MyApp')->accessToken;
    //         return $this->returnData("user_token", $token, 'User login successfully.', "A00");
    //     } else {
    //         return $this->returnError('A01', 'Email or Password not correct');
    //     }
    // }
    public function login(Request $request): JsonResponse
{
    $validator = Validator::make($request->all(), [
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if ($validator->fails()) {
        return $this->returnError("V00", $validator->errors());
    }

    if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
        $user = Auth::user();
        $token = $user->createToken('MyApp')->accessToken;

        // قمنا باستخدام 'roles_name' كما هو موجود في الداتابيز
        $data = [
            'user_token' => $token,
            'roles_name' => $user->roles_name,
        ];

        return $this->returnData("data", $data, 'User login successfully.', "A00");
    } else {
        return $this->returnError('A01', 'Email or Password not correct');
    }
}

   public function verify(Request $request): JsonResponse
 {
    $user = $request->user();
  $validated = $request->validate(['code' => ['required', 'digits:4']]);    $cacheKey = "email_verification_expires:{$user->id}";

    if (!Cache::has($cacheKey)) {
        return $this->returnError("E05", "Verification code expired. Request a new code.");
    }

    if (!$user->verification_code || !Hash::check($validated['code'], $user->verification_code)) {
        return $this->returnError("E03", "Verification code is incorrect.");
    }

    $user->forceFill([
        'email_verified_at' => now(),
        'verification_code' => null,
    ])->save();
    Cache::forget($cacheKey);

    return $this->returnSuccess("Email verified successfully.");

    // أضفنا هذا السطر للتصحيح
    \Log::info("البيانات المرسلة: " . $request->getContent());

    $user = auth()->user();

    if (!$user) {
        return $this->returnError("E04", "المستخدم غير مسجل دخول (التوكين غير صحيح)");
    }

    $code = $request->code;

    // إضافة Log للقيم للمقارنة
    \Log::info("الكود المرسل: " . $code . " - الكود في قاعدة البيانات: " . $user->verification_code);

    if ($code != $user->verification_code) {
        return $this->returnError("E03", "الكود غير صحيح");
    } else {
        $user->email_verified_at = now();
        $user->save();
        return $this->returnSuccess("تم التحقق بنجاح");
    }
}

    public function resend(): JsonResponse
    {
        $user = request()->user();
        $code = (string) random_int(100000, 999999);

        $user->verification_code = Hash::make($code);
        $user->save();
        Cache::put("email_verification_expires:{$user->id}", true, now()->addMinutes(10));

        try {
            $this->sendMail($user, $code);
            return $this->returnSuccess("Verification code sent successfully.");
        } catch (\Throwable $exception) {
            report($exception);
            return $this->returnError("E02", "Unable to send verification code.");
        }

        $user = auth()->user();
        $code = $user->verification_code;
        if ($this->sendMail($user, $code)) {
            return $this->returnSuccess("verification code resend successfully");
        } else {
            return $this->returnError("E02", "something went wrong.. pleas try again");
        }
    }

    public function home(): JsonResponse
    {
        $user = auth()->user();
        if ($user->hasSystemRole('patient')) {
            $patient = User::with('gnr_m_patients')->find($user->id);
            return $this->returnData("user", $patient, "patient");
        } else
            $doctor = User::with('doctor')->find($user->id);
        return $this->returnData("user", $doctor, 'doctor');

    }

    public function profile(): JsonResponse
    {
        $user = auth()->user();
        $id = $user->id;
        if ($user->hasSystemRole('patient')) {
            $patient = gnr_m_patients::with('user')->where('user_id', $id)->first();
            return $this->returnData("patient", $patient, "patient", "D00");
        } elseif ($user->hasSystemRole('doctor')) {
            $doctor = doctors::with('user')->where('user_id', $id)->first();
            return $this->returnData("doctor", $doctor, "doctor", "D00");
        }
    }

    public function sendMail($user, $token)
    {
        Mail::send(
            'auth.verification_code',
            ['user' => $user, 'token' => $token],
            function ($message) use ($user) {
                $message->to($user->email);
                $message->subject("Verification Code");
            }
        );
        return true;
    }
    // أيلا
   public function getClinics() {
    $clinics = gnr_m_clinics::all();

    return response()->json([
        'status' => 'success',
        'data' => $clinics
    ]);
}
// داخل ApiAuthController.php
public function getNationalities()
{
    $nationalities = gnr_m_nationality::all();

    return response()->json([
        'status' => true,
        'data' => [
            'nationalities' => $nationalities
        ]
    ]);
}
// آيلا
public function logout(Request $request): JsonResponse
{
    $token = $request->user()->token();
    $token->revoke();
    return $this->returnSuccess("Logged out successfully");
}
public function getCities()
{
    $cities = gnr_m_cities::all();

    return response()->json([
        'status' => true,
        'data' => [
            'cities' => $cities
        ]
    ]);
}
}
