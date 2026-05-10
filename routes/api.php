<?php

use App\Http\Controllers\DownloadController;
use Illuminate\Support\Facades\Route;

Route::post('/parse', [DownloadController::class, 'parse']);
Route::post('/download', [DownloadController::class, 'download']);
Route::get('/status/{id}', [DownloadController::class, 'status']);
Route::get('/file/{id}', [DownloadController::class, 'file']);
