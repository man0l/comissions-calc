<?php

namespace App\Api;

use App\Contracts\BinCheckerInterface;
use App\Contracts\ConfigInterface;
use App\Exceptions\BinLookupException;

class BinChecker implements BinCheckerInterface
{
    private $apiUrl;
    private $euCountries;

    public function __construct(ConfigInterface $config)
    {
        $this->apiUrl = $config->get('bin_lookup');
        $this->euCountries = $config->get('eu_countries');
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
        
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

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

    private function getCountry(string $bin): string
    {
        $data = $this->check($bin);

        if (isset($data['country']) && isset($data['country']['alpha2'])) {
            return $data['country']['alpha2'];
        }
        
        return 'Unknown';
    }
}