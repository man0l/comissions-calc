<?php

namespace App\File;

class FileReader
{
    private $fp;

    public function __construct(string $filePath)
    {
        $this->fp = fopen($filePath, 'r');
    }
    
    public function read(): array
    {
        $transactions = [];
        while (($line = fgets($this->fp)) !== false) {
            $transactions[] = json_decode($line, true);
        }
        
        return $transactions;
    }

    public function __destruct()
    {
        fclose($this->fp);
    }
}
