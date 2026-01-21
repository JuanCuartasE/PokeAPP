<?php

namespace App;

class HttpClient
{
    private CacheManager $cache;
    private string $baseUrl = 'https://pokeapi.co/api/v2/';

    public function __construct(CacheManager $cache)
    {
        $this->cache = $cache;
    }

    public function get(string $endpoint): array
    {
        $url = $this->baseUrl . $endpoint;

        // Try to get from FRESH cache first
        $cachedData = $this->cache->get($url);
        if ($cachedData !== null) {
            return $cachedData;
        }

        // Fetch from API
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5); // Reduced timeout for faster failure
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError || $httpCode !== 200) {
            // API failed, try to recover from STALE cache (ignore TTL)
            $staleData = $this->cache->get($url, true);
            if ($staleData !== null) {
                return $staleData;
            }

            // If no cache, throw descriptive technical exception
            $errorSource = $curlError ? "CURL_ERROR: $curlError" : "HTTP_STATUS: $httpCode";
            throw new \Exception("[CRITICAL] PokeAPI Connection Failure. Reason: $errorSource. Resource: $url. No fallback cache found.");
        }

        $data = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception("[PARSE_ERROR] Invalid JSON response from PokeAPI for resource: $url. JSON_ERROR: " . json_last_error_msg());
        }

        // Save to cache
        $this->cache->set($url, $data);

        return $data;
    }
}
