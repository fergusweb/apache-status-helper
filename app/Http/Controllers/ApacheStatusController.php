<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Services\ApacheStatusService;

/**
 * Class
 */
class ApacheStatusController extends Controller
{

    /**
     * Service class
     *
     * @var App\Services\ApacheStatusService;
     */
    protected $apacheStatusService;

    /**
     * Constructor
     *
     * @param ApacheStatusService $apacheStatusService Service class injection
     */
    public function __construct(ApacheStatusService $apacheStatusService)
    {
        $this->apacheStatusService = $apacheStatusService;
    }

    /**
     * Show the status
     *
     * @return void
     */
    public function showStatus()
    {
        try {
            // Use the service to fetch and count IPs
            $ips = $this->apacheStatusService->fetchAndCountIPs();

            // Pass the IP counts to the view
            return view(
                'dashboard', [
                    'ips'             => $ips,
                    'cache_duration'  => $this->apacheStatusService->cache_ttl,
                    'min_connections' => $this->apacheStatusService->min_connections
                ]
            );

        } catch (\Exception $e) {
            return back()->withErrors($e->getMessage());
        }
    }
}
