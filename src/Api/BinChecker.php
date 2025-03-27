<?php

namespace App\Api;

class BinChecker
{
    private $apiUrl = 'https://lookup.binlist.net/';

    public function check(string $bin): array
    {
        $url = $this->apiUrl . $bin;
        
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept-Version: 3',
        ]);
        
        $response = curl_exec($ch);
        
        if (curl_errno($ch)) {
            curl_close($ch);
            return [];
        }
        
        curl_close($ch);
        
        $data = json_decode($response, true);
        
        if (!$data) {
            return [];
        }
        
        return $data;
    }
    
    public function getCountry(string $bin): string
    {
        $data = $this->check($bin);
        
        if (isset($data['country']) && isset($data['country']['alpha2'])) {
            return $data['country']['alpha2'];
        }
        
        return 'Unknown';
    }
}