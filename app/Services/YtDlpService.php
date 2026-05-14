<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use RuntimeException;

class YtDlpService
{
    
    private function runCommand(array $args, int $timeout = 60): array
    {
        
        $escaped = array_map('escapeshellarg', $args);
        $command = implode(' ', $escaped);

        
        $descriptors = [
            0 => ['pipe', 'r'],  
            1 => ['pipe', 'w'],  
            2 => ['pipe', 'w'],  
        ];

        
        $env = [];
        
        foreach (getenv() as $key => $value) {
            $env[$key] = $value;
        }
        
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

        fclose($pipes[0]); 

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

    
    private function cookieArgs(): array
    {
        $cookiePath = storage_path('app/cookies.txt');
        $envCookies = env('YT_COOKIES');

        if ($envCookies) {
            
            if (!file_exists($cookiePath) || file_get_contents($cookiePath) !== $envCookies) {
                if (!is_dir(storage_path('app'))) {
                    mkdir(storage_path('app'), 0755, true);
                }
                file_put_contents($cookiePath, $envCookies);
                \Illuminate\Support\Facades\Log::info('Cookies do YouTube carregados da variável de ambiente YT_COOKIES.');
            }
        }

        if (file_exists($cookiePath) && filesize($cookiePath) > 0) {
            return ['--cookies', $cookiePath];
        }
        return [];
    }

    
    private function ffmpegArgs(): array
    {
        
        return [];
    }

    
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

    
    public function getInfo(string $url): array
    {
        $isWindows = PHP_OS_FAMILY === 'Windows';
        $binaryName = $isWindows ? 'yt-dlp.exe' : 'yt-dlp';
        $binary = base_path("bin/{$binaryName}");
        
        
        if (!file_exists($binary) || filesize($binary) < 1000000) {
            Log::info('Binário yt-dlp ausente ou desatualizado. Tentando download da Versão Standalone...');
            if (!is_dir(base_path('bin'))) mkdir(base_path('bin'), 0755, true);
            
            $downloadUrl = $isWindows 
                ? 'https://github.com/yt-dlp/yt-dlp/releases/latest/download/yt-dlp.exe' 
                : 'https://github.com/yt-dlp/yt-dlp/releases/latest/download/yt-dlp_linux';
                
            shell_exec("curl -L {$downloadUrl} -o \"{$binary}\"");
            if (!$isWindows) shell_exec("chmod a+rx \"{$binary}\"");
        }

        
        Log::info('Diagnóstico yt-dlp', [
            'path' => $binary,
            'exists' => file_exists($binary),
            'is_executable' => is_executable($binary),
            'base_path' => base_path(),
        ]);

        $args = array_merge(
            [
                $binary, 
                '--dump-json', 
                '--no-playlist', 
                '--no-warnings',
                '--user-agent', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.0.0 Safari/537.36',
                '--referer', 'https://www.google.com/',
            ],
            $this->ffmpegArgs(),
            $this->cookieArgs(),
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

        
        usort($videoFormats, fn($a, $b) => $b['height'] - $a['height']);
        $seen = [];
        foreach ($videoFormats as $vf) {
            if (!isset($seen[$vf['height']])) $seen[$vf['height']] = $vf;
        }
        $videoFormats = array_values($seen);

        
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

    
    public function download(string $url, string $formatId, string $outputPath, string $type = 'video'): void
    {
        $hasFfmpeg = $this->checkFfmpeg();
        $ffmpeg = $this->ffmpegArgs();

        $isWindows = PHP_OS_FAMILY === 'Windows';
        $binaryName = $isWindows ? 'yt-dlp.exe' : 'yt-dlp';
        $binary = base_path("bin/{$binaryName}");
        $cookies = $this->cookieArgs();

        $extraArgs = [
            '--no-playlist', 
            '--no-warnings',
            '--user-agent', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.0.0 Safari/537.36',
            '--referer', 'https://www.google.com/',
        ];

        if ($type === 'audio') {
            if ($hasFfmpeg) {
                $args = array_merge([$binary], $ffmpeg, $cookies, $extraArgs, [
                    '-f', $formatId, '-x', '--audio-format', 'mp3',
                    '-o', $outputPath, $url,
                ]);
            } else {
                $args = array_merge([$binary, '-f', $formatId], $cookies, $extraArgs, ['-o', $outputPath, $url]);
            }
        } else {
            if ($hasFfmpeg) {
                $args = array_merge([$binary], $ffmpeg, $cookies, $extraArgs, [
                    '-f', $formatId . '+bestaudio/best', '--merge-output-format', 'mp4',
                    '-o', $outputPath, $url,
                ]);
            } else {
                $args = array_merge([$binary, '-f', 'best[ext=mp4]/best'], $cookies, $extraArgs, ['-o', $outputPath, $url]);
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

    
    private function checkFfmpeg(): bool
    {
        try {
            $result = $this->runCommand(['ffmpeg', '-version'], 5);
            return $result['exitCode'] === 0;
        } catch (\Throwable) {
            return false;
        }
    }

    
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
