<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class ClinicDoctorsSeeder extends Seeder
{
    private const PASSWORD = '12345678';

    public function run(): void
    {
        $clinics = DB::table('gnr_m_clinics')->orderBy('id')->get();
        if ($clinics->isEmpty()) {
            $this->command?->warn('لا توجد عيادات؛ شغّل Seeder العيادات أولًا.');
            return;
        }

        $role = Role::findOrCreate('doctor', 'web');
        $firstNames = ['أحمد', 'سارة', 'محمد', 'نور', 'سامر', 'لينا', 'عمر', 'ريم', 'كنان', 'ياسمين'];
        $lastNames = ['الحكيم', 'الخطيب', 'العلي', 'المصري', 'الحموي', 'النجار', 'العثمان', 'العبد', 'الشامي', 'المنصور'];

        DB::transaction(function () use ($clinics, $role, $firstNames, $lastNames) {
            foreach ($clinics->values() as $index => $clinic) {
                $firstName = $firstNames[$index % count($firstNames)];
                $name = 'د. '.$firstName.' '.$lastNames[intdiv($index, count($firstNames)) % count($lastNames)];
                $email = 'demo.doctor.clinic.'.$clinic->id.'@wecare.test';
                $isFemale = in_array($firstName, ['سارة', 'نور', 'لينا', 'ريم', 'ياسمين'], true);
                [$from, $to] = match ($index % 3) {
                    1 => ['12:00:00', '18:00:00'],
                    2 => ['15:00:00', '21:00:00'],
                    default => ['08:00:00', '14:00:00'],
                };

                $user = User::updateOrCreate(['email' => $email], [
                    'name' => $name,
                    'password' => Hash::make(self::PASSWORD),
                    'roles_name' => ['doctor'],
                    'Status' => '1',
                    'email_verified_at' => now(),
                ]);
                $user->syncRoles([$role]);

                $doctorId = DB::table('doctors')->where('user_id', $user->id)->value('id');
                $values = [
                    'act' => 1, 'famous' => $index < 8 ? 1 : 0,
                    'name_ar' => $name, 'from_time' => $from, 'to_time' => $to,
                    'slot_time' => 30,
                    'phone_number' => '0998'.str_pad((string) $clinic->id, 6, '0', STR_PAD_LEFT),
                    'subgrp' => (string) $clinic->id,
                    'sex' => $isFemale ? 2 : 1,
                    'specialization_ar' => $clinic->name_ar ?: 'طب عام',
                    'updated_at' => now(),
                ];
                if ($doctorId) {
                    DB::table('doctors')->where('id', $doctorId)->update($values);
                } else {
                    $doctorId = DB::table('doctors')->insertGetId($values + ['user_id' => $user->id, 'created_at' => now()]);
                }

                $worksThursday = $index % 2 === 0;
                DB::table('expert_available_days')->updateOrInsert(['expert_id' => $doctorId], [
                    'sun' => 1, 'mon' => 1, 'tue' => 1, 'wen' => 1,
                    'thu' => $worksThursday ? 1 : 0, 'fri' => 0, 'sat' => 1,
                    'created_at' => now(), 'updated_at' => now(),
                ]);

                $days = ['sun', 'mon', 'tue', 'wen', 'sat'];
                if ($worksThursday) $days[] = 'thu';
                DB::table('doctor_available_slots')->where('doctor_id', $doctorId)->whereNotIn('day', $days)->delete();
                foreach ($days as $day) {
                    DB::table('doctor_available_slots')->updateOrInsert(
                        ['doctor_id' => $doctorId, 'day' => $day],
                        ['start_time' => $from, 'end_time' => $to, 'created_at' => now(), 'updated_at' => now()]
                    );
                }

                DB::table('expert_available_slots')->updateOrInsert(['expert_id' => $user->id], [
                    'from' => $from, 'to' => $to, 'is_deleted' => 0,
                    'created_at' => now(), 'updated_at' => now(),
                ]);
            }
        });

        $this->command?->info('تم إنشاء طبيب وحساب ودوام لكل عيادة. كلمة المرور: '.self::PASSWORD);
    }
}
