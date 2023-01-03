<?php

namespace ShahriarSiraj\LaravelIpBlacklisting\Middleware;

use Closure;
use Illuminate\Http\Request;
use ShahriarSiraj\LaravelIpBlacklisting\Models\BlacklistedIp;

class IpBlacklistingMiddleware
{
    /**
     * @return array
     */
    public function getBlacklistedIps(): array
    {
        return BlacklistedIp::pluck('ip')->all();
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (in_array($request->getClientIp(), $this->getBlacklistedIps())) {
            abort(403, 'You are restricted to access the site.');
        }

        return $next($request);
    }
}
