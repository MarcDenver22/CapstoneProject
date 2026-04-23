<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceHttps
{
    /**
     * Handle an incoming request - Force HTTPS in production
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only force HTTPS in production
        if ($this->shouldForceHttps()) {
            // If not using HTTPS, redirect to HTTPS
            if (!$request->secure() && !$this->isTrustedProxy($request)) {
                return redirect()->secure($request->getPathAndQuery());
            }
        }

        return $next($request);
    }

    /**
     * Determine if HTTPS should be enforced
     */
    private function shouldForceHttps(): bool
    {
        return app()->environment('production') || env('FORCE_HTTPS', false);
    }

    /**
     * Check if request is from a trusted proxy (e.g., load balancer)
     * Proxies typically set X-Forwarded-Proto: https
     */
    private function isTrustedProxy(Request $request): bool
    {
        $trustedProxies = array_filter(explode(',', env('TRUSTED_PROXIES', '')));
        
        // If no trusted proxies configured, don't force redirect
        if (empty($trustedProxies)) {
            return false;
        }

        $clientIp = $request->ip();
        foreach ($trustedProxies as $proxy) {
            if ($clientIp === trim($proxy)) {
                // Proxy set X-Forwarded-Proto to https, so request is actually secure
                return $request->header('X-Forwarded-Proto') === 'https';
            }
        }

        return false;
    }
}
