<?php
namespace App\Contracts;

interface FileReaderInterface {

    /**
     * @param string $filePath
     */
    public function __construct(string $filePath);
    
    /**
     * Read the file and return an array of json objects
     * @return array
     */
    public function read(): array;
}
