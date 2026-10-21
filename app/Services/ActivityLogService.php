<?php

namespace App\Services;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Log;

class ActivityLogService
{
    /**
     * Log user activity
     */
    public function log(string $action, string $description, ?Authenticatable $user = null, array $context = []): void
    {
        $user = $user ?? auth()->user();

        Log::channel('activity')->info($action, [
            'user_id' => $user?->getAuthIdentifier(),
            'user_type' => $user ? get_class($user) : null,
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'context' => $context,
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Log data creation
     */
    public function logCreate(string $model, $record, ?Authenticatable $user = null): void
    {
        $this->log(
            'create',
            "Created {$model}",
            $user,
            [
                'model' => $model,
                'record_id' => $record->id ?? null,
            ]
        );
    }

    /**
     * Log data update
     */
    public function logUpdate(string $model, $record, ?Authenticatable $user = null, array $changes = []): void
    {
        $this->log(
            'update',
            "Updated {$model}",
            $user,
            [
                'model' => $model,
                'record_id' => $record->id ?? null,
                'changes' => $changes,
            ]
        );
    }

    /**
     * Log data deletion
     */
    public function logDelete(string $model, $record, ?Authenticatable $user = null): void
    {
        $this->log(
            'delete',
            "Deleted {$model}",
            $user,
            [
                'model' => $model,
                'record_id' => $record->id ?? null,
            ]
        );
    }

    /**
     * Log file upload
     */
    public function logFileUpload(string $model, $record, string $filename, ?Authenticatable $user = null): void
    {
        $this->log(
            'file_upload',
            "Uploaded file to {$model}",
            $user,
            [
                'model' => $model,
                'record_id' => $record->id ?? null,
                'filename' => $filename,
            ]
        );
    }

    /**
     * Log export operation
     */
    public function logExport(string $type, array $filters = [], ?Authenticatable $user = null): void
    {
        $this->log(
            'export',
            "Exported {$type}",
            $user,
            [
                'type' => $type,
                'filters' => $filters,
            ]
        );
    }

    /**
     * Log login
     */
    public function logLogin(?Authenticatable $user = null, bool $success = true): void
    {
        $this->log(
            $success ? 'login_success' : 'login_failed',
            $success ? 'User logged in' : 'Login attempt failed',
            $user,
            [
                'email' => request()->input('email'),
            ]
        );
    }

    /**
     * Log logout
     */
    public function logLogout(?Authenticatable $user = null): void
    {
        $this->log('logout', 'User logged out', $user);
    }

    /**
     * Log admin action (approve/reject user)
     */
    public function logAdminAction(string $action, $target, ?Authenticatable $user = null): void
    {
        $this->log(
            'admin_action',
            "Admin {$action}",
            $user,
            [
                'action' => $action,
                'target_type' => get_class($target),
                'target_id' => $target->id ?? null,
            ]
        );
    }
}
