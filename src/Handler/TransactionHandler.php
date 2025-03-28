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

    /**
     * Process a single transaction
     *
     * @param array $transaction Transaction data containing 'bin', 'amount', and 'currency'
     * @return void
     * @throws \InvalidArgumentException If transaction data is invalid
     */
    public function handle(array $transaction): void
    {
        $this->validateTransaction($transaction);
        
        $bin = $transaction['bin'];
        
        try {
            $isEu = $this->getFromCacheOrFetch(
                $bin, 
                fn($key) => $this->binChecker->lookup($key)
            );
            
            $rate = $this->getFromCacheOrFetch(
                $transaction['currency'], 
                fn($currency) => $this->exchangeRate->getRate($currency)
            );
            
            $fee = $this->calculator->calculateFee(
                $transaction['amount'], 
                $transaction['currency'], 
                $rate, 
                $isEu
            );
            
            echo $fee . PHP_EOL;
        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage() . PHP_EOL;
        }
    }

    /**
     * Process multiple transactions
     *
     * @param array $transactions Array of transaction data
     * @return void
     */
    public function handleAll(array $transactions): void
    {
        foreach ($transactions as $transaction) {
            $this->handle($transaction);
        }
    }
    
    /**
     * Get a value from cache or fetch it using the provided callback
     *
     * @param string $key Cache key
     * @param callable $fetchCallback Callback to fetch the value if not in cache
     * @return mixed The cached or freshly fetched value
     */
    private function getFromCacheOrFetch(string $key, callable $fetchCallback): mixed
    {
        $cachedValue = $this->cache->get($key);
        
        if ($cachedValue === '' || $cachedValue === null) {
            $value = $fetchCallback($key);
            $this->cache->set($key, $value);
            return $value;
        }
        
        return $cachedValue;
    }
    
    /**
     * Validate transaction data
     *
     * @param array $transaction Transaction data to validate
     * @throws \InvalidArgumentException If data is invalid
     */
    private function validateTransaction(array $transaction): void
    {
        $requiredFields = ['bin', 'amount', 'currency'];
        
        foreach ($requiredFields as $field) {
            if (!isset($transaction[$field]) || empty($transaction[$field])) {
                throw new \InvalidArgumentException("Missing required transaction field: {$field}");
            }
        }
        
        if (!is_numeric($transaction['amount'])) {
            throw new \InvalidArgumentException("Transaction amount must be numeric");
        }
    }
}