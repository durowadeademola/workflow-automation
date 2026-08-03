<?php

use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\MarketingTrackingController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\PaystackController;
use App\Models\Plan;
use App\Models\Review;
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
            ->get(['id', 'name', 'slug', 'amount', 'promo_price', 'promo_ends_at', 'yearly_discount_percent', 'description', 'features', 'is_popular', 'appointment_limit', 'lead_limit', 'faq_limit']),
        // Real client-submitted reviews, approved by an admin and explicitly
        // marked to show here — never anything fabricated.
        'reviews' => Review::featured()
            ->latest()
            ->get(['name', 'job_title', 'company', 'location', 'rating', 'description']),
        // One 5-star featured review for the closing CTA banner — null (and
        // the banner just omits that block) until a real one exists.
        'ctaReview' => Review::featured()
            ->where('rating', 5)
            ->latest()
            ->first(['name', 'job_title', 'company', 'location', 'rating', 'description']),
    ]);
});

//services
Route::get('/services', fn() => inertia('Services/Index'))->name('services');
Route::get('/services/chat-widget', fn() => inertia('Services/ChatWidget'));
Route::get('/services/marketing-automation', fn() => inertia('Services/MarketingAutomation', [
    // Scoped to its own service — the homepage's #pricing section is
    // hardcoded to chat-widget plans, which would be wrong here.
    'plans' => Plan::active()
        ->where('service', 'marketing-automation')
        ->get(['id', 'name', 'slug', 'amount', 'promo_price', 'promo_ends_at', 'yearly_discount_percent', 'description', 'features', 'is_popular', 'contact_limit', 'journey_limit', 'email_send_limit']),
]));
// Marketing journey email tracking/unsubscribe — deliberately unauthenticated,
// keyed only by each send's own unguessable tracking_token (see
// MarketingTrackingController and App\Workflow\Steps\Marketing\SendEmailStep).
Route::get('/marketing/open/{token}.png', [MarketingTrackingController::class, 'open'])->name('marketing.track.open');
Route::get('/marketing/click/{token}', [MarketingTrackingController::class, 'click'])->name('marketing.track.click');
Route::get('/marketing/unsubscribe/{token}', [MarketingTrackingController::class, 'unsubscribe'])->name('marketing.unsubscribe');

// Newsletter unsubscribe — same unauthenticated, tracking_token-only pattern
// as the marketing routes above, just resolving through NewsletterSend
// instead (see NewsletterController).
Route::get('/newsletter/unsubscribe/{token}', [NewsletterController::class, 'unsubscribe'])->name('newsletter.unsubscribe');

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
