<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'غير مصرح لك'
            ], 401);
        }

        if ($user->status !== 'active') {

            $message = match ($user->status) {
                'blocked' => 'هذا الحساب محظور',
                'pending' => 'هذا الحساب قيد المراجعة',
                default => 'هذا الحساب غير فعال',
            };

            return response()->json([
                'status' => false,
                'message' => $message
            ], 403);
        }

        return $next($request);
    }
}
