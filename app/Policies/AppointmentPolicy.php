<?php

namespace App\Policies;

use App\Models\User;
use App\Models\back\Appointment;

class AppointmentPolicy
{
    public function view(User $user, Appointment $appointment): bool
    {
        return $this->isPrivileged($user) || $this->ownsAppointment($user, $appointment);
    }

    public function update(User $user, Appointment $appointment): bool
    {
        return $this->isPrivileged($user) || $this->ownsAppointment($user, $appointment);
    }

    public function cancel(User $user, Appointment $appointment): bool
    {
        return $this->isPrivileged($user) || $this->ownsAppointment($user, $appointment);
    }

    public function accept(User $user, Appointment $appointment): bool
    {
        return $this->isPrivileged($user) || $this->isAssignedDoctor($user, $appointment);
    }

    public function delete(User $user, Appointment $appointment): bool
    {
        return $this->isPrivileged($user) || $this->ownsAppointment($user, $appointment);
    }

    private function ownsAppointment(User $user, Appointment $appointment): bool
    {
        return (int) $appointment->appointment_for === (int) $user->id
            || $this->isAssignedDoctor($user, $appointment);
    }

    private function isAssignedDoctor(User $user, Appointment $appointment): bool
    {
        $doctorId = $user->doctor?->id;

        return (int) $appointment->appointment_with === (int) $user->id
            || ($doctorId && (int) $appointment->appointment_with === (int) $doctorId);
    }

    private function isPrivileged(User $user): bool
    {
        return $user->hasSystemRole('admin', 'super_admin', 'reception', 'receptionist', 'secretary');
    }
}
