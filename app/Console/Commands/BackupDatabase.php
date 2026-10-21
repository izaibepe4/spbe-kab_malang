<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class BackupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:database {--keep-days=7 : Number of days to keep backups}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup the database to storage/app/backups';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting database backup...');

        try {
            $filename = 'backup-'.now()->format('Y-m-d-His').'.sql';
            $backupPath = storage_path('app/backups');

            // Create backups directory if not exists
            if (! is_dir($backupPath)) {
                mkdir($backupPath, 0755, true);
            }

            $fullPath = $backupPath.'/'.$filename;

            // Get database configuration
            $dbHost = config('database.connections.mysql.host');
            $dbName = config('database.connections.mysql.database');
            $dbUser = config('database.connections.mysql.username');
            $dbPass = config('database.connections.mysql.password');

            // Build mysqldump command
            $command = sprintf(
                'mysqldump --host=%s --user=%s --password=%s %s > %s',
                escapeshellarg($dbHost),
                escapeshellarg($dbUser),
                escapeshellarg($dbPass),
                escapeshellarg($dbName),
                escapeshellarg($fullPath)
            );

            // Execute backup
            exec($command, $output, $returnCode);

            if ($returnCode !== 0) {
                $this->error('Backup failed!');
                Log::error('Database backup failed', ['return_code' => $returnCode]);

                return self::FAILURE;
            }

            $fileSize = filesize($fullPath);
            $this->info('Backup completed successfully!');
            $this->info("File: {$filename}");
            $this->info('Size: '.$this->formatBytes($fileSize));

            Log::info('Database backup completed', [
                'filename' => $filename,
                'size' => $fileSize,
            ]);

            // Clean old backups
            $this->cleanOldBackups((int) $this->option('keep-days'));

            return self::SUCCESS;

        } catch (\Exception $e) {
            $this->error('Backup failed: '.$e->getMessage());
            Log::error('Database backup exception', ['error' => $e->getMessage()]);

            return self::FAILURE;
        }
    }

    /**
     * Clean old backup files
     */
    private function cleanOldBackups(int $keepDays): void
    {
        $backupPath = storage_path('app/backups');
        $files = glob($backupPath.'/backup-*.sql');
        $cutoffTime = now()->subDays($keepDays)->timestamp;

        $deletedCount = 0;
        foreach ($files as $file) {
            if (filemtime($file) < $cutoffTime) {
                unlink($file);
                $deletedCount++;
            }
        }

        if ($deletedCount > 0) {
            $this->info("Cleaned {$deletedCount} old backup(s)");
        }
    }

    /**
     * Format bytes to human readable
     */
    private function formatBytes(int $bytes): string
    {
        if ($bytes < 1024) {
            return $bytes.' B';
        } elseif ($bytes < 1048576) {
            return round($bytes / 1024, 2).' KB';
        } else {
            return round($bytes / 1048576, 2).' MB';
        }
    }
}
