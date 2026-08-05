<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Receipt {{ $subscription->paystack_reference ?? $subscription->id }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', 'Helvetica', 'Arial', sans-serif;
            font-size: 13px;
            color: #1f2937;
            margin: 0;
            padding: 40px;
        }
        .header {
            width: 100%;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 16px;
            margin-bottom: 24px;
        }
        .header td { vertical-align: top; }
        .company-name { font-size: 20px; font-weight: bold; color: #2563eb; }
        .muted { color: #6b7280; }
        .doc-title { font-size: 22px; font-weight: bold; text-align: right; color: #111827; }
        .meta-table { width: 100%; margin-bottom: 20px; }
        .meta-table td { padding: 2px 0; }
        .section-label {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #9ca3af;
            margin-bottom: 4px;
        }
        .paid-stamp {
            display: inline-block;
            padding: 4px 14px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: bold;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            background: #dbeafe;
            color: #1e40af;
        }
        table.details {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }
        table.details td {
            padding: 10px 12px;
            border-bottom: 1px solid #e5e7eb;
        }
        table.details td.label {
            color: #6b7280;
            width: 45%;
        }
        table.details td.value {
            text-align: right;
            font-weight: bold;
            color: #111827;
        }
        .total-box {
            margin-top: 20px;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 8px;
            padding: 16px 20px;
        }
        .total-box .label { color: #1e40af; font-size: 12px; text-transform: uppercase; letter-spacing: 0.05em; }
        .total-box .value { color: #1e3a8a; font-size: 24px; font-weight: bold; margin-top: 4px; }
        .footer {
            margin-top: 48px;
            padding-top: 16px;
            border-top: 1px solid #e5e7eb;
            font-size: 11px;
            color: #9ca3af;
            text-align: center;
        }
    </style>
</head>
<body>
    <table class="header">
        <tr>
            <td width="60%">
                <div class="company-name">{{ config('invoice.company_name') }}</div>
                <div class="muted">{{ config('invoice.company_address') }}</div>
                <div class="muted">{{ config('invoice.company_email') }}</div>
            </td>
            <td width="40%">
                <div class="doc-title">RECEIPT</div>
                <div class="muted" style="text-align: right;">#{{ $subscription->paystack_reference ?? $subscription->id }}</div>
            </td>
        </tr>
    </table>

    <table class="meta-table">
        <tr>
            <td width="50%">
                <div class="section-label">Received From</div>
                <strong>{{ $subscription->client->name ?? 'N/A' }}</strong><br>
                <span class="muted">{{ $subscription->client->email ?? '' }}</span>
            </td>
            <td width="50%" style="text-align: right;">
                <div class="section-label">Date Paid</div>
                {{ ($subscription->paystack_paid_at ?? $subscription->start_date ?? $subscription->created_at)->format('F j, Y') }}<br><br>
                <span class="paid-stamp">Paid</span>
            </td>
        </tr>
    </table>

    <table class="details">
        <tr>
            <td class="label">Service</td>
            <td class="value">{{ $subscription->serviceLabel() }}</td>
        </tr>
        <tr>
            <td class="label">Plan</td>
            <td class="value">{{ $subscription->name }}</td>
        </tr>
        <tr>
            <td class="label">Payment method</td>
            <td class="value">{{ $subscription->paystack_channel ? ucfirst($subscription->paystack_channel) : 'Account credit' }}</td>
        </tr>
        <tr>
            <td class="label">Transaction reference</td>
            <td class="value">{{ $subscription->paystack_transaction_id ?? $subscription->paystack_reference ?? '—' }}</td>
        </tr>
        @if($subscription->credit_applied > 0)
            <tr>
                <td class="label">Credit applied</td>
                <td class="value">₦{{ number_format($subscription->credit_applied) }}</td>
            </tr>
        @endif
    </table>

    <div class="total-box">
        <div class="label">Amount Paid</div>
        <div class="value">₦{{ number_format($subscription->paystack_amount_charged ?? max(0, $subscription->amount - $subscription->credit_applied)) }}</div>
    </div>

    <div class="footer">
        This receipt confirms payment was received in full. Thank you for your business — {{ config('invoice.company_name') }}.
    </div>
</body>
</html>
