<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class KioskIpAllowlist
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $allowedIps = $this->getAllowedIps();
        $clientIp = $request->ip();

        if (!in_array($clientIp, $allowedIps)) {
            return response('Kiosk access denied.', Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }

    /**
     * Get the list of allowed IPs from environment configuration.
     *
     * @return array
     */
    private function getAllowedIps(): array
    {
        $ipsString = env('KIOSK_ALLOWED_IPS', '127.0.0.1');
        return array_map('trim', explode(',', $ipsString));
    }
}
