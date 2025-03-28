<?php

namespace App\Api;

use App\Contracts\HttpClientInterface;

class CurlHttpClient implements HttpClientInterface
{
    private array $options = [];
    private ?int $lastStatusCode = null;
    
    public function get(string $url, array $headers = []): string|false
    {
        return $this->request('GET', $url, null, $headers);
    }
    
    public function post(string $url, $data = [], array $headers = []): string|false
    {
        return $this->request('POST', $url, $data, $headers);
    }
    
    public function setOptions(array $options): void
    {
        $this->options = $options;
    }
    
    public function getLastStatusCode(): ?int
    {
        return $this->lastStatusCode;
    }
    
    private function request(string $method, string $url, $data = null, array $headers = []): string|false
    {
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        foreach ($this->options as $option => $value) {
            curl_setopt($ch, $option, $value);
        }
        
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            if ($data) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, is_array($data) ? http_build_query($data) : $data);
            }
        }
        
        if (!empty($headers)) {
            $formattedHeaders = [];
            foreach ($headers as $key => $value) {
                $formattedHeaders[] = "$key: $value";
            }
            curl_setopt($ch, CURLOPT_HTTPHEADER, $formattedHeaders);
        }
        
        $response = curl_exec($ch);
        
        $this->lastStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        curl_close($ch);
        
        return $response;
    }
}