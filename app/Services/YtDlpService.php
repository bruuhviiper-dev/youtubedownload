<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use RuntimeException;

class YtDlpService
{
    /**
     * Run a yt-dlp command using shell_exec to inherit full system environment.
     */
    private function runCommand(array $args, int $timeout = 60): array
    {
        // Escape each argument for shell
        $escaped = array_map('escapeshellarg', $args);
        $command = implode(' ', $escaped);

        // Use proc_open for full control
        $descriptors = [
            0 => ['pipe', 'r'],  // stdin
            1 => ['pipe', 'w'],  // stdout
            2 => ['pipe', 'w'],  // stderr
        ];

        // Build environment with critical Windows variables for Python
        $env = [];
        // Copy all current environment variables
        foreach (getenv() as $key => $value) {
            $env[$key] = $value;
        }
        // Ensure critical vars are set
        if (empty($env['SystemRoot'])) {
            $env['SystemRoot'] = 'C:\\Windows';
        }
        if (empty($env['SYSTEMROOT'])) {
            $env['SYSTEMROOT'] = $env['SystemRoot'];
        }
        if (empty($env['TEMP'])) {
            $env['TEMP'] = sys_get_temp_dir();
        }
        if (empty($env['TMP'])) {
            $env['TMP'] = sys_get_temp_dir();
        }
        $env['PYTHONHASHSEED'] = '0';

        $process = proc_open($command, $descriptors, $pipes, null, $env);

        if (!is_resource($process)) {
            throw new RuntimeException('Falha ao iniciar processo yt-dlp.');
        }

        fclose($pipes[0]); // close stdin

        $stdout = stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);

        $exitCode = proc_close($process);

