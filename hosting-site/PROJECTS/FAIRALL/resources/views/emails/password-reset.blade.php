<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>FAIRALL Password Reset</title>
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
            background-color: #20264F;
            color: white;
        }
        .logo {
            font-size: 28px;
            font-weight: bold;
        }
        .content {
            padding: 30px;
        }
        .code {
            font-size: 32px;
            font-weight: bold;
            text-align: center;
            letter-spacing: 5px;
            padding: 20px;
            background-color: #f0f0f0;
            border-radius: 10px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            padding: 20px;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #eee;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #FFB800;
            color: #000;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">FAIRALL</div>
        </div>

        <div class="content">
            <h2>Hello, {{ $name ?? 'Valued Donor' }}!</h2>
            <p>We received a request to reset your FAIRALL account password.</p>

            <div class="code">{{ $code }}</div>

            <p>Enter this 6-digit code on the password reset page.</p>
            <p>This code will expire in 30 minutes.</p>

            <p>If you did not request a password reset, no further action is needed.</p>

            <p>Thanks,<br><strong>The FAIRALL Team</strong></p>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} FAIRALL. All rights reserved.</p>
            <p>Building a better future through generosity.</p>
        </div>
    </div>
</body>
</html>