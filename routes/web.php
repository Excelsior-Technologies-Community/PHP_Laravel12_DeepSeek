<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DeepSeekController;


// DeepSeek

Route::get('/', [DeepSeekController::class, 'showForm']);
Route::post('/process', [DeepSeekController::class, 'process'])->name('deepseek.process');