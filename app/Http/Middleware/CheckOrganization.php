<?php

namespace App\Http\Middleware;

use App\Traits\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckOrganization
{
    use ApiResponse;

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $this->unauthorizedResponse('Unauthenticated');
        }

        if (! $user->organization_id) {
            return $this->forbiddenResponse('User is not associated with any organization');
        }

        return $next($request);
    }
}
