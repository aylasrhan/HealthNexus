<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use RuntimeException;
use Spatie\Permission\Models\Role;

class DoctorSimulationSeeder extends Seeder
{
    private const PASSWORD = 'Demo@12345';

    public function run(): void
    {
        $clinic = DB::table('gnr_m_clinics')->where('act', 1)->first()
            ?? DB::table('gnr_m_clinics')->first();
        if (!$clinic) {
            throw new RuntimeException('يجب إضافة عيادة واحدة على الأقل قبل تشغيل DoctorSimulationSeeder.');
        }

        $doctorRole = Role::findOrCreate('doctor', 'web');
        $patientRole = Role::findOrCreate('patient', 'web');
        $cityId = (int) (DB::table('gnr_m_cities')->value('id') ?? 0);
        $areaId = (int) (DB::table('gnr_m_areas')->value('id') ?? 0);
        $diagnoses = DB::table('cln_m_icd10')->orderBy('id')->limit(2)->get(['id', 'name_ar']);

        DB::transaction(function () use ($clinic, $doctorRole, $patientRole, $cityId, $areaId, $diagnoses) {
            $doctorUser = $this->upsertUser(
                'demo.doctor@healthnexus.test',
                'د. سامر الحكيم',
                'doctor',
                $doctorRole
            );

            $doctorId = DB::table('doctors')->where('user_id', $doctorUser->id)->value('id');
            $doctorData = [
                'act' => 1,
                'famous' => 1,
                'name_ar' => 'د. سامر الحكيم',
                'from_time' => '09:00:00',
                'to_time' => '15:00:00',
                'slot_time' => 30,
                'phone_number' => '0999000100',
                'subgrp' => (string) $clinic->id,
                'sex' => 1,
                'specialization_ar' => 'طب أسرة',
                'updated_at' => now(),
            ];
            if ($doctorId) {
                DB::table('doctors')->where('id', $doctorId)->update($doctorData);
            } else {
                $doctorId = DB::table('doctors')->insertGetId($doctorData + [
                    'user_id' => $doctorUser->id,
                    'created_at' => now(),
                ]);
            }

            $patients = [
                ['email' => 'demo.patient1@healthnexus.test', 'first' => 'ياسمين', 'last' => 'الخطيب', 'sex' => 2, 'birth' => '1998-04-12', 'mobile' => '0999000201', 'first_visit' => true],
                ['email' => 'demo.patient2@healthnexus.test', 'first' => 'عمر', 'last' => 'العلي', 'sex' => 1, 'birth' => '1989-09-23', 'mobile' => '0999000202', 'first_visit' => true],
                ['email' => 'demo.patient3@healthnexus.test', 'first' => 'ريم', 'last' => 'المصري', 'sex' => 2, 'birth' => '1984-02-08', 'mobile' => '0999000203', 'first_visit' => false],
                ['email' => 'demo.patient4@healthnexus.test', 'first' => 'كنان', 'last' => 'الحموي', 'sex' => 1, 'birth' => '1977-11-17', 'mobile' => '0999000204', 'first_visit' => false],
            ];
            $simulationStart = now()->seconds(0);

            foreach ($patients as $index => $data) {
                $patientUser = $this->upsertUser($data['email'], $data['first'].' '.$data['last'], 'patient', $patientRole);
                $patientId = $this->upsertPatient($patientUser->id, $data, $cityId, $areaId, $index);

                DB::table('appointments')->updateOrInsert(
                    ['appointment_for' => $patientUser->id, 'appointment_with' => $doctorId],
                    [
                        'appointment_date' => $simulationStart->toDateString(),
                        'time' => $simulationStart->copy()->addMinutes($index * 10)->format('H:i:s'),
                        'status' => 1,
                        'is_deleted' => 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );

                if (!$data['first_visit']) {
                    $this->seedPreviousVisit(
                        $patientId,
                        (int) $clinic->id,
                        (int) $doctorId,
                        $index,
                        $diagnoses->get($index - 2)?->id
                    );
                }
            }
        });

        $this->command?->info('تم إنشاء محاكاة الطبيب. كلمة مرور جميع الحسابات: '.self::PASSWORD);
    }

    private function upsertUser(string $email, string $name, string $roleName, Role $role): User
    {
        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make(self::PASSWORD),
                'roles_name' => [$roleName],
                'Status' => '1',
                'email_verified_at' => now(),
            ]
        );
        $user->syncRoles([$role]);

        return $user;
    }

    private function upsertPatient(int $userId, array $data, int $cityId, int $areaId, int $index): int
    {
        $values = [
            'f_name' => $data['first'],
            'l_name' => $data['last'],
            'ft_name' => 'محمد',
            'mother_name' => 'نورا',
            'plc_birth' => 'دمشق',
            'no' => 'DEMO-P-'.($index + 1),
            'mobile' => $data['mobile'],
            'birth_date' => $data['birth'],
            'sex' => $data['sex'],
            'phone' => $data['mobile'],
            'date' => time(),
            'blood' => ['A+', 'O+', 'B+', 'AB+'][$index],
            'p_city' => $cityId,
            'p_area' => $areaId,
            'profession' => 'بيانات محاكاة',
            'marital_status' => 2,
            'title' => $data['sex'] === 1 ? 1 : 2,
            'address' => 'عنوان تجريبي — دمشق',
        ];

        $patientId = DB::table('gnr_m_patients')->where('user_id', $userId)->value('id');
        if ($patientId) {
            DB::table('gnr_m_patients')->where('id', $patientId)->update($values);
            return (int) $patientId;
        }

        return (int) DB::table('gnr_m_patients')->insertGetId($values + ['user_id' => $userId]);
    }

    private function seedPreviousVisit(int $patientId, int $clinicId, int $doctorId, int $index, ?int $diagnosisId): void
    {
        $marker = '[DOCTOR_SIMULATION_PREVIOUS_VISIT_'.$index.']';
        $visitId = DB::table('cln_x_visits')->where('patient', $patientId)->where('note', $marker)->value('id');
        $visitData = [
            'clinic' => $clinicId,
            'type' => 1,
            'd_start' => Carbon::today()->subDays(30 + $index)->timestamp,
            'status' => 1,
            'note' => $marker,
            'sub_status' => 0,
            'new_pat' => 0,
        ];
        if ($visitId) {
            DB::table('cln_x_visits')->where('id', $visitId)->update($visitData);
        } else {
            $visitId = DB::table('cln_x_visits')->insertGetId($visitData + ['patient' => $patientId]);
        }

        $sections = [
            'cln_x_prev_com' => 'صداع متكرر منذ ثلاثة أيام.',
            'cln_x_prev_str' => 'بدأت الأعراض تدريجيًا مع الإجهاد.',
            'cln_x_prev_cln' => 'المريض بحالة عامة جيدة والفحص السريري مستقر.',
            'cln_x_prev_not' => 'متابعة بعد شهر مع الالتزام بالتعليمات.',
        ];
        foreach ($sections as $table => $value) {
            DB::table($table)->updateOrInsert(
                ['visit' => $visitId, 'patient' => $patientId, 'doc' => $doctorId],
                ['val' => $value]
            );
        }

        if ($diagnosisId) {
            DB::table('cln_x_prev_icd10')->updateOrInsert(
                ['visit' => $visitId, 'patient' => $patientId, 'doc' => $doctorId, 'opr_id' => $diagnosisId],
                []
            );
        }
    }
}
