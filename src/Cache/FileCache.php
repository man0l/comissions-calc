<?php
namespace App\Cache;

use App\Contracts\CacheInterface;

class FileCache implements CacheInterface {
    
    private const CACHE_FILE = __DIR__ . '/../../data/cache.json';

    public function __construct() {
        if (!file_exists(self::CACHE_FILE)) {
            file_put_contents(self::CACHE_FILE, json_encode([]));
        }
    }
    
    public function get(string $key): string {
        if (!file_exists(self::CACHE_FILE)) {
            throw new \Exception('Cache file not found');
        }

        $cache = json_decode(file_get_contents(self::CACHE_FILE), true);
        
        if (isset($cache[$key])) {
            return $cache[$key];
        }

        return '';
    }

    public function set(string $key, string $value): void {
        $cache = json_decode(file_get_contents(self::CACHE_FILE), true);
        $cache[$key] = $value;
        file_put_contents(self::CACHE_FILE, json_encode($cache));
    }
}