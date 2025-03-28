<?php
namespace App\Contracts;

interface ExchangeRateInterface {
    /**
     * Get the exchange rate for a currency from EUR base
     * @param string $currency
     * @return float
     */
    public function getRate(string $currency): float;
}
    