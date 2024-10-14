<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Services\LookupIP;

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
     * Cache duration (seconds)
     *
     * @var int
     */
    public $cache_ttl;

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
        $this->cache_ttl = config('app.cache_status_seconds');
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
                    $cacheKey, $this->cache_ttl, function () use ($url) {

                        // Fetch the server-status page using the Http facade
                        $response = Http::timeout(5)->get($url);

                        if ($response->failed()) {
                            throw new \Exception("Failed to fetch: $url");
                        }
                        //echo "Loaded: $url <br>";

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
            $ip_counter = array_count_values($ips);
            foreach ($ip_counter as $ip => $count) {
                if ($count <= $this->min_connections) {
                    unset($ip_counter[$ip]);
                }
            }

            // Sort by count
            arsort($ip_counter);

            // Perform a Bulk Lookup of all IPs now, to minimise the API calls
            $lookupIP = new LookupIP();
            $lookupIP->bulkLookup(array_keys($ip_counter));

            // Turn this into a new data structure
            $ip_data = array();

            foreach ($ip_counter as $ip => $count) {
                $data = $lookupIP->lookup($ip);
                //dd($data);
                $data['count'] = $count;

                $ip_data[] = $data;
                /*
                $ip_data[] = (object) array(
                    'address' => $ip,
                    'count' => $count,
                    'country' => null,
                    'provider' => null,
                );
                */
            }
            //echo '<pre>IP Data: ', print_r($ip_data, true), '</pre>';
            return $ip_data;

        } catch (\Exception $e) {
            throw new \Exception("An error occurred: " . $e->getMessage());
        }
    }
}
