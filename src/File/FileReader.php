<?php

namespace App\File;

use App\Exceptions\InvalidTransactionException;

class FileReader
{
    private $filePath;
    private $fp;

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }
    
    protected function getStream()
    {
        if ($this->fp === null) {
            $this->fp = fopen($this->filePath, 'r');
        }
        return $this->fp;
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
        $fp = $this->getStream();
        
        while (($line = fgets($fp)) !== false) {
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
        if ($this->fp !== null) {
            fclose($this->fp);
        }
    }
}
