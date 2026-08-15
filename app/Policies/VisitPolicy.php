<?php

namespace App\Policies;

use App\Models\back\Appointment;
use App\Models\back\cln_x_visits;
use App\Models\back\gnr_m_patients;
use App\Models\User;

class VisitPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasSystemRole('admin', 'super_admin', 'reception', 'receptionist', 'secretary', 'doctor', 'patient');
    }

    public function view(User $user, cln_x_visits $visit): bool
    {
        return $this->isStaff($user)
            || (int) $visit->patient()->value('user_id') === (int) $user->id
            || $this->doctorIsAssigned($user, (int) $visit->patient);
    }

    public function createForPatient(User $user, gnr_m_patients $patient): bool
    {
        return $this->isStaff($user) || $this->doctorIsAssigned($user, (int) $patient->id);
    }

    public function update(User $user, cln_x_visits $visit): bool
    {
        return $this->isStaff($user) || $this->doctorIsAssigned($user, (int) $visit->patient);
    }

    public function writeMedicalFile(User $user, cln_x_visits $visit): bool
    {
        return $user->hasSystemRole('doctor') && $this->doctorIsAssigned($user, (int) $visit->patient);
    }

    public function delete(User $user, cln_x_visits $visit): bool
    {
        return $this->isStaff($user);
    }

    private function isStaff(User $user): bool
    {
        return $user->hasSystemRole('admin', 'super_admin', 'reception', 'receptionist', 'secretary');
    }

    private function doctorIsAssigned(User $user, int $patientId): bool
    {
        if (!$user->hasSystemRole('doctor')) {
            return false;
        }

        $patientUserId = gnr_m_patients::whereKey($patientId)->value('user_id');
        $doctorIds = array_filter([(int) $user->id, (int) $user->doctor?->id]);

        return $patientUserId && Appointment::query()
            ->where('appointment_for', $patientUserId)
            ->whereIn('appointment_with', $doctorIds)
            ->where('is_deleted', 0)
            ->exists();
    }
}
