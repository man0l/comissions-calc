<?php
namespace App\Handler;

use App\Contracts\BinCheckerInterface;
use App\Contracts\ExchangeRateInterface;
use App\Contracts\CacheInterface;
use App\Contracts\TransactionHandlerInterface;
use App\Contracts\FeeCalculatorInterface;
class TransactionHandler implements TransactionHandlerInterface
{

    public function __construct(
        private BinCheckerInterface $binChecker,
        private ExchangeRateInterface $exchangeRate,
        private CacheInterface $cache,
        private FeeCalculatorInterface $calculator
    ) {}

    public function handle(array $transaction): void
    {
        $bin = $transaction['bin'];
        try {
            if ($this->cache->get($bin) == '') {
                $isEu = $this->binChecker->lookup($bin);
                $this->cache->set($bin, $isEu);
            } else {
                $isEu = $this->cache->get($bin);
            }
    
            if ($this->cache->get($transaction['currency']) == '') {
                $rate = $this->exchangeRate->getRate($transaction['currency']);
                $this->cache->set($transaction['currency'], $rate);
            } else {
                $rate = $this->cache->get($transaction['currency']);
            }
            
            $fee = $this->calculator->calculateFee($transaction['amount'], $transaction['currency'], $rate, $isEu);
            echo $fee . "\n";
        } catch (\Exception $e) {
            echo $e->getMessage() . "\n";
        }
        
    }

    public function handleAll(array $transactions): void
    {
        foreach ($transactions as $transaction) {
            $this->handle($transaction);
        }
    }
}