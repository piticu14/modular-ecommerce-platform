<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class EnsureUserContext
{
    public function handle(Request $request, Closure $next): Response
    {
        if (app()->environment('testing')) {
            return $next($request);
        }

        $userId = $request->header('X-User-Id');

        if (! is_numeric($userId)) {
            abort(401, 'Missing user context');
        }

        return $next($request);
    }
}
