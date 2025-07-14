<?php

namespace App\Http\Controllers;

class CacheManagementController extends Controller
{
    /**
     * Clear persistence cache for fresh testing
     */
    public function clearPersistenceCache()
    {
        cache()->forget('request_persistence_test');
        cache()->forget('interface_request_persistence_test');
        cache()->forget('app_injection_requests');
        cache()->forget('pure_singleton_requests');
        
        return response()->json([
            'message' => 'All persistence cache cleared successfully (including Application injection and Pure singleton tests)',
            'timestamp' => now()->format('Y-m-d H:i:s.u')
        ]);
    }
} 