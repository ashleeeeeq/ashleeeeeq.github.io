<html>
<body style="font-family: Arial, Helvetica, sans-serif; color:#111;">
    <div style="max-width:640px;margin:0 auto;padding:18px;">
        <h2 style="margin-bottom:6px;color:#0f172a;">Thank you for your donation</h2>

        <p style="color:#374151;">Hello {{ $donation->donor?->display_name ?? 'Friend' }},</p>

        @if ($pdfAttached ?? true)
            <p style="color:#374151;">Thank you for your generous contribution to Fairplay for All Foundation. We've attached your donation receipt for your records.</p>
        @else
            <p style="color:#374151;">Thank you for your generous contribution to Fairplay for All Foundation.</p>
        @endif

        <ul style="color:#374151;">
            <li><strong>Amount:</strong> {{ number_format($donation->amount, 2) }} {{ $donation->currency ?? config('services.paypal.currency', 'USD') }}</li>
            <li><strong>Date:</strong> {{ $donation->transaction_date?->format('Y-m-d') ?? $donation->created_at->format('Y-m-d') }}</li>
            <li><strong>Reference:</strong> {{ $donation->gateway_reference ?? $donation->reference_number ?? $donation->receipt_number ?? '-' }}</li>
        </ul>

        @if (!($pdfAttached ?? true))
            <p style="color:#b91c1c;font-size:13px;">
                Note: Your receipt PDF could not be attached to this email.
                You can view and download your receipts anytime by logging into your donor portal.
            </p>
        @endif

        <p style="color:#374151;">If you have any questions, reply to this email.</p>

        <p style="color:#374151;">With thanks,<br>FAIRALL</p>
    </div>
</body>
</html>
