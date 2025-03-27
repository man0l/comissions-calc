<?php

namespace App\Calculator;

class FeeCalculator
{
    public function calculateFee(float $amount): float
    {
        return $amount * 0.01;
    }
}
