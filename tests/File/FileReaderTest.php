<?php

namespace Tests\File;

use App\File\FileReader;
use App\Exceptions\InvalidTransactionException;
use PHPUnit\Framework\TestCase;

class FileReaderTest extends TestCase
{
    private $validJson;
    private $invalidJson;
    private $missingBinJson;
    private $missingAmountJson;
    private $missingCurrencyJson;
    private $nonNumericAmountJson;
    private $invalidCurrencyFormatJson;
    
    protected function setUp(): void
    {
        $this->validJson = implode(PHP_EOL, [
            '{"bin":"45717360","amount":"100.00","currency":"EUR"}',
            '{"bin":"516793","amount":"50.00","currency":"USD"}',
            '{"bin":"45417360","amount":"10000.00","currency":"JPY"}'
        ]);
        
        $this->invalidJson = implode(PHP_EOL, [
            '{"bin":"45717360","amount":"100.00","currency":"EUR"}',
            'this is not valid json',
            '{"bin":"45417360","amount":"10000.00","currency":"JPY"}'
        ]);
        
        $this->missingBinJson = '{"amount":"100.00","currency":"EUR"}';
        
        $this->missingAmountJson = '{"bin":"45717360","currency":"EUR"}';
        
        $this->missingCurrencyJson = '{"bin":"45717360","amount":"100.00"}';
        
        $this->nonNumericAmountJson = '{"bin":"45717360","amount":"not-a-number","currency":"EUR"}';
        
        $this->invalidCurrencyFormatJson = '{"bin":"45717360","amount":"100.00","currency":"EURO"}';
    }
    
    private function createMockFileReader(string $content): FileReader
    {
        $stream = fopen('php://memory', 'r+');
        fwrite($stream, $content);
        rewind($stream);
     
        $fileReader = $this->getMockBuilder(FileReader::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getStream'])
            ->getMock();
        
        $fileReader->method('getStream')
            ->willReturn($stream);
        
        return $fileReader;
    }
    
    public function testReadValidTransactions()
    {
        $fileReader = $this->createMockFileReader($this->validJson);
        $transactions = $fileReader->read();
        
        $this->assertCount(3, $transactions);
        $this->assertEquals('45717360', $transactions[0]['bin']);
        $this->assertEquals('100.00', $transactions[0]['amount']);
        $this->assertEquals('EUR', $transactions[0]['currency']);
    }
    
    public function testInvalidJsonFormat()
    {
        $fileReader = $this->createMockFileReader($this->invalidJson);
        
        $this->expectException(InvalidTransactionException::class);
        $this->expectExceptionMessage('Invalid JSON format');
        
        $fileReader->read();
    }
    
    public function testMissingBin()
    {
        $fileReader = $this->createMockFileReader($this->missingBinJson);
        
        $this->expectException(InvalidTransactionException::class);
        $this->expectExceptionMessage('Missing BIN number');
        
        $fileReader->read();
    }
    
    public function testMissingAmount()
    {
        $fileReader = $this->createMockFileReader($this->missingAmountJson);
        
        $this->expectException(InvalidTransactionException::class);
        $this->expectExceptionMessage('Missing amount');
        
        $fileReader->read();
    }
    
    public function testMissingCurrency()
    {
        $fileReader = $this->createMockFileReader($this->missingCurrencyJson);
        
        $this->expectException(InvalidTransactionException::class);
        $this->expectExceptionMessage('Missing currency');
        
        $fileReader->read();
    }
    
    public function testNonNumericAmount()
    {
        $fileReader = $this->createMockFileReader($this->nonNumericAmountJson);
        
        $this->expectException(InvalidTransactionException::class);
        $this->expectExceptionMessage('Amount must be numeric');
        
        $fileReader->read();
    }
    
    public function testInvalidCurrencyFormat()
    {
        $fileReader = $this->createMockFileReader($this->invalidCurrencyFormatJson);
        
        $this->expectException(InvalidTransactionException::class);
        $this->expectExceptionMessage('Invalid currency format');
        
        $fileReader->read();
    }
} 