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
     * Constructor
     */
    public function __construct()
    {
        $this->cache_ttl = config('app.cache_ip_seconds');
    }

    /**
     * Used to pre-populate any values we have cached from previous lookups
     *
     * @param object $ip [address, count, country, provider]
     *
     * @return array
     */
    public function prepopulate($ip)
    {
        // Cache key
        $cacheKey = $this->cacheKey($ip->address);
        // Load if present
        $json = Cache::get($cacheKey);
        if ($json) {
            $json = $this->formatLookupResponse($json);

            $ip->country = $json['country'];
            $ip->provider = $json['provider'];
            $ip->countryCode = $json['countryCode'];
        }
        return $ip;
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
     * Perform lookup of IP address, cache it, and return array
     *
     * @param string $ip IP address
     *
     * @return array
     */
    public function lookup($ip)
    {

        // Cache key
        $cacheKey = $this->cacheKey($ip);

        // Perform a lookup, or fetch from cache if present
        $json = Cache::remember(
            $cacheKey, $this->cache_ttl, function () use ($ip) {
                $url = "https://ipinfo.io/{$ip}/json";
                $response = Http::timeout(10)->get($url);
                if ($response->failed()) {
                    throw new \Exception("Failed to fetch: $url");
                }

                $json = $response->json();
                $countryName = Countries::getName($json['country'], 'en');
                $json['countryName'] = $countryName;
                $json['countryCode'] = strtolower($json['country']);
                //dd($json);
                return $json;
            }
        );

        //dd($json);


        // Handle return
        if ($json) {
            return $this->formatLookupResponse($json);
        }
        return false;
    }

    /**
     * Helper to format data consistently between functions
     *
     * @param  array $json JSON response.
     * @return array
     */
    public function formatLookupResponse($json)
    {
        $region = array_filter([ $json['countryName'], $json['country'], $json['region'], $json['city'] ]);

        $data = array(
            'country'     => implode(', ', $region),
            'provider'    => $json['org'],
            'countryCode' => $json['countryCode'],
        );
        return $data;
    }

}
