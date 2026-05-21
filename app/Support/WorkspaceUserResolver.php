<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Str;

class WorkspaceUserResolver
{
    /**
     * Resolve the hidden owner used for single-workspace notebooks.
     */
    public function resolve(): User
    {
        return User::query()->updateOrCreate(
            ['email' => 'workspace@notegov.local'],
            [
                'name' => 'Shared Workspace',
                'role' => User::ROLE_ADMIN,
                'job_title' => 'Workspace Owner',
                'office' => 'DILG Knowledge Operations',
                'password' => Str::random(40),
            ],
        );
    }
}
