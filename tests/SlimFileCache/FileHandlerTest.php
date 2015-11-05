<?php

use SNicholson\SlimFileCache\File;
use SNicholson\SlimFileCache\FileHandler;

class FileHandlerTest extends PHPUnit_Framework_TestCase
{
    private $testCachePath = __DIR__ . '/../../cache/';

    /**
     * Tests that when you call write on the file handler the file is saved to disk in the right place with the right
     * name
     * @throws \SNicholson\SlimFileCache\CacheFileSystemException
     */
    public function testWriteFileSavesFileToDisk()
    {
        $fileHandler = $this->getTestFileHandler();
        $file = File::create();
        $file->setRoute('sampleWriteFile');
        $file->setContent('someContent');
        $file->setExpires(time() + 3600);
        $fileHandler->write($file);
        $this->assertTrue(
            $exist = file_exists( $this->testCachePath . sha1('sampleWriteFile'))
        );
        if ($exist) {
            unlink($this->testCachePath . sha1('sampleWriteFile'));
        }
    }

    /**
     * Tests that reading a cache returns false when the cache doesn't exist
     */
    public function testReadReturnsFalseWhenFileDoesNotExist()
    {
        $fileHandler = $this->getTestFileHandler();
        $this->assertFalse($fileHandler->read('someRouteThatNoOneWouldEverUse123!!'));
    }

    /**
     * Tests reading a saved cache file works correctly
     * @throws \SNicholson\SlimFileCache\CacheFileSystemException
     */
    public function testReadParsesFile()
    {
        $fileHandler = $this->getTestFileHandler();
        $file = File::create();
        $file->setRoute('sampleWriteFile');
        $file->setContent('someContent');
        $file->setExpires(time() + 3600);
        $fileHandler->write($file);
        $this->assertEquals($file, $fileHandler->read('sampleWriteFile'));
        unlink($this->testCachePath . sha1('sampleWriteFile'));
    }

    /**
     * Tests expired caches are removed when they are read
     * @throws \SNicholson\SlimFileCache\CacheFileSystemException
     */
    public function testInvalidCachesAreDeleted()
    {
        $fileHandler = $this->getTestFileHandler();
        $file = File::create();
        $file->setRoute('sampleWriteFile');
        $file->setContent('someContent');
        $file->setExpires(time() - 100);
        $fileHandler->write($file);
        $this->assertTrue(file_exists($this->testCachePath . sha1('sampleWriteFile')));
        $this->assertFalse($fileHandler->read('sampleWriteFile'));
        $this->assertFalse(file_exists($this->testCachePath . sha1('sampleWriteFile')));
    }

    /**
     * Tests that delete removes a cache entry if present
     */
    public function testDeleteRemovesCacheIfPresent()
    {
        $fileHandler = $this->getTestFileHandler();
        $file = File::create();
        $file->setRoute('sampleWriteFile');
        $file->setContent('someContent');
        $file->setExpires(time() + 100);
        $fileHandler->write($file);
        $this->assertTrue(file_exists($this->testCachePath . sha1('sampleWriteFile')));
        $fileHandler->delete('sampleWriteFile');
        $this->assertFalse(file_exists($this->testCachePath . sha1('sampleWriteFile')));
    }

    /**
     * @return FileHandler
     */
    private function getTestFileHandler()
    {
        return new FileHandler( __DIR__ . '/../../cache');
    }
}
