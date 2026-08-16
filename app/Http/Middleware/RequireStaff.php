<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireStaff
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless(
            $request->user()?->hasSystemRole('admin', 'super_admin', 'reception', 'receptionist', 'secretary'),
            403
        );

        return $next($request);
    }
}
