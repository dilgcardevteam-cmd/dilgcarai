<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Notebook;
use App\Models\Source;
use App\Models\User;

class ActivityLogger
{
    /**
     * Persist an activity log entry.
     */
    public function log(
        ?User $user,
        string $action,
        string $description,
        ?Notebook $notebook = null,
        ?Source $source = null,
        array $properties = [],
        ?string $ipAddress = null,
        ?string $userAgent = null,
    ): ActivityLog {
        return ActivityLog::create([
            'user_id' => $user?->id,
            'notebook_id' => $notebook?->id,
            'source_id' => $source?->id,
            'action' => $action,
            'description' => $description,
            'properties' => $properties,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'created_at' => now(),
        ]);
    }
}
