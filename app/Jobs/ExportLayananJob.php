<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ExportLayananJob implements ShouldQueue
{
    use Queueable;

    public $timeout = 300; // 5 minutes

    public $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public User $user,
        public array $filters = [],
        public string $exportClass = ''
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $filename = 'layanan-export-'.now()->format('Y-m-d-His').'.xlsx';
            $path = 'exports/'.$filename;

            // Export to storage
            if (class_exists($this->exportClass)) {
                Excel::store(new $this->exportClass($this->filters), $path, 'local');

                Log::channel('activity')->info('Export completed', [
                    'user_id' => $this->user->id,
                    'filename' => $filename,
                    'filters' => $this->filters,
                ]);

                // TODO: Send notification to user
                // $this->user->notify(new ExportReadyNotification($path));
            }
        } catch (\Exception $e) {
            Log::error('Export job failed', [
                'user_id' => $this->user->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Export job failed permanently', [
            'user_id' => $this->user->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
