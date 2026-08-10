<?php

namespace App\Http\Middleware;

use Closure;

class RedirectIfAdmin
{
    public function handle($request, Closure $next)
    {
        return $next($request);
    }
}
