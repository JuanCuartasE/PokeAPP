<?php

namespace App;

class CacheManager
{
    private string $cachePath;
    private int $ttl;

    public function __construct(string $cachePath, int $ttl = 600)
    {
        $this->cachePath = rtrim($cachePath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $this->ttl = $ttl;

        if (!is_dir($this->cachePath)) {
            mkdir($this->cachePath, 0777, true);
        }
    }

    public function get(string $key, bool $ignoreTTL = false): ?array
    {
        $filename = $this->getFilename($key);
        if (file_exists($filename)) {
            $mtime = filemtime($filename);
            if ($ignoreTTL || (time() - $mtime < $this->ttl)) {
                $content = file_get_contents($filename);
                return json_decode($content, true);
            }
        }
        return null;
    }

    public function set(string $key, array $data): void
    {
        $filename = $this->getFilename($key);
        file_put_contents($filename, json_encode($data));
    }

    private function getFilename(string $key): string
    {
        return $this->cachePath . md5($key) . '.json';
    }
}
