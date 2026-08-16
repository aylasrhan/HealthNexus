<?php

namespace App\Policies;

use App\Models\User;
use App\Models\back\Appointment;
use App\Models\back\gnr_m_patients;

class PatientPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasSystemRole(
            'admin', 'super_admin', 'secretary', 'reception', 'receptionist',
            'doctor', 'طبيب', 'patient', 'مريض'
        );
    }

    public function view(User $user, gnr_m_patients $patient): bool
    {
        return $this->isStaff($user)
            || (int) $patient->user_id === (int) $user->id
            || $this->doctorIsAssigned($user, $patient);
    }

    public function create(User $user): bool
    {
        return $this->isStaff($user);
    }

    public function update(User $user, gnr_m_patients $patient): bool
    {
        return $this->isStaff($user) || (int) $patient->user_id === (int) $user->id;
    }

    public function delete(User $user, gnr_m_patients $patient): bool
    {
        return $this->isStaff($user);
    }

    private function isStaff(User $user): bool
    {
        return $user->hasSystemRole('admin', 'super_admin', 'secretary', 'reception', 'receptionist');
    }

    private function doctorIsAssigned(User $user, gnr_m_patients $patient): bool
    {
        if (!$user->hasSystemRole('doctor', 'طبيب')) {
            return false;
        }

        $doctorId = $user->doctor?->id;

        return Appointment::query()
            ->where('appointment_for', $patient->user_id)
            ->whereIn('appointment_with', array_filter([$user->id, $doctorId]))
            ->where('is_deleted', 0)
            ->exists();
    }
}
