<?php

namespace App\Api;

use App\Contracts\ExchangeRateInterface;
use App\Exceptions\ExchangeRateException;
use App\Contracts\ConfigInterface;
use App\Contracts\HttpClientInterface;

class ExchangeRate implements ExchangeRateInterface
{
    private $apiUrl;
    private $accessKey;
    private $httpClient;

    public function __construct(ConfigInterface $config, HttpClientInterface $httpClient)
    {
        $this->apiUrl = $config->get('exchange_rate');
        $this->accessKey = $config->get('access_key');
        $this->httpClient = $httpClient;
    }

    public function getRate(string $currency): float
    {
        $url = $this->apiUrl . '?base=EUR&access_key=' . $this->accessKey;

        $response = $this->httpClient->get($url);
        
        if ($response === false) {
            throw new ExchangeRateException('ExchangeRate.php: HTTP request failed');
        }

        $data = json_decode($response, true);

        if (!$data) {
            throw new ExchangeRateException('ExchangeRate.php: Invalid response');
        }

        return $data['rates'][$currency];
    }
}