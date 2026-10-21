<?php

namespace App\Filament\Traits;

use App\Services\ActivityLogService;

trait LogsActivity
{
    protected function logActivity(string $action, string $description, array $context = []): void
    {
        $service = app(ActivityLogService::class);
        $service->log($action, $description, auth()->user(), $context);
    }

    protected function logCreate($record): void
    {
        $this->logActivity('create', "Created {$this->getModelLabel()}", [
            'model' => get_class($record),
            'record_id' => $record->id ?? null,
        ]);
    }

    protected function logUpdate($record, array $changes = []): void
    {
        $this->logActivity('update', "Updated {$this->getModelLabel()}", [
            'model' => get_class($record),
            'record_id' => $record->id ?? null,
            'changes' => $changes,
        ]);
    }

    protected function logDelete($record): void
    {
        $this->logActivity('delete', "Deleted {$this->getModelLabel()}", [
            'model' => get_class($record),
            'record_id' => $record->id ?? null,
        ]);
    }

    protected function logFileUpload($record, string $filename): void
    {
        $this->logActivity('file_upload', "Uploaded file to {$this->getModelLabel()}", [
            'model' => get_class($record),
            'record_id' => $record->id ?? null,
            'filename' => $filename,
        ]);
    }

    protected function getModelLabel(): string
    {
        return static::$resource::getModelLabel() ?? 'Record';
    }
}
