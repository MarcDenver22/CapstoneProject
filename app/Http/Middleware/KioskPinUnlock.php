<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class KioskPinUnlock
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if kiosk has been unlocked via cookie
        if ($request->cookie('kiosk_unlocked') === 'true' || $request->cookie('kiosk_unlocked') === true) {
            return $next($request);
        }

        // Redirect to unlock page if not unlocked
        return redirect()->route('kiosk.unlock');
    }
}
