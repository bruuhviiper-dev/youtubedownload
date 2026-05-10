<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessVideoDownload;
use App\Models\Download;
use App\Services\YtDlpService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Database\QueryException;

class DownloadController extends Controller
{
    public function __construct(
        private YtDlpService $ytdlp
    ) {}

    /**
     * Show the main page.
     */
    public function index()
    {
        return view('welcome');
    }

    /**
     * Parse a YouTube URL and return video info + available formats.
     */
    public function parse(Request $request): JsonResponse
    {
        $request->validate([
            'url' => 'required|url',
        ]);

        $url = $request->input('url');

        // Validate YouTube URL
        if (!$this->ytdlp->isValidYoutubeUrl($url)) {
            return response()->json([
                'success' => false,
                'message' => 'URL inválida. Insira uma URL válida do YouTube.',
            ], 422);
        }

        try {
            $info = $this->ytdlp->getInfo($url);
            $metadata = $this->ytdlp->extractMetadata($info);
            $formats = $this->ytdlp->getAvailableFormats($info);

            return response()->json([
                'success' => true,
                'video' => $metadata,
                'formats' => $formats,
            ]);
        } catch (\Throwable $e) {
            Log::error('Erro ao analisar URL: ' . $e->getMessage());
            
            // Se for erro de banco de dados (ex: log), retorna mensagem limpa
            if ($e instanceof QueryException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ocorreu um erro interno de conexão. Tente novamente mais tarde.',
                ], 500);
            }

            return response()->json([
                'success' => false,
                'message' => 'Erro técnico (Railway): ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Start a download for the specified format.
     */
    public function download(Request $request): JsonResponse
    {
        $request->validate([
            'video_id' => 'required|string|max:20',
            'title' => 'required|string|max:255',
            'thumbnail' => 'nullable|url',
            'duration' => 'nullable|integer',
            'format_id' => 'required|string',
            'quality' => 'required|string',
            'type' => 'required|in:video,audio',
        ]);

        try {
            $extension = $request->input('type') === 'audio' ? 'mp3' : 'mp4';

            $download = Download::create([
                'video_id' => $request->input('video_id'),
                'title' => $request->input('title'),
                'thumbnail' => $request->input('thumbnail'),
                'duration' => $request->input('duration'),
                'format_id' => $request->input('format_id'),
                'quality' => $request->input('quality'),
                'extension' => $extension,
                'status' => 'pending',
                'progress' => 0,
            ]);

            // Dispatch the download job
            ProcessVideoDownload::dispatch($download);

            return response()->json([
                'success' => true,
                'download_id' => $download->id,
                'message' => 'Download iniciado!',
            ]);
        } catch (\Throwable $e) {
            Log::error('Erro ao iniciar download: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar sua solicitação de download. Tente novamente.',
            ], 500);
        }
    }

    /**
     * Check the status of a download.
     */
    public function status(string $id): JsonResponse
    {
        try {
            $download = Download::findOrFail($id);

            return response()->json([
                'id' => $download->id,
                'status' => $download->status,
                'progress' => $download->progress,
                'title' => $download->title,
                'quality' => $download->quality,
                'file_size' => $download->formatted_file_size,
                'error_message' => $download->error_message,
            ]);
        } catch (\Throwable $e) {
            Log::error('Erro ao verificar status: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Não foi possível verificar o status do download.',
            ], 500);
        }
    }

    /**
     * Stream the downloaded file to the user.
     */
    public function file(string $id)
    {
        try {
            $download = Download::findOrFail($id);

            if (!$download->isCompleted() || !$download->file_path || !file_exists($download->file_path)) {
                abort(404, 'Arquivo não encontrado.');
            }

            $safeName = preg_replace('/[^a-zA-Z0-9_\-\. ]/', '_', $download->title);
            $filename = $safeName . '.' . $download->extension;

            return response()->streamDownload(function () use ($download) {
                $stream = fopen($download->file_path, 'rb');
                while (!feof($stream)) {
                    echo fread($stream, 8192);
                    flush();
                }
                fclose($stream);
            }, $filename, [
                'Content-Type' => $download->extension === 'mp3' ? 'audio/mpeg' : 'video/mp4',
                'Content-Length' => $download->file_size,
            ]);
        } catch (\Throwable $e) {
            Log::error('Erro ao baixar arquivo: ' . $e->getMessage());
            abort(500, 'Erro ao processar o arquivo para download.');
        }
    }
}
