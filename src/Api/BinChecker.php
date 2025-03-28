<?php

namespace App\Api;

use App\Contracts\BinCheckerInterface;
use App\Contracts\ConfigInterface;
use App\Contracts\HttpClientInterface;
use App\Exceptions\BinLookupException;

class BinChecker implements BinCheckerInterface
{
    private $apiUrl;
    private $euCountries;
    private $httpClient;
    
    public function __construct(ConfigInterface $config, HttpClientInterface $httpClient)
    {
        $this->apiUrl = $config->get('bin_lookup');
        $this->euCountries = $config->get('eu_countries');
        $this->httpClient = $httpClient;
    }

    public function lookup(string $bin): bool
    {
        $country = $this->getCountry($bin);

        if ($country == 'Unknown') {
            throw new BinLookupException('BinChecker.php: Country not found');
        }

        return in_array($country, $this->euCountries);
    }

    private function check(string $bin): array
    {
        $url = $this->apiUrl . $bin;
        
        $response = $this->httpClient->get($url);
        
        if ($response === false) {
            return [];
        }
        
        $data = json_decode($response, true);
        
        if (!$data) {
            return [];
        }
        
        return $data;
    }

    private function getCountry(string $bin): string
    {
        $data = $this->check($bin);

        if (isset($data['country']) && isset($data['country']['alpha2'])) {
            return $data['country']['alpha2'];
        }
        
        return 'Unknown';
    }
}