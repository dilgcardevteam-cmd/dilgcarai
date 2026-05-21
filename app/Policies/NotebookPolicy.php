<?php

namespace App\Policies;

use App\Models\Notebook;
use App\Models\User;

class NotebookPolicy
{
    /**
     * Perform pre-authorization checks.
     */
    public function before(?User $user, string $ability): bool|null
    {
        return $user?->isAdmin() ? true : null;
    }

    /**
     * Determine whether the user can view any notebooks.
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the notebook.
     */
    public function view(?User $user, Notebook $notebook): bool
    {
        return $notebook->isAccessibleBy($user);
    }

    /**
     * Determine whether the user can create notebooks.
     */
    public function create(?User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the notebook.
     */
    public function update(?User $user, Notebook $notebook): bool
    {
        return $notebook->canManage($user);
    }

    /**
     * Determine whether the user can delete the notebook.
     */
    public function delete(?User $user, Notebook $notebook): bool
    {
        return true;
    }

    /**
     * Determine whether the user can share the notebook.
     */
    public function share(?User $user, Notebook $notebook): bool
    {
        return false;
    }

    /**
     * Determine whether the user can upload a source to the notebook.
     */
    public function uploadSource(?User $user, Notebook $notebook): bool
    {
        return $notebook->canManage($user);
    }
}
