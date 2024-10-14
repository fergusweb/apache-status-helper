<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\Intl\Countries;



/**
 * Service class
 */
class LookupIP
{

    /**
     * Cache duration (seconds)
     *
     * @var int
     */
    public $cache_ttl;


    /**
     * DEBUG: Whether to announce each API call with output
     *
     * @var boolean
     */
    public $announce_api_calls = false;



    /**
     * Constructor
     */
    public function __construct()
    {
        $this->cache_ttl = config('app.cache_ip_seconds');
    }



    /**
     * Getter: Country
     *
     * @param  string $ip IP address
     * @return string
     */
    public function country($ip=null)
    {
        $lookup = $this->lookup($ip);
        return $lookup['country'];
    }

    /**
     * Getter: Provider
     *
     * @param string $ip IP address
     *
     * @return string
     */
    public function provider($ip=null)
    {
        $lookup = $this->lookup($ip);
        return $lookup['provider'];
    }

    /**
     * Getter: countryCode
     *
     * @param string $ip IP address
     *
     * @return string
     */
    public function countryCode($ip=null)
    {
        $lookup = $this->lookup($ip);
        return $lookup['countryCode'];
    }

    /**
     * Helper to use the same cache_key for IP address lookups
     *
     * @param string $ip IP address
     *
     * @return string
     */
    public function cacheKey($ip)
    {
        return 'ip_lookup_' . $ip;
    }






    /**
     * Perform a lookup for an IP address using ip2location.io API
     * Will do any customised formatting, and cache the response.
     *
     * @param string $ip     IP address to look up
     * @param bool   $lookup Whether to perform API lookup, or just fetch from cache
     *
     * @return array Response object
     */
    public function lookup($ip, $lookup=true)
    {
        // Cache key
        $cacheKey = $this->cacheKey($ip);

        // Configure
        $api_key = config('app.ipapi_api_key');


        // Fetch from cache only
        if (!$lookup && Cache::has($cacheKey)) {
            $json = Cache::get($cacheKey);
            if ($json) {
                return $json;
            } else {
                //throw new \Exception('No JSON found in cache');
            }
        }


        // Perform lookup
        if ($lookup) {
            $json = Cache::remember(
                $cacheKey, $this->cache_ttl, function () use ($ip, $api_key, $cacheKey) {
                    try {
                        if ($this->announce_api_calls) {
                            echo '<p>DEBUG: Doing an API lookup of '.$ip.'  (Cache key: '.$cacheKey.')</p>';
                        }

                        $url = sprintf('https://api.ipapi.is/?q=%s&key=%s', $ip, $api_key);
                        //$url = sprintf('https://us.ipapi.is/?q=%s&key=%s', $ip, $api_key);
                        $response = Http::timeout(10)->get($url);
                        $json = $response->json();
                        //dd($json);
                        // Do some formatting
                        $json = $this->formatLookupResponse($json);
                        // Return & save the response
                        return $json;
                    } catch(\Exception $e) {
                        var_dump($e->getCode() . ": " . $e->getMessage());
                    }
                }
            );
            return $json;
        }
        return false;
    }

    /**
     * Bulk lookup of IP addresses
     *
     * @param array $ips Array of IP addresses
     *
     * @return array Array of responses
     */
    public function bulkLookup($ips)
    {
        $hash = md5(serialize($ips));
        $cacheKey = $this->cacheKey('bulk_ips_'.$hash);
        $api_key = config('app.ipapi_api_key');

        //echo '<pre>Bulk lookup of: ', print_r($ips, true), '</pre>';

        try {

            $json = Cache::remember(
                $cacheKey, $this->cache_ttl, function () use ($ips, $api_key) {

                    if ($this->announce_api_calls) {
                        echo '<p>DEBUG: Doing a bulk API lookup of '.count($ips).' IPs</p>';
                    }

                    $response = Http::withHeaders(
                        ['Content-Type' => 'application/json',]
                    )->post(
                        'https://api.ipapi.is?key='.$api_key,
                        ['ips' => $ips]
                    );

                    $json = $response->json();
                    //dd($json);

                    // Remove the total_elapsed_ms, we just want the IPs
                    if (array_key_exists('total_elapsed_ms', $json)) {
                        unset($json['total_elapsed_ms']);
                    }


                    // Apply our standard formatting to the individual responses
                    foreach ($ips as $ip) {
                        if (!array_key_exists($ip, $json)) {
                            continue;
                        }
                        $json[$ip] = $this->formatLookupResponse($json[$ip]);
                    }
                    //dd($json);


                    // I also want to cache these as individual lookups
                    foreach ($ips as $ip) {
                        //echo '<p>Maybe cache individual query for: '.$ip.'</p>';
                        if (array_key_exists($ip, $json)) {
                            $thisCacheKey = $this->cacheKey($ip);
                            Cache::put($thisCacheKey, $json[$ip], $seconds = $this->cache_ttl);
                            //echo '<p>Cache key: '.$thisCacheKey.'</p>';
                            //echo '<pre>Data: '.print_r($json[$ip], true).'</pre>';
                        }
                    }

                    return $json;
                }
            );


            //echo '<pre>JSON response: ', print_r($json, true), '</pre>';


        } catch(\Exception $e) {
            var_dump($e->getCode() . ": " . $e->getMessage());
        }
        return false;
    }

    /**
     * Helper to format data consistently between functions
     *
     * @param array $json Original JSON response.
     *
     * @return array Modified JSON response.
     */
    public function formatLookupResponse($json)
    {
        $region = array_filter(
            [
            $json['location']['country'],
            $json['location']['state'],
            $json['location']['city'],
            ]
        );
        $json['region'] = implode(', ', $region);
        return $json;
    }
}
