<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Route::get('/', function () {
//     return redirect('/admin/login');
// });

// Route::get('/start', function () {
//     return redirect()->away('https://forms.gle/rG4Jf1xoguD67mH26');
// });

Route::get('/', function () {
    return Inertia::render('Home');
});

//services
Route::get('/services', fn() => inertia('Services/Index'))->name('services');
Route::get('/services/whatsapp-automation', fn() => inertia('Services/WhatsappAutomation'));
Route::get('/services/crm-integration', fn() => inertia('Services/CRMIntegration'));
Route::get('/services/email-automation', fn() => inertia('Services/EmailAutomation'));
Route::get('/services/payment-automation', fn() => inertia('Services/PaymentAutomation'));
Route::get('/services/workflow-automation', fn() => inertia('Services/WorkflowAutomation'));
Route::get('/services/custom-solutions', fn() => inertia('Services/CustomSolutions'));

//industries
Route::get('/industries', fn() => inertia('Industries/Index'))->name('industries');
Route::get('/industries/healthcare', fn() => inertia('Industries/Healthcare'));
Route::get('/industries/ecommerce', fn() => inertia('Industries/Ecommerce'));
Route::get('/industries/restaurants', fn() => inertia('Industries/Restaurants'));
Route::get('/industries/hospitality', fn() => inertia('Industries/Hospitality'));
Route::get('/industries/real-estate', fn() => inertia('Industries/RealEstate'));
Route::get('/industries/professional-services', fn() => inertia('Industries/ProfessionalServices'));
