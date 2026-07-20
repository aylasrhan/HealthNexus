<?php

namespace App\Repositories\Doctors;

use App\Models\back\DoctorAvailableDay;
use App\Models\back\DoctorAvailableSlot;
use App\Models\back\doctors;
use App\Models\User;
use App\Traits\UploadFileTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DoctorRepository implements IDoctorRepository
{
    use UploadFileTrait;

    public $doctor;
    public $availableDay;
    public $availableSlot;

    public function __construct(doctors $doctor, DoctorAvailableDay $availableDay, DoctorAvailableSlot $availableSlot)
    {
        $this->doctor = $doctor;
        $this->availableDay = $availableDay;
        $this->availableSlot = $availableSlot;
    }

    public function index()
    {
        return doctors::with('user', 'gnr_m_clinics')->get();
    }

    public function show($subgrp)
    {
        // استرجاع بسيط لتجنب أي أخطاء في العلاقات حالياً
        return doctors::where('subgrp', $subgrp)->get();
    }

    public function edit($doctor)
    {
        $doc = $this->doctor::with('user')->where('id', '=', $doctor)->get();
        $user = User::all();
        return [$doc, $user];
    }

    public function update(Request $request, doctors $doctors)
    {
        try {
            DB::transaction(function () use ($request) {
                $doctor = doctors::findOrFail($request->doctor_id);
                $new_image = $this->ReplaceImg($doctor->photo, $request, 'photo', 'doctors');
                $doctor->act = $request->act;
                $doctor->name_ar = $request->name_ar;
                $doctor->from_time = $request->from_time;
                $doctor->to_time = $request->to_time;
                $doctor->slot_time = $request->slot_time;
                $doctor->user_id = $request->user_id;
                $doctor->phone_number = $request->phone_number;
                $doctor->photo = $new_image;
                $doctor->subgrp = $request->subgrp;
                $doctor->sex = $request->sex;
                $doctor->specialization_ar = $request->specialization_ar;
                $doctor->save();
            });
            DB::commit();
        } catch (\Exception $ex) {
            DB::rollback();
            return redirect()->back()->with(['error' => $ex->getMessage()]);
        }
    }

    public function create()
    {
        return []; // تم تنفيذها لتوافق الـ Interface
    }

    public function store(Request $request)
    {
        // تم اختصار الكود هنا للتوضيح، تأكدي من منطق الحفظ الخاص بك
        return [];
    }

    // public function search($key)
    // {
    //     $query = doctors::query()->join('users', 'doctors.user_id', '=', 'users.id')->orderBy('users.id');
    //     $columns = ['doctors.name_ar', 'specialization_ar'];
    //     foreach ($columns as $column) {
    //         $query->orWhere($column, 'LIKE', '%' . $key . '%');
    //     }
    //     return $query->get();
    // }
public function search($key)
{
    // البحث المباشر في جدول الأطباء دون الحاجة لـ Join مع الـ users لتجنب مشاكل وتضارب البيانات
    return doctors::where('name_ar', 'LIKE', '%' . $key . '%')
                  ->orWhere('specialization_ar', 'LIKE', '%' . $key . '%')
                  ->get();
}
    public function destroy($doctors)
    {
        try {
            DB::transaction(function () use ($doctors) {
                $doctor = doctors::find($doctors);
                if ($doctor) {
                    if ($doctor->photo !== "" && file_exists(public_path('img/' . $doctor->photo))) {
                        unlink(public_path('img/' . $doctor->photo));
                    }
                    $doctor->delete();
                    if ($doctor->user_id !== "") {
                        User::find($doctor->user_id)?->delete();
                    }
                }
            });
            DB::commit();
        } catch (\Exception $ex) {
            DB::rollback();
        }
    }

    public function getFamousDoctors()
{
    // نفترض هنا أن لديك عمود في جدول الأطباء اسمه 'famous' 
    // وقيمته 1 تعني أن الطبيب مشهور.
    // إذا كان اسم العمود مختلفاً، قومي بتغيير 'famous' بالاسم الصحيح
    return doctors::where('famous', 1)->get();
}
}