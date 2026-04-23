<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request - Add security headers
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Enforce HTTPS Strict Transport Security (HSTS)
        // Tells browsers to always use HTTPS for this domain
        // max-age: 31536000 seconds = 1 year
        if (app()->environment('production')) {
            $response->header('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }

        // Prevent clickjacking - Only allow framing from same origin
        $response->header('X-Frame-Options', 'SAMEORIGIN');

        // Prevent MIME type sniffing
        // Forces browser to respect Content-Type header
        $response->header('X-Content-Type-Options', 'nosniff');

        // Enable XSS protection in older browsers
        $response->header('X-XSS-Protection', '1; mode=block');

        // Referrer Policy - Control how much referrer info is shared
        $response->header('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Permissions Policy - Control which browser features can be used
        // Allow camera and microphone for kiosk (face recognition needs them)
        // Block other potentially dangerous features
        $response->header('Permissions-Policy', 
            'accelerometer=(), camera=(self), geolocation=(), gyroscope=(), magnetometer=(), microphone=(self), payment=(), usb=()'
        );

        // Content Security Policy - Prevent XSS by restricting resource loading
        // In production, customize this based on your actual resource needs
        if (app()->environment('production')) {
            $csp = $this->getContentSecurityPolicy();
            $response->header('Content-Security-Policy', $csp);
        }

        return $response;
    }

    /**
     * Get Content Security Policy header value
     * Customize based on your application's needs
     */
    private function getContentSecurityPolicy(): string
    {
        $appUrl = config('app.url');
        
        return implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval'",  // Adjust based on your needs
            "style-src 'self' 'unsafe-inline'",
            "img-src 'self' data: https:",
            "font-src 'self' data:",
            "connect-src 'self' https:",
            "frame-ancestors 'self'",
            "form-action 'self'",
            "base-uri 'self'",
            "upgrade-insecure-requests",  // Upgrade http:// to https://
        ]);
    }
}
