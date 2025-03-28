<?php

namespace App\Calculator;

use App\Contracts\FeeCalculatorInterface;

class FeeCalculator implements FeeCalculatorInterface
{
    private const BASE_CURRENCY = 'EUR';

    public function calculateFee(float $amount, string $currency, float $rate, bool $isEu): float
    {
        $tempAmount = ($currency == self::BASE_CURRENCY || $rate == 0.0) ? $amount : $amount / $rate;
        return $tempAmount * ($isEu ? 0.01 : 0.02);
    }
}
