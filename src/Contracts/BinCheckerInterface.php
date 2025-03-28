<?php
namespace App\Contracts;

interface BinCheckerInterface {

    /**
     * Constructor
     * @param ConfigInterface $config
     */
    public function __construct(ConfigInterface $config, HttpClientInterface $httpClient);

    /**
     * Check if the bin is in the EU
     * @param string $bin
     * @return bool
     */
    public function lookup(string $bin): bool;
}
