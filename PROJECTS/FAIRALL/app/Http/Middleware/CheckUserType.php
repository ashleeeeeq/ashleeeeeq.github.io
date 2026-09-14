<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckUserType
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$types
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$types)
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }
        
        if (!in_array($user->user_type, $types)) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied. Only ' . implode(' or ', $types) . ' can access this resource.'
            ], 403);
        }
        
        return $next($request);
    }
}