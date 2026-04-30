<?php

use App\Http\Controllers\API\AIAgentController;
use App\Http\Controllers\API\CustomerController;
use App\Http\Controllers\API\DomainController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\ScanController;
use App\Http\Controllers\API\VulnerabilityController;
use Illuminate\Support\Facades\Route;


Route::post('/ai', [AIAgentController::class, 'insights'])->name('ai');
Route::post('/order', [OrderController::class, 'store'])->name('order');
Route::post('/customer', [CustomerController::class, 'store'])->name('customer');
Route::get('/domains', [DomainController::class, 'index'])->middleware('auth:sanctum')->name('domains');
Route::post('/vulnerabilities', [VulnerabilityController::class, 'store'])->middleware('auth:sanctum')->name('vulnerabilities.store');
Route::post('/scan', [ScanController::class, 'trigger'])->middleware('auth:sanctum');
Route::get('/scan-results/{domain}', [ScanController::class, 'results'])->middleware('auth:sanctum');
