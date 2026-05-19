<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DeepSeekController;

// DeepSeek Routes
Route::get('/', [DeepSeekController::class, 'showForm'])->name('deepseek.form');
Route::post('/process', [DeepSeekController::class, 'process'])->name('deepseek.process');
Route::get('/result/{id}', [DeepSeekController::class, 'showResult'])->name('deepseek.result');
Route::post('/retry/{id}', [DeepSeekController::class, 'retry'])->name('deepseek.retry');
Route::get('/delete/{id}', [DeepSeekController::class, 'deleteHistory'])->name('deepseek.delete');
Route::get('/clear-all', [DeepSeekController::class, 'clearAllHistory'])->name('deepseek.clear');
Route::get('/export', [DeepSeekController::class, 'exportHistory'])->name('deepseek.export');
Route::post('/share/{id}', [DeepSeekController::class, 'shareChat'])->name('deepseek.share');
Route::get('/shared-chat/{token}', [DeepSeekController::class, 'viewSharedChat'])->name('deepseek.shared');