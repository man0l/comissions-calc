<?php
namespace App\Config;

use App\Contracts\ConfigInterface;
use App\Exceptions\ConfigException;
class Config implements ConfigInterface
{
    private array $config;
    
    public function __construct()
    {
        $this->config = [
            'eu_countries' => [
                'AT', 'BE', 'BG', 'CY', 'CZ', 'DE', 'DK', 'EE', 
                'ES', 'FI', 'FR', 'GR', 'HR', 'HU', 'IE', 'IT', 
                'LT', 'LU', 'LV', 'MT', 'NL', 'PO', 'PT', 'RO', 
                'SE', 'SI', 'SK'
            ],
            'bin_lookup' => 'https://lookup.binlist.net/',
            'exchange_rate' => 'https://api.exchangeratesapi.io/latest',
            'access_key' => getenv('ACCESS_KEY'),
            'eu_fee_rate' => 0.01,
            'non_eu_fee_rate' => 0.02
        ];
    }
    
    public function get(string $key)
    {
        if (!isset($this->config[$key])) {
            throw new ConfigException("Config key '$key' not found");
        }
        return $this->config[$key];
    }
}