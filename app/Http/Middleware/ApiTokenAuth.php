<?php

namespace App\Http\Middleware;

use App\Models\ApiToken;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiTokenAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $plainToken = $request->bearerToken();

        if (! $plainToken || strlen($plainToken) < 40) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication is required.',
                'errors' => [],
            ], 401);
        }

        $token = ApiToken::query()
            ->with('user')
            ->where('token_hash', hash('sha256', $plainToken))
            ->first();

        if (
            ! $token
            || $token->isExpired()
            || ! $token->user
            || ! $token->user->isActive()
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Your authentication token is invalid or expired.',
                'errors' => [],
            ], 401);
        }

        if (
            $token->last_used_at === null
            || $token->last_used_at->lt(now()->subMinutes(5))
        ) {
            $token->forceFill([
                'last_used_at' => now(),
            ])->save();
        }

        $request->attributes->set('api_token', $token);
        $request->setUserResolver(
            fn () => $token->user
        );

        return $next($request);
    }
}
