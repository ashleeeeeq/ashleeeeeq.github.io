<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Login user and return token
     */
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        // Determine if input is email or login_id
        $credential = $validated['email'];
        $isEmail = filter_var($credential, FILTER_VALIDATE_EMAIL);

        $field = $isEmail ? 'email' : 'login_id';
        $user = User::where($field, $credential)->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials'],
            ]);
        }

        if (!$user->is_active) {
            throw ValidationException::withMessages([
                'email' => ['Your account is inactive.'],
            ]);
        }

        if (!$user->email_verified_at && !($user->user_type === 'beneficiary' && !$isEmail)) {
            return response()->json([
                'success' => false,
                'message' => 'Please verify your email address before logging in.',
            ], 403);
        }

        $credentials = [
            $field => $credential,
            'password' => $validated['password'],
            'is_active' => true,
        ];

        if (!Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials'],
            ]);
        }

        $user = Auth::user();

        // RESTRICT: Only allow beneficiary and donor to login from mobile app
        if (!in_array($user->user_type, ['beneficiary', 'donor'])) {
            Auth::logout();

            return response()->json([
                'success' => false,
                'message' => 'Access denied. Only beneficiaries and donors can use the mobile app.',
            ], 403);
        }

        // LOAD RELATIONSHIPS based on user type
        if ($user->user_type === 'beneficiary') {
            $user->load('beneficiary.address', 'beneficiary.educationEnrollments');
        } elseif ($user->user_type === 'donor') {
            $user->load('donor');
        }

        // Generate Sanctum token
        $token = $user->createToken(
            'api-token',
            ['*'],
            now()->addHours(1)
        )->plainTextToken;

        $refreshToken = $user->createToken(
            'api-refresh',
            ['refresh'],
            now()->addDays(7)
        )->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'access_token' => $token,
            'refresh_token' => $refreshToken,
            'token_type' => 'Bearer',
            'expires_in' => 3600,
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
                'login_id' => $user->login_id,
                'user_type' => $user->user_type,
                'is_active' => $user->is_active,
                'email_verified_at' => $user->email_verified_at,
                'beneficiary' => $user->beneficiary,
                'donor' => $user->donor,
            ],
        ], 200);
    }

    /**
     * Logout user (revoke token)
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully',
        ], 200);
    }

    /**
     * Refresh authentication token
     */
    public function refresh(Request $request): JsonResponse
    {
        $user = $request->user();

        // Revoke old token
        $request->user()->currentAccessToken()->delete();

        // Generate new token
        $token = $user->createToken('api-token', ['*'], now()->addHours(1))->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Token refreshed successfully',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => 3600,
        ], 200);
    }

    /**
     * Resend email verification link
     */
    public function resendVerificationEmail(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email', 'exists:users,email'],
        ]);

        $user = User::where('email', $validated['email'])->firstOrFail();

        if ($user->email_verified_at) {
            return response()->json([
                'success' => false,
                'message' => 'Email already verified',
            ], 422);
        }

        $user->sendEmailVerificationNotification();

        return response()->json([
            'success' => true,
            'message' => 'Verification email sent successfully',
            'email' => $user->email,
        ], 200);
    }

    /**
     * Send password reset code
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email', 'exists:users,email'],
        ]);

        $user = User::where('email', $validated['email'])->first();

        // Generate random 6-digit code
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Store in database
        PasswordReset::updateOrCreate(
            ['email' => $user->email],
            [
                'code' => bcrypt($code),
                'expires_at' => now()->addMinutes(30),
                'is_used' => false,
            ]
        );

        // Send email with the code
        try {
            \Illuminate\Support\Facades\Mail::send(
                'emails.password-reset',
                ['code' => $code, 'name' => $user->name ?? 'Valued Donor'],
                function ($message) use ($user) {
                    $message->to($user->email)
                        ->subject('FAIRALL Password Reset Code');
                }
            );

            return response()->json([
                'success' => true,
                'message' => 'Password reset code sent to your email'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send email: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verify reset code
     */
    public function verifyResetCode(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email', 'exists:users,email'],
            'code' => ['required', 'string', 'size:6'],
        ]);

        $resetRecord = PasswordReset::where('email', $validated['email'])
            ->where('is_used', false)
            ->where('expires_at', '>', now())
            ->first();

        if (!$resetRecord || !Hash::check($validated['code'], $resetRecord->code)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired reset code'
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Code verified successfully'
        ], 200);
    }

    /**
     * Reset password
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email', 'exists:users,email'],
            'code' => ['required', 'string', 'size:6'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $resetRecord = PasswordReset::where('email', $validated['email'])
            ->where('is_used', false)
            ->where('expires_at', '>', now())
            ->first();

        if (!$resetRecord || !Hash::check($validated['code'], $resetRecord->code)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired reset code'
            ], 400);
        }

        $user = User::where('email', $validated['email'])->first();
        $user->password = Hash::make($validated['password']);
        $user->save();

        // Mark code as used
        $resetRecord->is_used = true;
        $resetRecord->save();

        // Revoke all tokens
        $user->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Password reset successfully'
        ], 200);
    }

    /**
     * Verify user email - Returns HTML page
     */
    public function verifyEmail($id, $hash)
    {
        $user = User::findOrFail($id);

        // Invalid hash
        if (sha1($user->email) !== $hash) {
            return $this->verificationResponse(
                false,
                'Invalid Verification Link',
                'The verification link is invalid or has been tampered with.',
                'error'
            );
        }

        // Already verified
        if ($user->email_verified_at) {
            return $this->verificationResponse(
                false,
                'Email Already Verified',
                'Your email has already been verified. You can now log in to your account.',
                'warning'
            );
        }

        // Verify the email
        $user->email_verified_at = now();
        $user->save();

        return $this->verificationResponse(
            true,
            'Email Verified Successfully',
            'Your email has been verified. You can now log in to your FAIRALL account.',
            'success'
        );
    }

    /**
     * Return HTML verification response
     */
    private function verificationResponse($success, $title, $message, $type)
    {
        $buttonText = $success ? 'Go to Login' : 'Back to Login';
        $buttonLink = url('/login');

        $iconHtml = '';
        if ($type === 'success') {
            $iconHtml = '<div style="font-size: 64px; color: #4CAF50;">✓</div>';
        } elseif ($type === 'error') {
            $iconHtml = '<div style="font-size: 64px; color: #f44336;">✗</div>';
        } elseif ($type === 'warning') {
            $iconHtml = '<div style="font-size: 64px; color: #ff9800;">!</div>';
        } else {
            $iconHtml = '<div style="font-size: 64px; color: #2196F3;">i</div>';
        }

        $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAIRALL - $title</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background: #f5f5f5;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .container {
            max-width: 480px;
            width: 100%;
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: #20264F;
            padding: 32px 20px;
            text-align: center;
        }
        .logo {
            font-size: 28px;
            font-weight: bold;
            color: white;
            letter-spacing: 1px;
        }
        .tagline {
            font-size: 13px;
            color: rgba(255,255,255,0.7);
            margin-top: 6px;
        }
        .icon {
            text-align: center;
            padding: 32px 0 16px 0;
        }
        .content {
            padding: 24px 32px 32px 32px;
            text-align: center;
        }
        .title {
            font-size: 22px;
            font-weight: bold;
            color: #20264F;
            margin-bottom: 12px;
        }
        .message {
            font-size: 15px;
            color: #666;
            line-height: 1.5;
            margin-bottom: 28px;
        }
        .button {
            display: inline-block;
            padding: 12px 28px;
            background: #FFB800;
            color: #20264F;
            text-decoration: none;
            border-radius: 40px;
            font-weight: 600;
            font-size: 15px;
            transition: background 0.2s ease;
        }
        .button:hover {
            background: #e5a600;
        }
        .footer {
            background: #fafafa;
            padding: 20px;
            text-align: center;
            font-size: 11px;
            color: #999;
            border-top: 1px solid #eee;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">FAIRALL</div>
            <div class="tagline">Building a better future through generosity</div>
        </div>
        
        <div class="icon">$iconHtml</div>
        
        <div class="content">
            <h1 class="title">$title</h1>
            <p class="message">$message</p>
            <a href="$buttonLink" class="button">$buttonText</a>
        </div>
        
        <div class="footer">
            <p>&copy; 2026 FAIRALL. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
HTML;

        return response($html)->header('Content-Type', 'text/html');
    }
}
