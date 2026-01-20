<?php

namespace App\Http\Middleware;

use App\Traits\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    use ApiResponse;

    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return $this->unauthorizedResponse('Unauthenticated');
        }

        if (! in_array($user->role, $roles)) {
            return $this->forbiddenResponse('You do not have permission to access this resource');
        }

        return $next($request);
    }
}
