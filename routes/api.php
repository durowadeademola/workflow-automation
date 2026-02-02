<?php

use App\Http\Controllers\API\AIAgentController;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/ai-insights', [AIAgentController::class, 'insights'])->name('ai.insights');

Route::post('/external-order', function (Request $request) {
    $order = Order::create($request->all());

    return response()->json(['success' => true]);
})->name('external.order');

Route::post('/external-customer', function (Request $request) {
    $customer = Customer::create($request->all());

    return response()->json(['success' => true]);
})->name('external.customer');
