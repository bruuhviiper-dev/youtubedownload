<?php

use App\Http\Controllers\DownloadController;
use Illuminate\Support\Facades\Route;

// Redirect root to current locale if needed, or just serve the index
Route::get('/', [DownloadController::class, 'index'])->name('home');

// Multi-language routes
Route::group(['prefix' => '{locale}', 'where' => ['locale' => 'en|pt|es']], function () {
    Route::get('/', [DownloadController::class, 'index'])->name('home.locale');
    Route::get('/privacy-policy', fn() => view('pages.policy'))->name('policy');
    Route::get('/terms-of-service', fn() => view('pages.terms'))->name('terms');
    Route::get('/dmca', fn() => view('pages.dmca'))->name('dmca');
});
