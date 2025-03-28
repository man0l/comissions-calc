<?php

namespace Tests\Api;

use App\Api\ExchangeRate;
use App\Config\Config;
use App\Contracts\HttpClientInterface;
use App\Exceptions\ExchangeRateException;
use PHPUnit\Framework\TestCase;

class ExchangeRateTest extends TestCase
{
    private $config;
    private $httpClientMock;
    private $exchangeRate;
    
    protected function setUp(): void
    {
        $this->config = new Config();
        $this->httpClientMock = $this->createMock(HttpClientInterface::class);
        $this->exchangeRate = new ExchangeRate($this->config, $this->httpClientMock);
    }

    public function testGetRateSuccess()
    {
        $mockResponse = json_encode([
            'success' => true,
            'base' => 'EUR',
            'rates' => [
                'USD' => 1.1,
                'GBP' => 0.9
            ]
        ]);

        $this->httpClientMock
            ->expects($this->once())
            ->method('get')
            ->willReturn($mockResponse);

        $rate = $this->exchangeRate->getRate('USD');
        $this->assertEquals(1.1, $rate);
    }

    public function testGetRateHttpFailure()
    {
        $this->httpClientMock
            ->expects($this->once())
            ->method('get')
            ->willReturn(false);

        $this->expectException(ExchangeRateException::class);
        $this->expectExceptionMessage('ExchangeRate.php: HTTP request failed');

        $this->exchangeRate->getRate('USD');
    }

    public function testGetRateInvalidResponse()
    {
        $this->httpClientMock
            ->expects($this->once())
            ->method('get')
            ->willReturn('not valid json');

        $this->expectException(ExchangeRateException::class);
        $this->expectExceptionMessage('ExchangeRate.php: Invalid response');

        $this->exchangeRate->getRate('USD');
    }

    public function testGetRateMissingCurrency()
    {
        $mockResponse = json_encode([
            'success' => true,
            'base' => 'EUR',
            'rates' => [
                'USD' => 1.1,
                'GBP' => 0.9
            ]
        ]);

        $this->httpClientMock
            ->expects($this->once())
            ->method('get')
            ->willReturn($mockResponse);

        $this->expectException(ExchangeRateException::class);
        $this->expectExceptionMessage('ExchangeRate.php: Currency not found');

        $this->exchangeRate->getRate('JPY');
    }
}