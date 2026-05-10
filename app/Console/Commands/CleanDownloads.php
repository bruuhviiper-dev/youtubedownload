<?php

namespace App\Console\Commands;

use App\Models\Download;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CleanDownloads extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'downloads:clean {--hours=1 : Hours after which to delete files}';

    /**
     * The console command description.
     */
    protected $description = 'Clean up old downloaded files and their database records';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $hours = (int) $this->option('hours');
        $cutoff = now()->subHours($hours);

        $downloads = Download::where('created_at', '<', $cutoff)->get();

        $deletedFiles = 0;
        $deletedRecords = 0;

        foreach ($downloads as $download) {
            // Delete the physical file
            if ($download->file_path && file_exists($download->file_path)) {
                unlink($download->file_path);
                $deletedFiles++;
            }

            // Delete the database record
            $download->delete();
            $deletedRecords++;
        }

        $this->info("Cleaned up {$deletedFiles} files and {$deletedRecords} records older than {$hours} hour(s).");

        Log::info('Downloads cleanup completed', [
            'deleted_files' => $deletedFiles,
            'deleted_records' => $deletedRecords,
            'cutoff' => $cutoff->toDateTimeString(),
        ]);

        return self::SUCCESS;
    }
}
