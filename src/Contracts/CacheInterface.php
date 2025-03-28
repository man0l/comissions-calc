<?php
namespace App\Contracts;

interface CacheInterface {
    /**
     * Get the value of a key
     * @param string $key
     * @return string
     */
    public function get(string $key): string;

    /**
     * Set the value of a key
     * @param string $key
     * @param string $value
     */
    public function set(string $key, string $value): void;
}  