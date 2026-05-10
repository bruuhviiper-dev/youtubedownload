<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Models\Download;
use Carbon\Carbon;

class CleanupDownloads extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'downloads:clean';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove old downloaded videos from storage and database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting cleanup...');

        // Files older than 24 hours
        $expiryTime = Carbon::now()->subHours(24);

        // 1. Clean Database and get file IDs
        $oldDownloads = Download::where('created_at', '<', $expiryTime)->get();

        foreach ($oldDownloads as $download) {
            // Delete file from storage
            $this->info("Deleting file for: {$download->title}");
            
            // We search for any file starting with this download ID in the downloads folder
            $files = Storage::disk('local')->files('downloads');
            foreach ($files as $file) {
                if (str_contains($file, (string)$download->id)) {
                    Storage::disk('local')->delete($file);
                }
            }

            // Delete record
            $download->delete();
        }

        // 2. Extra safety: Clean orphaned files in storage/app/downloads
        $allFiles = Storage::disk('local')->files('downloads');
        foreach ($allFiles as $file) {
            $lastModified = Carbon::createFromTimestamp(Storage::disk('local')->lastModified($file));
            if ($lastModified->lessThan($expiryTime)) {
                Storage::disk('local')->delete($file);
                $this->info("Deleted orphaned file: $file");
            }
        }

        $this->info('Cleanup completed!');
    }
}
