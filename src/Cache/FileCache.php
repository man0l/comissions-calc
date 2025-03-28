<?php
namespace App\Cache;

use App\Contracts\CacheInterface;

class FileCache implements CacheInterface {
    
    private const CACHE_FILE = __DIR__ . '/../../data/cache.json';

    public function __construct() {
        if (!$this->cacheFileExists()) {
            $this->writeCacheFile([]);
        }
    }
    
    public function get(string $key): string {
        if (!$this->cacheFileExists()) {
            throw new \Exception('Cache file not found');
        }

        $cache = $this->readCacheFile();
        
        if (isset($cache[$key])) {
            return $cache[$key];
        }

        return '';
    }

    public function set(string $key, string $value): void {
        $cache = $this->readCacheFile();
        $cache[$key] = $value;
        $this->writeCacheFile($cache);
    }

    protected function cacheFileExists(): bool
    {
        return file_exists(self::CACHE_FILE);
    }

    protected function readCacheFile(): array
    {
        return json_decode(file_get_contents(self::CACHE_FILE), true);
    }

    protected function writeCacheFile(array $data): void
    {
        file_put_contents(self::CACHE_FILE, json_encode($data));
    }
}