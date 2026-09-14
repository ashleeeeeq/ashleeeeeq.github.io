<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsurePasswordChanged
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user && in_array($user->user_type, ['staff', 'donor'], true) && is_null($user->password_changed_at)) {
            if (!$request->routeIs('force-password-change*') && !$request->is('force-password-change') && !$request->routeIs('logout')) {
                return redirect('/force-password-change');
            }
        }

        return $next($request);
    }
}
