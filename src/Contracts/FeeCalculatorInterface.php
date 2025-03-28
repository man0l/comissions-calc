<?php

namespace App\Contracts;

interface FeeCalculatorInterface
{
    public function calculateFee(float $amount, string $currency, float $rate, bool $isEu): float;
}