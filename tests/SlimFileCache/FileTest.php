<?php

use SNicholson\SlimFileCache\File;

class FileTest extends PHPUnit_Framework_TestCase
{

    /**
     * Tests that a completely new file has null for each property
     * @test Test Create Sets no Properties
     */
    public function testCreateSetsNoProperties()
    {
        $file = File::create();
        $this->assertEquals(null, $file->getContent());
        $this->assertEquals(null, $file->getExpires());
        $this->assertEquals(null, $file->getRoute());
        $this->assertEquals(200, $file->getStatus());
        $this->assertEquals([], $file->getHeaders());
    }

    /**
     * Tests that when reading from a file, the json is parsed correctly
     * @test Test From String Parses Correctly
     */
    public function testFromStringParsesCorrectly()
    {
        $expires = time() + 3600;
        $file = File::fromString(
            '{"route": "route", "status":200, "content": "content", "headers":[], "expires": ' . $expires . '}'
        );
        $this->assertEquals('route', $file->getRoute());
        $this->assertEquals('content', $file->getContent());
        $this->assertEquals($expires, $file->getExpires());
    }

    /**
     * Tests that invalid files throw exceptions
     * @test Invalid cache files cause exceptions
     * @dataProvider getInvalidFiles
     */
    public function testInvalidCacheFilesCauseException($file)
    {
        $this->setExpectedException('\InvalidArgumentException');
        File::fromString($file);
    }

    /**
     * The data providers for the invalid file tests
     * @return array
     */
    public function getInvalidFiles()
    {
        return [
            [''],
            ['{"route": "oops"}'],
            ['{"route": "route", "contents": "content", "expires": "expires"}'],
            [null]
        ];
    }

    /**
     * @test Test that to string returns a correctly formatted cache file string
     */
    public function testToStringReturnsCorrectly()
    {
        $file = File::create();
        $file->setExpires('sometime');
        $file->setRoute('some-route');
        $file->setContent('some-content');
        $file->setHeaders(['test' => 'test']);
        $file->setStatus(200);
        $this->assertEquals(
            '{"route":"some-route","status":200,"content":"some-content","headers":{"test":"test"},"expires":"sometime"}',
            $file->toString()
        );
    }


    /**
     * @test Test files set to never expire, never actually do!
     */
    public function testFilesSetNeverToExpireNeverDo()
    {
        $fileString = '{"route":"some-route","status":200,"content":"some-content","headers":{},"expires":-1}';
        File::fromString($fileString);
    }
}
