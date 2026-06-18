<?php

namespace App\Policies;

use App\Models\MedicalRecord;
use App\Models\User;

class MedicalRecordPolicy
{
    public function view(User $user, MedicalRecord $record): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, MedicalRecord $record): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, MedicalRecord $record): bool
    {
        return $user->isAdmin();
    }

    public function export(User $user, MedicalRecord $record): bool
    {
        return $user->isAdmin();
    }
}
