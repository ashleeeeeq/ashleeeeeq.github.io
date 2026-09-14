<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Donation Receipt</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            color: #111;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 24px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 12px;
            margin-bottom: 18px;
        }

        .logo {
            font-weight: 700;
            color: #0f172a;
        }

        .meta {
            text-align: right;
            font-size: 12px;
            color: #6b7280;
        }

        .section {
            margin-bottom: 14px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            padding: 8px 6px;
            border: 1px solid #e5e7eb;
        }

        .total {
            font-weight: 700;
            font-size: 18px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="logo">FAIRALL</div>
            <div class="meta">
                Receipt #: {{ $donation->receipt_number }}<br>
                Date: {{ $donation->transaction_date?->format('Y-m-d') ?? $donation->created_at->format('Y-m-d') }}
            </div>
        </div>

        <div class="section">
            <strong>Donor Details</strong><br>
            @if ($donation->donor)
                {{ $donation->donor->display_name }}<br>
                {{ optional($donation->donor->user)->email }}
            @else
                Anonymous donor
            @endif
        </div>

        <div class="section">
            <strong>Donation details</strong>
            <table class="table" style="margin-top:8px;">
                <thead>
                    <tr>
                        <th>Description</th>
                        <th>Program</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            @php
                                $receiptDescription = 'One-time Donation';

                                if ($donation->subscription) {
                                    $plan = data_get($donation->subscription->metadata, 'subscription_plan');
                                    $planName = is_array($plan) ? $plan['name'] ?? null : null;

                                    if ($planName) {
                                        $receiptDescription = $planName;
                                    } elseif ($donation->subscription->plan_id) {
                                        $receiptDescription = 'Subscription ' . $donation->subscription->plan_id;
                                    } else {
                                        $receiptDescription = $donation->description ?? 'Subscription';
                                    }
                                }

                                if ($donation->donation_type == 'in-kind') {
                                    $receiptDescription = 'In-Kind Donation';
                                }
                            @endphp

                            {{ $receiptDescription }}
                        </td>
                        <td>{{ $donation->program?->program_name ?? 'No program' }}</td>
                        <td>{{ number_format($donation->amount, 2) }}
                            {{ $donation->currency ?? config('services.paypal.currency', 'USD') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="section">
            <div class="total">Total: {{ number_format($donation->amount, 2) }}
                {{ $donation->currency ?? config('services.paypal.currency', 'USD') }}</div>
        </div>

        <div class="section" style="font-size:12px;color:#6b7280;">
            @if ($donation->donation_type == 'in-kind')
                Thank you for supporting Fairplay for All Foundation. The amount listed in this receipt is the assessed
                value of your in-kind donation.
            @elseif($donation->subscription)
                Thank you for supporting Fairplay for All Foundation. You have subscribed to a recurring donation. To
                cancel your subscription, you can visit your PayPal account or sign in as a donor.
            @else
                Thank you for supporting Fairplay for All Foundation. This receipt is issued for your donation.
            @endif
        </div>
    </div>
</body>

</html>
