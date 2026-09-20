<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAIRALL - Verify Your Donor Account</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
        }
        .header {
            text-align: center;
            padding: 20px;
            background: linear-gradient(135deg, #20264F 0%, #2a3366 100%);
            color: white;
            border-radius: 10px 10px 0 0;
        }
        .logo {
            font-size: 32px;
            font-weight: bold;
            letter-spacing: 2px;
        }
        .tagline {
            font-size: 14px;
            margin-top: 5px;
            opacity: 0.8;
        }
        .content {
            padding: 30px;
            color: #333;
            line-height: 1.6;
        }
        .button {
            display: inline-block;
            padding: 14px 32px;
            background-color: #FFB800;
            color: #1a1a2e;
            text-decoration: none;
            border-radius: 50px;
            font-weight: bold;
            margin: 20px 0;
            text-align: center;
            transition: all 0.3s ease;
        }
        .button:hover {
            background-color: #ffcd4a;
            transform: scale(1.02);
        }
        .divider {
            border-top: 2px solid #FFB800;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            padding: 20px;
            font-size: 12px;
            color: #999;
            border-top: 1px solid #eee;
            background-color: #f9f9f9;
            border-radius: 0 0 10px 10px;
        }
        .info-box {
            background-color: #f5f5f5;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #FFB800;
        }
        .highlight {
            color: #20264F;
            font-weight: bold;
        }
        .donor-type {
            display: inline-block;
            padding: 4px 12px;
            background-color: #e8e8e8;
            border-radius: 20px;
            font-size: 12px;
            margin-top: 10px;
        }
        @media only screen and (max-width: 480px) {
            .container {
                width: 100%;
            }
            .content {
                padding: 20px;
            }
            .button {
                display: block;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">FAIRALL</div>
            <div class="tagline">Building a better future through generosity</div>
        </div>

        <div class="content">
            <h2 style="color: #20264F;">Welcome to FAIRALL, {{ $name }}!</h2>

            <p>Thank you for registering as a <strong>{{ ucfirst($donorType) }} Donor</strong> with FAIRALL. We're excited to have you on our mission to create positive change.</p>

            <div class="info-box">
                <strong>📋 Registration Details:</strong><br>
                <span class="highlight">Email:</span> {{ $email }}<br>
                <span class="highlight">Donor Type:</span> {{ ucfirst($donorType) }}<br>
                @if($donorType === 'organization')
                <span class="highlight">Organization:</span> {{ $organizationName ?? 'N/A' }}<br>
                @else
                <span class="highlight">Name:</span> {{ $name }}<br>
                @endif
                <span class="donor-type">🎉 New Donor</span>
            </div>

            <p>Please verify your email address to activate your donor account and start making a difference.</p>

            <div style="text-align: center;">
                <a href="{{ $verificationUrl }}" class="button">✓ Verify Email Address</a>
            </div>

            <p>This verification link will expire in <strong>60 minutes</strong>.</p>

            <p><strong>Why verify your email?</strong><br>
            • Secure your donor account<br>
            • Receive donation receipts and acknowledgements<br>
            • Track your impact and donation history<br>
            • Get updates about the causes you support</p>

            <p>If you did not create this account, no further action is required.</p>

            <div class="divider"></div>

            <p style="font-size: 14px;">
                <strong>Need help?</strong> Contact our support team at <a href="mailto:support@fairall.com" style="color: #20264F;">support@fairall.com</a>
            </p>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} FAIRALL. All rights reserved.</p>
            <p>Building a better future through generosity</p>
            <p style="font-size: 11px; margin-top: 10px;">
                This email was sent to {{ $email }} because you registered as a donor on FAIRALL.
            </p>
        </div>
    </div>
</body>
</html>