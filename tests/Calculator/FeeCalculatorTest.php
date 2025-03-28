<?php

namespace Tests\Calculator;

use App\Calculator\FeeCalculator;
use App\Config\Config;
use PHPUnit\Framework\TestCase;

class FeeCalculatorTest extends TestCase
{
    private $feeCalculator;
    private $config;
    
    protected function setUp(): void
    {
        $this->config = new Config();
        $this->feeCalculator = new FeeCalculator($this->config);
    }
    
    public function testCalculateFeeForEuWithEurCurrency()
    {
        $amount = 100.00;
        $currency = 'EUR';
        $rate = 1.0;
        $isEu = true;
        
        $fee = $this->feeCalculator->calculateFee($amount, $currency, $rate, $isEu);
        
        $this->assertEquals(1.0, $fee);
    }
    
    public function testCalculateFeeForNonEuWithEurCurrency()
    {
        $amount = 100.00;
        $currency = 'EUR';
        $rate = 1.0;
        $isEu = false;
        
        $fee = $this->feeCalculator->calculateFee($amount, $currency, $rate, $isEu);
        
        $this->assertEquals(2.0, $fee);
    }
    
    public function testCalculateFeeForEuWithNonEurCurrency()
    {
        $amount = 130.00;
        $currency = 'USD';
        $rate = 1.1;
        $isEu = true;
        
        $fee = $this->feeCalculator->calculateFee($amount, $currency, $rate, $isEu);
        
        $expected = (130.00 / 1.1) * 0.01;
        $this->assertEquals($expected, $fee);
    }
    
    public function testCalculateFeeForNonEuWithNonEurCurrency()
    {
        $amount = 130.00;
        $currency = 'USD';
        $rate = 1.1;
        $isEu = false;
        
        $fee = $this->feeCalculator->calculateFee($amount, $currency, $rate, $isEu);
        
        $expected = (130.00 / 1.1) * 0.02;
        $this->assertEquals($expected, $fee);
    }
    
    public function testCalculateFeeWithZeroRate()
    {
        $amount = 90.00;
        $currency = 'JPY';
        $rate = 0.0;
        $isEu = true;
        
        $fee = $this->feeCalculator->calculateFee($amount, $currency, $rate, $isEu);
        
        $expected = 90.00 * 0.01;
        $this->assertEquals($expected, $fee);
    }
    
    public function testCalculateFeeWithZeroAmount()
    {
        $amount = 0.0;
        $currency = 'USD';
        $rate = 1.1;
        $isEu = false;
        
        $fee = $this->feeCalculator->calculateFee($amount, $currency, $rate, $isEu);
        
        $this->assertEquals(0.0, $fee);
    }
    
    public function testCalculateFeeWithLargeAmount()
    {
        $amount = 10000.00;
        $currency = 'GBP';
        $rate = 0.9;
        $isEu = true;
        
        $fee = $this->feeCalculator->calculateFee($amount, $currency, $rate, $isEu);
        
        $expected = (10000.00 / 0.9) * 0.01;
        $this->assertEquals($expected, $fee);
    }
} 