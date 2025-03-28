<?php

namespace App\Contracts;

interface HttpClientInterface
{
    public function get(string $url, array $headers = []): string|false;    
    public function post(string $url, $data = [], array $headers = []): string|false;
    public function setOptions(array $options): void;
    public function getLastStatusCode(): ?int;
}