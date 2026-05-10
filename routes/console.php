<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\File;
use App\Models\Download;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your closure based console
| commands. Each closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/**
 * Comando para limpar downloads antigos (mais de 24h)
 * Rodar via: php artisan downloads:clean
 */
Artisan::command('downloads:clean', function () {
    $this->info('Iniciando limpeza de downloads antigos...');
    
    $expired = Download::where('created_at', '<', now()->subHours(24))->get();
    $count = 0;

    foreach ($expired as $download) {
        if ($download->file_path && File::exists(storage_path('app/' . $download->file_path))) {
            File::delete(storage_path('app/' . $download->file_path));
        }
        $download->delete();
        $count++;
    }

    $this->info("Limpeza concluída! {$count} registros removidos.");
})->purpose('Remove downloads older than 24 hours from disk and database');

/**
 * Agendamento Automático (Scheduler)
 */
Schedule::command('downloads:clean')->hourly();
