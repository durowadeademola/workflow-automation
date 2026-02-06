<?php

use App\Http\Controllers\API\AIAgentController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\CustomerController;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/ai', [AIAgentController::class, 'insights'])->name('ai');

Route::post('/order',[OrderController::class, 'store'])->name('order');

Route::post('/customer', [CustomerController::class, 'store'])->name('customer');
