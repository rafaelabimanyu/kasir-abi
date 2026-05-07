<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UpdateLastActivity
{
    /**
     * Update last_activity_at on every authenticated request.
     * Throttled to once per 30s to reduce DB pressure.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();

            $shouldUpdate = is_null($user->last_activity_at)
                || $user->last_activity_at->lt(now()->subSeconds(30));

            if ($shouldUpdate) {
                // Skip model events & updated_at mutation
                $user->timestamps = false;
                $user->forceFill(['last_activity_at' => now()])->save();
                $user->timestamps = true;
            }
        }

        return $next($request);
    }
}