        return [
            'output' => $stdout,
            'error' => $stderr,
            'exitCode' => $exitCode,
        ];
    }

    /**
     * Get ffmpeg location args if ffmpeg exists in project root.
     */
    private function ffmpegArgs(): array
    {
        $ffmpegPath = base_path('ffmpeg.exe');
        if (file_exists($ffmpegPath)) {
            return ['--ffmpeg-location', base_path()];
        }
        return [];
    }

    /**
     * Validate that the URL is a valid YouTube URL.
     */
    public function isValidYoutubeUrl(string $url): bool
    {
        $patterns = [
            '/^(https?:\/\/)?(www\.)?youtube\.com\/watch\?v=[\w-]{11}/',
            '/^(https?:\/\/)?(www\.)?youtube\.com\/shorts\/[\w-]{11}/',
            '/^(https?:\/\/)?youtu\.be\/[\w-]{11}/',
            '/^(https?:\/\/)?(www\.)?youtube\.com\/embed\/[\w-]{11}/',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get video information (metadata + formats) from YouTube.
     */
    public function getInfo(string $url): array
    {
        $args = array_merge(
            [base_path('bin/yt-dlp'), '--dump-json', '--no-playlist', '--no-warnings'],
            $this->ffmpegArgs(),
            [$url]
        );

        $result = $this->runCommand($args, 30);

        if ($result['exitCode'] !== 0) {
            Log::error('yt-dlp getInfo failed', [
                'url' => $url,
                'error' => $result['error'],
            ]);
            throw new RuntimeException('Não foi possível obter informações do vídeo: ' . $result['error']);
        }

        $info = json_decode($result['output'], true);

        if (!$info) {
            throw new RuntimeException('Resposta inválida do yt-dlp.');
        }

        return $info;
    }

    /**
     * Extract and organize available formats from yt-dlp info.
     */
    public function getAvailableFormats(array $info): array
    {
        $formats = [];

        if (!isset($info['formats'])) {
            return $formats;
        }

        $videoFormats = [];
        $audioFormats = [];

        foreach ($info['formats'] as $fmt) {
            $formatId = $fmt['format_id'] ?? '';
            $ext = $fmt['ext'] ?? 'unknown';
            $filesize = $fmt['filesize'] ?? $fmt['filesize_approx'] ?? null;
            $vcodec = $fmt['vcodec'] ?? 'none';
            $acodec = $fmt['acodec'] ?? 'none';
            $height = $fmt['height'] ?? null;
            $tbr = $fmt['tbr'] ?? null;

            if ($vcodec === 'none' && $acodec === 'none') continue;
            if (in_array($ext, ['mhtml', 'json'])) continue;

            // Audio only
            if ($vcodec === 'none' && $acodec !== 'none') {
                $abr = $fmt['abr'] ?? $tbr ?? 0;
                $audioFormats[] = [
                    'format_id' => $formatId,
                    'type' => 'audio',
                    'quality' => round($abr) . 'kbps',
                    'ext' => $ext,
                    'filesize' => $filesize,
                    'abr' => $abr,
                ];
                continue;
            }

            // Video
            if ($height) {
                $hasAudio = $acodec !== 'none';
                $videoFormats[] = [
                    'format_id' => $formatId,
                    'type' => 'video',
                    'quality' => $height . 'p',
                    'height' => $height,
                    'ext' => $ext,
                    'filesize' => $filesize,
                    'has_audio' => $hasAudio,
                    'tbr' => $tbr,
                ];
            }
        }

        // Sort video by height desc, deduplicate
        usort($videoFormats, fn($a, $b) => $b['height'] - $a['height']);
        $seen = [];
        foreach ($videoFormats as $vf) {
            if (!isset($seen[$vf['height']])) $seen[$vf['height']] = $vf;
        }
        $videoFormats = array_values($seen);

        // Sort audio by bitrate desc, limit to 3
        usort($audioFormats, fn($a, $b) => ($b['abr'] ?? 0) - ($a['abr'] ?? 0));
        if (count($audioFormats) > 3) $audioFormats = array_slice($audioFormats, 0, 3);

        foreach ($videoFormats as $vf) {
            $formats[] = [
                'format_id' => $vf['format_id'],
                'type' => 'video',
                'quality' => $vf['quality'],
                'ext' => 'mp4',
                'filesize' => $vf['filesize'],
                'label' => 'MP4 ' . $vf['quality'],
            ];
        }

        foreach ($audioFormats as $af) {
            $formats[] = [
                'format_id' => $af['format_id'],
                'type' => 'audio',
                'quality' => $af['quality'],
                'ext' => 'mp3',
                'filesize' => $af['filesize'],
                'label' => 'MP3 ' . $af['quality'],
            ];
        }

        return $formats;
    }

    /**
     * Download a video/audio with the specified format.
     */
    public function download(string $url, string $formatId, string $outputPath, string $type = 'video'): void
    {
        $hasFfmpeg = $this->checkFfmpeg();
        $ffmpeg = $this->ffmpegArgs();

        $binary = base_path('bin/yt-dlp');
        if ($type === 'audio') {
            if ($hasFfmpeg) {
                $args = array_merge([$binary], $ffmpeg, [
                    '-f', $formatId, '-x', '--audio-format', 'mp3',
                    '-o', $outputPath, '--no-playlist', '--no-warnings', $url,
                ]);
            } else {
                $args = [$binary, '-f', $formatId, '-o', $outputPath, '--no-playlist', '--no-warnings', $url];
            }
        } else {
            if ($hasFfmpeg) {
                $args = array_merge([$binary], $ffmpeg, [
                    '-f', $formatId . '+bestaudio/best', '--merge-output-format', 'mp4',
                    '-o', $outputPath, '--no-playlist', '--no-warnings', $url,
                ]);
            } else {
                $args = [$binary, '-f', 'best[ext=mp4]/best', '-o', $outputPath, '--no-playlist', '--no-warnings', $url];
            }
        }

        $result = $this->runCommand($args, 600);

        if ($result['exitCode'] !== 0) {
            Log::error('yt-dlp download failed', [
                'url' => $url,
                'format' => $formatId,
                'error' => $result['error'],
            ]);
            throw new RuntimeException('Download falhou: ' . $result['error']);
        }
    }

    /**
     * Check if ffmpeg is available on the system.
     */
    private function checkFfmpeg(): bool
    {
        try {
            $result = $this->runCommand(['ffmpeg', '-version'], 5);
            return $result['exitCode'] === 0;
        } catch (\Throwable) {
            return false;
        }
    }

    /**
     * Extract video metadata summary from yt-dlp info.
     */
    public function extractMetadata(array $info): array
    {
        return [
            'video_id' => $info['id'] ?? '',
            'title' => $info['title'] ?? 'Sem título',
            'thumbnail' => $info['thumbnail'] ?? ($info['thumbnails'][0]['url'] ?? null),
            'duration' => $info['duration'] ?? 0,
            'channel' => $info['channel'] ?? $info['uploader'] ?? 'Desconhecido',
            'view_count' => $info['view_count'] ?? 0,
        ];
    }
}
