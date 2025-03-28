<?php
namespace App\Contracts;

interface ExchangeRateInterface {

    /**
     * Constructor
     * @param ConfigInterface $config
     */
    public function __construct(ConfigInterface $config, HttpClientInterface $httpClient);

    /**
     * Get the exchange rate for a currency from EUR base
     * @param string $currency
     * @return float
     */
    public function getRate(string $currency): float;
}
    