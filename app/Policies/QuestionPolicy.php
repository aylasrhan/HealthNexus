<?php

namespace App\Policies;

use App\Models\back\Question;
use App\Models\User;

class QuestionPolicy
{
    public function view(User $user, Question $question): bool
    {
        return $this->isStaff($user) || (int) $question->user_id === (int) $user->id
            || $this->doctorSection($user) === (int) $question->section;
    }

    public function update(User $user, Question $question): bool
    {
        return $this->isStaff($user) || $this->doctorSection($user) === (int) $question->section
            || ((int) $question->user_id === (int) $user->id && !$question->answer);
    }

    public function delete(User $user, Question $question): bool
    {
        return $this->isStaff($user) || ((int) $question->user_id === (int) $user->id && !$question->answer);
    }

    private function isStaff(User $user): bool
    {
        return $user->hasSystemRole('admin', 'super_admin', 'reception', 'receptionist', 'secretary');
    }

    private function doctorSection(User $user): int
    {
        return $user->hasSystemRole('doctor') ? (int) $user->doctor?->subgrp : 0;
    }
}
