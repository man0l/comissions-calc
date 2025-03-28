<?php

namespace Tests\Api;

use PHPUnit\Framework\TestCase;
use App\Api\BinChecker;
use App\Config\Config;
use App\Exceptions\BinLookupException;
use App\Contracts\ConfigInterface;
use App\Contracts\HttpClientInterface;

class BinCheckerTest extends TestCase
{
    private $config;
    private $httpClientMock;
    private $binChecker;
    private $apiUrl = 'https://lookup.binlist.net/';


    protected function setUp(): void
    {
        $this->config = new Config();
        $this->httpClientMock = $this->createMock(HttpClientInterface::class);

        $this->binChecker = new BinChecker($this->config, $this->httpClientMock);
    }

    public function testLookupWithEuCountry()
    {
        // Mock API response for a German BIN (DE is in EU)
        $mockResponse = json_encode([
            'country' => [
                'alpha2' => 'DE',
                'name' => 'Germany'
            ]
        ]);

        $this->httpClientMock->expects($this->once())
            ->method('get')
            ->with($this->apiUrl . '45717360')
            ->willReturn($mockResponse);

        $result = $this->binChecker->lookup('45717360');

        $this->assertTrue($result);
    }

    public function testLookupWithNonEuCountry()
    {
        // Mock API response for a US BIN (US is not in EU)
        $mockResponse = json_encode([
            'country' => [
                'alpha2' => 'US',
                'name' => 'United States of America'
            ]
        ]);

        $this->httpClientMock->expects($this->once())
            ->method('get')
            ->with($this->apiUrl . '41174311')
            ->willReturn($mockResponse);

        $result = $this->binChecker->lookup('41174311');
        
        $this->assertFalse($result);
    }

    public function testLookupWithApiError()
    {
        // Mock a failed API response
        $this->httpClientMock->expects($this->once())
            ->method('get')
            ->willReturn(false);

        $this->expectException(BinLookupException::class);
        $this->expectExceptionMessage('BinChecker.php: Country not found');
        
        $this->binChecker->lookup('00000000');
    }

    public function testLookupWithInvalidJsonResponse()
    {
        // Mock an invalid JSON response
        $this->httpClientMock->expects($this->once())
            ->method('get')
            ->willReturn('not a valid json');

        $this->expectException(BinLookupException::class);
        $this->expectExceptionMessage('BinChecker.php: Country not found');
        
        $this->binChecker->lookup('00000000');
    }

    public function testLookupWithNoCountryData()
    {
        // Mock a response without country data
        $mockResponse = json_encode([
            'number' => [
                'length' => 16,
                'luhn' => true
            ],
            'scheme' => 'visa'
        ]);

        $this->httpClientMock->expects($this->once())
            ->method('get')
            ->willReturn($mockResponse);

        $this->expectException(BinLookupException::class);
        $this->expectExceptionMessage('BinChecker.php: Country not found');
        
        $this->binChecker->lookup('12345678');
    }

    public function testLookupWithEmptyCountryData()
    {
        // Mock a response with empty country object
        $mockResponse = json_encode([
            'country' => []
        ]);

        $this->httpClientMock->expects($this->once())
            ->method('get')
            ->willReturn($mockResponse);

        $this->expectException(BinLookupException::class);
        $this->expectExceptionMessage('BinChecker.php: Country not found');
        
        $this->binChecker->lookup('87654321');
    }
}
