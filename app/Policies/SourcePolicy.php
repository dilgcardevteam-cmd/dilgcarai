<?php

namespace App\Policies;

use App\Models\Source;
use App\Models\User;

class SourcePolicy
{
    /**
     * Perform pre-authorization checks.
     */
    public function before(?User $user, string $ability): bool|null
    {
        return $user?->isAdmin() ? true : null;
    }

    /**
     * Determine whether the user can view the source.
     */
    public function view(?User $user, Source $source): bool
    {
        return $source->notebook->isAccessibleBy($user);
    }

    /**
     * Determine whether the user can update the source.
     */
    public function update(?User $user, Source $source): bool
    {
        return $source->notebook->canManage($user);
    }

    /**
     * Determine whether the user can delete the source.
     */
    public function delete(?User $user, Source $source): bool
    {
        return $source->notebook->canManage($user);
    }
}
