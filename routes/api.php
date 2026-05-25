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
// routes/web.php
Route::get('/widget-knowledge', function () {
    return response()->json([
        'content' => '
            Blueflow Automation is a Nigerian automation agency based in Benin City.
            We build AI chat widgets for businesses, WhatsApp automation, n8n workflows,
            and Laravel web applications. Our services include website AI assistants,
            lead generation automation, and custom business automation pipelines.
            Contact us on WhatsApp: 2347064706193.
            We serve businesses across Nigeria including Lagos, Abuja, and Benin City.
        '
    ]);
});

Route::get('/widget-knowledges', function () {
    return response()->json([
        'content' => '
            We are quite ready to help you automate your business processes and build AI chat widgets for your website.
            Our team of experts can create custom automation solutions tailored to your specific needs, helping you save time and increase efficiency. Whether you need a chatbot for customer support, lead generation, or any other business process, we have the skills and experience to deliver high-quality results. 
            Contact us today to learn more!
        '
    ]);
});
