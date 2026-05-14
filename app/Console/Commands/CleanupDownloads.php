<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Models\Download;
use Carbon\Carbon;

class CleanupDownloads extends Command
{
    
    protected $signature = 'downloads:clean';

    
    protected $description = 'Remove old downloaded videos from storage and database';

    
    public function handle()
    {
        $this->info('Starting cleanup...');

        
        $expiryTime = Carbon::now()->subHours(24);

        
        $oldDownloads = Download::where('created_at', '<', $expiryTime)->get();

        foreach ($oldDownloads as $download) {
            
            $this->info("Deleting file for: {$download->title}");
            
            
            $files = Storage::disk('local')->files('downloads');
            foreach ($files as $file) {
                if (str_contains($file, (string)$download->id)) {
                    Storage::disk('local')->delete($file);
                }
            }

            
            $download->delete();
        }

        
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
