<?php

namespace App\Policies;

use App\Models\Intervention;
use App\Models\User;

class InterventionPolicy
{
    public function view(User $user, Intervention $intervention): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Intervention $intervention): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Intervention $intervention): bool
    {
        return $user->isAdmin();
    }
}
