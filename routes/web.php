<?php

use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PaystackController;
use App\Models\Plan;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Home', [
        // Only chat-widget is actually sellable right now, so the homepage
        // pricing section shows exactly those plans — not the older
        // universal ones, which predate service-scoping and aren't part of
        // what's being sold today.
        'plans' => Plan::active()
            ->where('service', 'chat-widget')
            ->get(['name', 'slug', 'amount', 'description', 'features', 'is_popular']),
    ]);
});

//services
Route::get('/services', fn() => inertia('Services/Index'))->name('services');
Route::get('/services/chat-widget', fn() => inertia('Services/ChatWidget'));
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

//company
Route::get('/about', fn() => inertia('About'))->name('about');
Route::get('/contact', fn() => inertia('Contact'))->name('contact');
Route::get('/register', fn() => inertia('Register'))->name('register');
Route::get('/privacy-policy', fn() => inertia('PrivacyPolicy'))->name('privacy-policy');
Route::get('/terms-of-service', fn() => inertia('TermsOfService'))->name('terms-of-service');

// Paystack redirects the browser here after checkout (success or failure).
Route::get('/billing/callback', [PaystackController::class, 'callback'])->name('paystack.callback');

Route::get('/billing/invoices/{subscription}', [InvoiceController::class, 'download'])
    ->middleware('auth')
    ->name('invoices.download');
