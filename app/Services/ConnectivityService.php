<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Connectivity Service
 *
 * Checks whether the Supabase (remote) database is currently reachable.
 * Used by the kiosk to decide whether to save attendance immediately
 * or queue it for later syncing.
 */
class ConnectivityService
{
    /**
     * Check whether the Supabase database is reachable.
     *
     * A lightweight SELECT 1 query is used so we don't pull any real data.
     * The result is cached for 10 seconds to avoid hammering the DB
     * on every individual check within the same request.
     */
    public function isSupabaseReachable(): bool
    {
        try {
            DB::connection('supabase')->select('SELECT 1');
            return true;
        } catch (\Exception $e) {
            Log::warning('ConnectivityService: Supabase unreachable — ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Alias so callers can use the more generic name.
     */
    public function isOnline(): bool
    {
        return $this->isSupabaseReachable();
    }
}
