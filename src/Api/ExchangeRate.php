<?php

namespace App\Api;

use App\Contracts\ExchangeRateInterface;
use App\Exceptions\ExchangeRateException;
use App\Contracts\ConfigInterface;

class ExchangeRate implements ExchangeRateInterface
{
    private $apiUrl;
    private $accessKey;

    public function __construct(ConfigInterface $config)
    {
        $this->apiUrl = $config->get('exchange_rate');
        $this->accessKey = $config->get('access_key');
    }

    public function getRate(string $currency): float
    {
        $url = $this->apiUrl . '?base=EUR&access_key=' . $this->accessKey;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            curl_close($ch);
            throw new ExchangeRateException('ExchangeRate.php: Curl error');
        }

        curl_close($ch);

        $data = json_decode($response, true);

        if (!$data) {
            throw new ExchangeRateException('ExchangeRate.php: Invalid response');
        }

        return $data['rates'][$currency];
        
    }
}