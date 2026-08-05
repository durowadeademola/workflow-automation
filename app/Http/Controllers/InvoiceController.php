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

    /**
     * A receipt is only meaningful once a subscription actually activated —
     * same gate as the invoice above, plus it never makes sense for a
     * subscription that never had money change hands (a plan switch fully
     * covered by credit, with nothing charged).
     */
    public function downloadReceipt(Request $request, Subscription $subscription)
    {
        $user = $request->user();

        abort_unless($user && $user->client_id === $subscription->client_id, 403);
        abort_unless($subscription->start_date !== null, 404, 'No receipt available for this subscription.');

        $pdf = Pdf::loadView('invoices.receipt', ['subscription' => $subscription]);

        return $pdf->download("receipt-{$subscription->paystack_reference}.pdf");
    }
}
