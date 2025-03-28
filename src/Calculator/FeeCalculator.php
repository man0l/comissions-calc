<?php

namespace App\Calculator;

use App\Contracts\FeeCalculatorInterface;
use App\Contracts\ConfigInterface;

class FeeCalculator implements FeeCalculatorInterface
{
    private const BASE_CURRENCY = 'EUR';
    private $config;

    public function __construct(ConfigInterface $config)
    {
        $this->config = $config;
    }

    public function calculateFee(float $amount, string $currency, float $rate, bool $isEu): float
    {
        $tempAmount = ($currency == self::BASE_CURRENCY || $rate == 0.0) ? $amount : $amount / $rate;
        $feeRate = $isEu ? $this->config->get('eu_fee_rate') : $this->config->get('non_eu_fee_rate');
        return $tempAmount * $feeRate;
    }
}
