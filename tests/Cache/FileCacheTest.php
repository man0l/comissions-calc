<?php

namespace Tests\Cache;

use App\Cache\FileCache;
use PHPUnit\Framework\TestCase;

class FileCacheTest extends TestCase
{
    private $fileCache;
    private $cacheData = [];
    
    protected function setUp(): void
    {
        $this->fileCache = $this->getMockBuilder(FileCache::class)
            ->onlyMethods(['readCacheFile', 'writeCacheFile', 'cacheFileExists'])
            ->getMock();
            
        $this->fileCache->method('cacheFileExists')
            ->willReturnCallback(function() {
                return true;
            });
            
        $this->fileCache->method('readCacheFile')
            ->willReturnCallback(function() {
                return $this->cacheData;
            });
            
        $this->fileCache->method('writeCacheFile')
            ->willReturnCallback(function($data) {
                $this->cacheData = $data;
            });
    }
    
    public function testGetExistingKey()
    {
        $this->cacheData = ['test_key' => 'test_value'];
        
        $value = $this->fileCache->get('test_key');
        
        $this->assertEquals('test_value', $value);
    }
    
    public function testGetNonExistentKey()
    {
        $this->cacheData = ['other_key' => 'other_value'];
        
        $value = $this->fileCache->get('non_existent_key');
        
        $this->assertEquals('', $value);
    }
    
    public function testSetValue()
    {
        $this->cacheData = [];
        
        $this->fileCache->set('new_key', 'new_value');
        
        $this->assertEquals(['new_key' => 'new_value'], $this->cacheData);
    }
    
    public function testUpdateExistingValue()
    {
        $this->cacheData = ['update_key' => 'original_value'];
        
        $this->fileCache->set('update_key', 'updated_value');
        
        $this->assertEquals(['update_key' => 'updated_value'], $this->cacheData);
    }
    
    public function testSetMultipleValues()
    {
        $this->cacheData = [];
        
        $this->fileCache->set('key1', 'value1');
        $this->fileCache->set('key2', 'value2');
        
        $this->assertEquals([
            'key1' => 'value1',
            'key2' => 'value2'
        ], $this->cacheData);
        
        $value1 = $this->fileCache->get('key1');
        $value2 = $this->fileCache->get('key2');
        
        $this->assertEquals('value1', $value1);
        $this->assertEquals('value2', $value2);
    }
    
    public function testGetThrowsExceptionWhenCacheFileDoesNotExist()
    {
        $fileCache = $this->getMockBuilder(FileCache::class)
            ->onlyMethods(['readCacheFile', 'writeCacheFile', 'cacheFileExists'])
            ->getMock();
            
        $fileCache->method('cacheFileExists')
            ->willReturn(false);
            
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Cache file not found');
        
        $fileCache->get('any_key');
    }
}

