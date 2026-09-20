<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            if ($request->expectsJson()) {
                return response()->json(['status' => false, 'message' => 'يرجى تسجيل الدخول أولاً'], 401);
            }
            return redirect()->route('login')->with('error', 'لإكمال هذه العملية يجب تسجيل الدخول أولاً.');
        }

        if (!in_array($user->role, $roles, true)) {
            if ($request->expectsJson()) {
                return response()->json(['status' => false, 'message' => 'ليس لديك صلاحية للوصول'], 403);
            }
            return redirect()->route('home')->with('error', 'عذراً، ليس لديك صلاحية الوصول لهذه الصفحة.');
        }

        return $next($request);
    }
}
