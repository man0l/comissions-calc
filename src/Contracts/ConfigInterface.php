<?php

namespace App\Contracts;

interface ConfigInterface
{
    public function get(string $key);
}