<?php

namespace Tests\Handler;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use App\Handler\TransactionHandler;
use App\Contracts\BinCheckerInterface;
use App\Contracts\ExchangeRateInterface;
use App\Contracts\CacheInterface;
use App\Contracts\FeeCalculatorInterface;

class TransactionHandlerTest extends TestCase
{
    #[DataProvider('provideTransactionsData')]
    public function testHandleTransaction(
        array $transaction, 
        bool $isEu, 
        float $rate, 
        float $expectedFee
    ): void {
        
        $binChecker = $this->createMock(BinCheckerInterface::class);
        $exchangeRate = $this->createMock(ExchangeRateInterface::class);
        $cache = $this->createMock(CacheInterface::class);
        $calculator = $this->createMock(FeeCalculatorInterface::class);        
        
        $cache->method('get')->willReturn("");
            
        $binChecker->expects($this->once())
            ->method('lookup')
            ->with($transaction['bin'])
            ->willReturn($isEu);
            
        $exchangeRate->expects($this->once())
            ->method('getRate')
            ->with($transaction['currency'])
            ->willReturn($rate);
            
        $cache->method('set');
        
        $calculator->expects($this->once())
            ->method('calculateFee')
            ->with(
                $transaction['amount'],
                $transaction['currency'],
                $rate,
                $isEu
            )
            ->willReturn($expectedFee);
            
        $handler = new TransactionHandler($binChecker, $exchangeRate, $cache, $calculator);
        
        /** @var float|null $result */
        $result = $handler->handle($transaction);
        
        $this->assertEquals($expectedFee, $result);
    }    
    
    public function testInvalidTransaction(): void
    {
        $binChecker = $this->createMock(BinCheckerInterface::class);
        $exchangeRate = $this->createMock(ExchangeRateInterface::class);
        $cache = $this->createMock(CacheInterface::class);
        $calculator = $this->createMock(FeeCalculatorInterface::class);
        
        $handler = new TransactionHandler($binChecker, $exchangeRate, $cache, $calculator);
        
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Missing required transaction field: bin");
        $handler->handle(['amount' => '100.00', 'currency' => 'EUR']);
    }
    
    public function testNonNumericAmount(): void
    {
        $binChecker = $this->createMock(BinCheckerInterface::class);
        $exchangeRate = $this->createMock(ExchangeRateInterface::class);
        $cache = $this->createMock(CacheInterface::class);
        $calculator = $this->createMock(FeeCalculatorInterface::class);
        
        $handler = new TransactionHandler($binChecker, $exchangeRate, $cache, $calculator);
        
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Transaction amount must be numeric");
        $handler->handle(['bin' => '45717360', 'amount' => 'abc', 'currency' => 'EUR']);
    }
    
    public function testExceptionHandling(): void
    {
        $binChecker = $this->createMock(BinCheckerInterface::class);
        $exchangeRate = $this->createMock(ExchangeRateInterface::class);
        $cache = $this->createMock(CacheInterface::class);
        $calculator = $this->createMock(FeeCalculatorInterface::class);
        
        $cache->method('get')->willReturn("");
        $binChecker->method('lookup')->willThrowException(new \Exception("API Error"));
        
        $handler = new TransactionHandler($binChecker, $exchangeRate, $cache, $calculator);
        
        /** @var float|null $result */
        $result = $handler->handle(['bin' => '45717360', 'amount' => '100.00', 'currency' => 'EUR']);
        $this->assertNull($result);
    }
    
    /**
     * Data provider for testHandleTransaction
     * @return array[]
     */
    public static function provideTransactionsData(): array
    {
        return [
            [
                ['bin' => '45717360', 'amount' => '100.00', 'currency' => 'EUR'],
                true, // isEu
                1.0, // rate
                1.0, // expectedFee
            ],
            [
                ['bin' => '516793', 'amount' => '50.00', 'currency' => 'USD'],
                true, // isEu
                1.0676, // rate
                0.47, // expectedFee
            ],
            [
                ['bin' => '45417360', 'amount' => '10000.00', 'currency' => 'JPY'],
                false, // isEu (non-EU)
                143.287409, // rate
                1.4, // expectedFee
            ],            
            [
                ['bin' => '41417360', 'amount' => '130.00', 'currency' => 'USD'],
                false, // isEu (non-EU)
                1.0676, // rate
                2.43, // expectedFee
            ],
            [
                ['bin' => '4745030', 'amount' => '2000.00', 'currency' => 'GBP'],
                true, // isEu
                0.85, // rate
                20.0, // expectedFee
            ],
        ];
    }
} 