<?php

// Include the Composer autoloader
require_once __DIR__ . '/vendor/autoload.php';
use App\Calculator\FeeCalculator;
use App\File\FileReader;

if (!isset($argv[1])) {
    echo "Usage: php app.php <transactions_file>\n";
    exit(1);
}

$transactionsFile = $argv[1];

$reader = new FileReader($transactionsFile);
$transactions = $reader->read();



var_dump($transactions);

$calc = new FeeCalculator();

echo $calc->calculateFee(0.1);
