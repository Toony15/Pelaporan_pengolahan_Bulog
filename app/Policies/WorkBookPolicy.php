<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\User;
use App\Models\WorkBook;

class WorkBookPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // daftar difilter di controller sesuai peran
    }

    public function view(User $user, WorkBook $workBook): bool
    {
        return $user->role !== Role::Pic || $workBook->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->role === Role::Pic;
    }

    public function update(User $user, WorkBook $workBook): bool
    {
        return $user->role === Role::Pic && $workBook->user_id === $user->id;
    }

    public function delete(User $user, WorkBook $workBook): bool
    {
        return $this->update($user, $workBook);
    }
}