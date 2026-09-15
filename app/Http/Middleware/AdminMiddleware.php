<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {

        /*
        |--------------------------------------------------------------------------
        | Not Logged In
        |--------------------------------------------------------------------------
        */

        if (!auth()->check()) {
            return redirect()->route('auth.login');
        }


        $user = auth()->user();


        /*
        |--------------------------------------------------------------------------
        | Account Disabled
        |--------------------------------------------------------------------------
        */

        if (!$user->status) {

            auth()->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('auth.login')
                ->withErrors([
                    'email' => 'Your account has been disabled.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Admin Role Check
        |--------------------------------------------------------------------------
        */

        if (!$user->isAdmin()) {
            abort(403);
        }


        /*
        |--------------------------------------------------------------------------
        | Continue
        |--------------------------------------------------------------------------
        */

        return $next($request);
    }
}