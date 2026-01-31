<?php

use App\Http\Controllers\API\AIAgentController;
use Illuminate\Support\Facades\Route;

Route::post('/ai-insights', [AIAgentController::class, 'insights'])->name('ai.insights');
