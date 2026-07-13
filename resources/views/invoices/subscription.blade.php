<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $subscription->paystack_reference }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
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
        .invoice-title { font-size: 22px; font-weight: bold; text-align: right; color: #111827; }
        .meta-table { width: 100%; margin-bottom: 28px; }
        .meta-table td { padding: 2px 0; }
        .section-label {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #9ca3af;
            margin-bottom: 4px;
        }
        table.items {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }
        table.items th {
            background: #f3f4f6;
            text-align: left;
            padding: 10px 12px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            color: #6b7280;
        }
        table.items td {
            padding: 12px;
            border-bottom: 1px solid #e5e7eb;
        }
        table.items .amount { text-align: right; }
        .totals { width: 100%; margin-top: 8px; }
        .totals td { padding: 6px 12px; }
        .totals .label { text-align: right; color: #6b7280; }
        .totals .value { text-align: right; width: 120px; font-weight: bold; }
        .total-row td { border-top: 2px solid #111827; font-size: 15px; }
        .status-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            background: #d1fae5;
            color: #065f46;
        }
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
                <div class="invoice-title">INVOICE</div>
                <div class="muted" style="text-align: right;">#{{ $subscription->paystack_reference }}</div>
            </td>
        </tr>
    </table>

    <table class="meta-table">
        <tr>
            <td width="50%">
                <div class="section-label">Billed To</div>
                <strong>{{ $subscription->client->name ?? 'N/A' }}</strong><br>
                <span class="muted">{{ $subscription->client->email ?? '' }}</span>
            </td>
            <td width="50%" style="text-align: right;">
                <div class="section-label">Invoice Date</div>
                {{ ($subscription->start_date ?? $subscription->created_at)->format('F j, Y') }}<br><br>
                <div class="section-label">Status</div>
                <span class="status-badge">{{ ucfirst($subscription->status) }}</span>
            </td>
        </tr>
    </table>

    <table class="items">
        <thead>
            <tr>
                <th>Description</th>
                <th>Billing Period</th>
                <th class="amount">Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <strong>{{ $subscription->name }} Plan</strong><br>
                    @if($subscription->planRecord?->description)
                        <span class="muted">{{ $subscription->planRecord->description }}</span>
                    @endif
                </td>
                <td>
                    @if($subscription->start_date && $subscription->end_date)
                        {{ $subscription->start_date->format('M j, Y') }} – {{ $subscription->end_date->format('M j, Y') }}
                    @else
                        —
                    @endif
                </td>
                <td class="amount">₦{{ number_format($subscription->amount) }}</td>
            </tr>
        </tbody>
    </table>

    <table class="totals">
        <tr>
            <td class="label">Total Paid</td>
            <td class="value">₦{{ number_format($subscription->amount) }}</td>
        </tr>
    </table>

    <div class="footer">
        Thank you for your business. This invoice was generated automatically by {{ config('invoice.company_name') }}.
    </div>
</body>
</html>
