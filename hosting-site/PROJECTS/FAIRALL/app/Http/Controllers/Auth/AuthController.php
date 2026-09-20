<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function create()
    {
        return view('login');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'max:255'],  // Can be email or login_id
            'password' => ['required', 'string', 'min:8'],
            'remember' => ['sometimes', 'boolean'],
        ]);

        // Determine if input is email or login_id
        $credential = $validated['email'];
        $isEmail = filter_var($credential, FILTER_VALIDATE_EMAIL);

        $field = $isEmail ? 'email' : 'login_id';
        $user = User::where($field, $credential)->first();

        if (!$user) {
            return back()->withErrors([
                'password' => 'Invalid credentials',
            ])->withInput($request->only('email', 'remember'));
        }
        
        if (!$user->is_active) {
            return back()->withErrors([
                'password' => 'Your account is inactive.',
            ])->withInput($request->only('email', 'remember'));
        }

        if ($user->user_type === 'beneficiary') {
            return back()->withErrors([
                'email' => 'Beneficiaries cannot log in via the web. Please use the mobile app.',
            ])->withInput($request->only('email', 'remember'));
        }

        if (!$user->email_verified_at) {
            return back()->withErrors([
                'email' => 'Please verify your email address before logging in.',
            ])->withInput($request->only('email', 'remember'));
        }

        $credentials = [
            $field => $credential,
            'password' => $validated['password'],
            'is_active' => true,
        ];

        $remember = $request->boolean('remember', false);

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            // Force password change on first sign-in for auto-generated passwords
            if (is_null(Auth::user()->password_changed_at) && Auth::user()->user_type === 'staff') {
                return redirect('/force-password-change');
            }

            return redirect('/dashboard');
        }

        return back()->withErrors([
            'password' => 'Invalid credentials',
        ])->withInput($request->only('email', 'remember'));
    }

    public function destroy()
    {
        Auth::logout();
        return redirect('/');
    }
}
