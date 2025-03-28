<?php

// Include the Composer autoloader
require_once __DIR__ . '/vendor/autoload.php';

use App\Calculator\FeeCalculator;
use App\File\FileReader;
use App\Api\BinChecker;
use App\Api\ExchangeRate;
use App\Cache\FileCache;
use App\Handler\TransactionHandler;
use App\Config\Config;
use App\Api\CurlHttpClient;
use Symfony\Component\Dotenv\Dotenv;

if (file_exists(__DIR__ . '/.env')) {
    $dotenv = new Dotenv();
    $dotenv->load(__DIR__ . '/.env');
}

if (!isset($argv[1])) {
    echo "Usage: php app.php <transactions_file>\n";
    exit(1);
}

$transactionsFile = $argv[1];

$reader = new FileReader($transactionsFile);
$transactions = $reader->read();

$config = new Config();
$binChecker = new BinChecker($config, new CurlHttpClient());
$exchangeRate = new ExchangeRate($config, new CurlHttpClient());
$calculator = new FeeCalculator();
$cache = new FileCache();

$handler = new TransactionHandler($binChecker, $exchangeRate, $cache, $calculator);
$handler->handleAll($transactions);
