<?php

namespace App\Jobs;

use App\Models\Download;
use App\Services\YtDlpService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProcessVideoDownload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 2;

    /**
     * The maximum number of seconds the job can run.
     */
    public int $timeout = 600;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Download $download
    ) {}

    /**
     * Execute the job.
     */
    public function handle(YtDlpService $ytdlp): void
    {
        $download = $this->download;

        try {
            // Update status to processing
            $download->update([
                'status' => 'processing',
                'progress' => 10,
            ]);

            // Build output path
            $safeName = preg_replace('/[^a-zA-Z0-9_-]/', '_', substr($download->title, 0, 50));
            $extension = $download->extension ?? 'mp4';
            $filename = $download->video_id . '_' . $safeName . '.' . $extension;
            $outputDir = storage_path('app/downloads');

            // Ensure downloads directory exists
            if (!is_dir($outputDir)) {
                mkdir($outputDir, 0755, true);
            }

            $outputPath = $outputDir . DIRECTORY_SEPARATOR . $filename;

            $download->update(['progress' => 25]);

            // Determine type
            $type = str_contains($download->quality ?? '', 'kbps') ? 'audio' : 'video';

            // Reconstruct URL
            $url = 'https://www.youtube.com/watch?v=' . $download->video_id;

            // Perform download
            $ytdlp->download($url, $download->format_id, $outputPath, $type);

            $download->update(['progress' => 80]);

            // Find the actual output file (yt-dlp might change extension)
            $actualFile = $this->findOutputFile($outputDir, $download->video_id);

            if (!$actualFile) {
                throw new \RuntimeException('Arquivo de saída não encontrado após download.');
            }

            $fileSize = filesize($actualFile);

            // Update download record
            $download->update([
                'status' => 'completed',
                'progress' => 100,
                'file_path' => $actualFile,
                'file_size' => $fileSize,
            ]);

            Log::info('Download completed', [
                'id' => $download->id,
                'video_id' => $download->video_id,
                'file' => $actualFile,
                'size' => $fileSize,
            ]);

        } catch (\Throwable $e) {
            Log::error('Download job failed', [
                'id' => $download->id,
                'error' => $e->getMessage(),
            ]);

            $friendlyMessage = $this->sanitizeErrorMessage($e->getMessage());

            $download->update([
                'status' => 'failed',
                'progress' => 0,
                'error_message' => $friendlyMessage,
            ]);
        }
    }

    /**
     * Sanitize error message for the end user.
     */
    private function sanitizeErrorMessage(string $message): string
    {
        if (str_contains($message, 'SQLSTATE') || str_contains($message, 'Access denied')) {
            return 'Ocorreu um erro de conexão com o servidor. Tente novamente mais tarde.';
        }
        
        if (str_contains($message, 'yt-dlp')) {
            return 'Não foi possível baixar este vídeo. O YouTube pode estar bloqueando a requisição.';
        }

        return substr($message, 0, 500);
    }

    /**
     * Find the output file matching the video ID.
     */
    private function findOutputFile(string $dir, string $videoId): ?string
    {
        $files = glob($dir . DIRECTORY_SEPARATOR . $videoId . '_*');

        if (empty($files)) {
            return null;
        }

        // Return the most recently modified file
        usort($files, fn($a, $b) => filemtime($b) - filemtime($a));

        return $files[0];
    }

    /**
     * Handle a job failure.
     */
    public function failed(?\Throwable $exception): void
    {
        $message = $exception ? $this->sanitizeErrorMessage($exception->getMessage()) : 'Erro interno no processamento.';
        
        $this->download->update([
            'status' => 'failed',
            'progress' => 0,
            'error_message' => $message,
        ]);
    }
}
