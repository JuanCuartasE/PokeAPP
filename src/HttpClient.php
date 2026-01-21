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

        // Intentar obtener de la caché vigente primero
        $cachedData = $this->cache->get($url);
        if ($cachedData !== null) {
            return $cachedData;
        }

        // Petición a la API
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5); // Timeout reducido para detectar caídas rápidamente
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError || $httpCode !== 200) {
            // Error en la API, intentar recuperar de caché antigua (ignorar TTL)
            $staleData = $this->cache->get($url, true);
            if ($staleData !== null) {
                return $staleData;
            }

            // Si no hay caché disponible, lanzar excepción técnica
            $errorSource = $curlError ? "CURL_ERROR: $curlError" : "HTTP_STATUS: $httpCode";
            throw new \Exception("[CRITICAL] Fallo de conexión con PokeAPI. Motivo: $errorSource. Recurso: $url. No se encontró caché de respaldo.");
        }

        $data = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception("[PARSE_ERROR] Respuesta JSON inválida de PokeAPI para el recurso: $url. JSON_ERROR: " . json_last_error_msg());
        }

        // Almacenar en caché
        $this->cache->set($url, $data);

        return $data;
    }
}
