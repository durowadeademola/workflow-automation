<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function download(Request $request, Subscription $subscription)
    {
        $user = $request->user();

        abort_unless($user && $user->client_id === $subscription->client_id, 403);
        abort_unless($subscription->start_date !== null, 404, 'No invoice available for this subscription.');

        $pdf = Pdf::loadView('invoices.subscription', ['subscription' => $subscription]);

        return $pdf->download("invoice-{$subscription->paystack_reference}.pdf");
    }
}
