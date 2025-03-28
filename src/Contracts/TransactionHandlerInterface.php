<?php
namespace App\Contracts;

interface TransactionHandlerInterface {

    /**
     * Constructor
     * @param BinCheckerInterface $binChecker
     * @param ExchangeRateInterface $exchangeRate
     * @param CacheInterface $cache
     * @param FeeCalculatorInterface $calculator
     */
    public function __construct(
        BinCheckerInterface $binChecker,
        ExchangeRateInterface $exchangeRate,
        CacheInterface $cache,
        FeeCalculatorInterface $calculator
    );

    /**
     * Handle a transaction
     * @param array $transaction
     * @return float
     */
    public function handle(array $transaction): ?float;

    /**
     * Handle all transactions
     * @param array $transactions
     * @return array Array of fees
     */
    public function handleAll(array $transactions): array;
}
