<?php

use App\Http\Controllers\API\AIAgentController;
use App\Http\Controllers\API\ClientRegistrationController;
use App\Http\Controllers\API\CustomerController;
use App\Http\Controllers\API\DomainController;
use App\Http\Controllers\API\LeadController;
use App\Http\Controllers\API\NewsletterSubscriptionController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\WidgetAppointmentController;
use App\Http\Controllers\API\WidgetChatController;
use App\Http\Controllers\API\WidgetConversationController;
use App\Http\Controllers\API\WidgetFaqController;
use App\Http\Controllers\API\WidgetLeadController;
use App\Http\Controllers\API\WidgetRegistrationController;
use App\Http\Controllers\API\WorkflowTriggerController;
use App\Http\Controllers\PaystackController;
use App\Http\Controllers\ScanController;
use App\Http\Controllers\API\VulnerabilityController;
use Illuminate\Support\Facades\Route;


Route::middleware('webhook.secret')->group(function () {
    Route::post('/ai', [AIAgentController::class, 'insights'])->name('ai');
    Route::post('/order', [OrderController::class, 'store'])->name('order');
    Route::post('/customer', [CustomerController::class, 'store'])->name('customer');
    // Called by n8n only, when a visitor asks to speak with a human.
    Route::post('/widget/conversations', [WidgetConversationController::class, 'store'])->name('widget.conversations.store');
    // Called by n8n only, once the AI has collected everything needed to book an appointment.
    Route::post('/widget/appointments', [WidgetAppointmentController::class, 'store'])->name('widget.appointments.store');
    // Called by n8n only, once the AI has picked up on what a visitor is interested in.
    Route::post('/widget/lead', [WidgetLeadController::class, 'store'])->name('widget.lead.store');
    // Called by n8n only, once the AI has collected a visitor's name plus at least one contact method.
    Route::post('/widget/register', [WidgetRegistrationController::class, 'store'])->name('widget.register.store');
    // Generic entry point for any AutomationWorkflow with trigger_type=webhook.
    Route::post('/workflows/{slug}/trigger', [WorkflowTriggerController::class, 'trigger'])->name('workflows.trigger');
});

// Called directly by the embedded chat widget in the visitor's browser.
// These are anonymous by necessity, so they're locked down by the
// unguessable per-conversation session token instead of the webhook secret.
Route::middleware('throttle:30,1')->group(function () {
    Route::post('/widget/chat', [WidgetChatController::class, 'send'])->name('widget.chat');
    Route::get('/widget/history', [WidgetChatController::class, 'history'])->name('widget.history');
    Route::get('/widget/conversations/{conversation}/messages', [WidgetConversationController::class, 'messages'])->name('widget.conversations.messages');
    Route::post('/widget/conversations/{conversation}/messages', [WidgetConversationController::class, 'send'])->name('widget.conversations.send');
    Route::get('/widget/faqs', [WidgetFaqController::class, 'index'])->name('widget.faqs');
    Route::post('/widget/faqs/{faq}/view', [WidgetFaqController::class, 'recordView'])->name('widget.faqs.view');
});

Route::post('/leads', [LeadController::class, 'store'])->middleware('throttle:5,1')->name('leads.store');
Route::post('/register', [ClientRegistrationController::class, 'store'])->middleware('throttle:5,1')->name('register.store');
Route::post('/newsletter/subscribe', [NewsletterSubscriptionController::class, 'store'])->middleware('throttle:5,1')->name('newsletter.subscribe');

// Server-to-server from Paystack. Authenticated via HMAC signature, not the webhook secret.
Route::post('/paystack/webhook', [PaystackController::class, 'webhook'])->middleware('throttle:60,1')->name('paystack.webhook');
Route::get('/domains', [DomainController::class, 'index'])->middleware('auth:sanctum')->name('domains');
Route::post('/vulnerabilities', [VulnerabilityController::class, 'store'])->middleware('auth:sanctum')->name('vulnerabilities.store');
Route::post('/scan', [ScanController::class, 'trigger'])->middleware('auth:sanctum');
Route::get('/scan-results/{domain}', [ScanController::class, 'results'])->middleware('auth:sanctum');

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
