<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

/**
 * Service class
 */
class ApacheStatusService
{

    /**
     * Min number of connections to be counted
     * (So we can filter out single-connection IPs, etc)
     *
     * @var int
     */
    public $min_connections = 5;

    /**
     * Array of URLs to fetch
     *
     * @var array
     */
    protected $urls;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->urls = config('app.apache_status_urls');
    }

    /**
     * Setter function
     *
     * @param array $urls Array of URLs
     *
     * @return void
     */
    public function setUrls($urls)
    {
        $this->urls = $urls;
    }

    /**
     * Getter function
     *
     * @return array
     */
    public function getUrls()
    {
        return $this->urls;
    }


    /**
     * Fetch the Apache server-status page and return IP address counts.
     *
     * @return array
     */
    public function fetchAndCountIPs(): array
    {
        try {
            $ips = array();
            foreach ($this->urls as $url) {

                // Create a unique cache key for each URL based on the URL itself
                $cacheKey = 'apache_server_status_ips_' . md5($url);

                $responseBody = Cache::remember(
                    $cacheKey, 90, function () use ($url) {
                        // Fetch the server-status page using the Http facade
                        $response = Http::timeout(5)->get($url);

                        if ($response->failed()) {
                            throw new \Exception("Failed to fetch: $url");
                        }
                        echo "Loaded: $url <br>";

                        // Return the response body to be cached
                        return $response->body();
                    }
                );

                // Regular expression to match IP addresses (IPv4)
                $ip_regex = '/\d+\.\d+\.\d+\.\d+/';

                // Extract all client IP addresses from the server-status content
                preg_match_all($ip_regex, $responseBody, $matches);

                $ips = array_merge($ips, $matches[0]); // The matched IP addresses

            }

            // Count occurrences of each unique IP address
            //return array_count_values($ips);
            $ip_counter = array_count_values($ips);
            //echo '<pre>', print_r($ip_counter, true), '</pre>';
            foreach ($ip_counter as $ip => $count) {
                if ($count <= $this->min_connections) {
                    unset($ip_counter[$ip]);
                }
            }


            return $ip_counter;

        } catch (\Exception $e) {
            throw new \Exception("An error occurred: " . $e->getMessage());
        }
    }
}
