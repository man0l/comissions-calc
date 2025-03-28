<?php

namespace App\File;

use App\Exceptions\InvalidTransactionException;

class FileReader
{
    private $fp;

    public function __construct(string $filePath)
    {
        $this->fp = fopen($filePath, 'r');
    }
    
    private function validateTransaction(array $transaction): void
    {
        if (!isset($transaction['bin'])) {
            throw new InvalidTransactionException('Missing BIN number');
        }

        if (!isset($transaction['amount'])) {
            throw new InvalidTransactionException('Missing amount');
        }

        if (!isset($transaction['currency'])) {
            throw new InvalidTransactionException('Missing currency');
        }

        if (!is_numeric($transaction['amount'])) {
            throw new InvalidTransactionException('Amount must be numeric');
        }

        if (!preg_match('/^[A-Z]{3}$/', $transaction['currency'])) {
            throw new InvalidTransactionException('Invalid currency format');
        }
    }

    public function read(): array
    {
        $transactions = [];
        while (($line = fgets($this->fp)) !== false) {
            $data = json_decode($line, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new InvalidTransactionException('Invalid JSON format');
            }
            $this->validateTransaction($data);
            $transactions[] = $data;
        }
        
        return $transactions;
    }

    public function __destruct()
    {
        fclose($this->fp);
    }
}
