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
    }

    /**
     * Tests that when using the static from Args the correct properties are set on the object
     * @test Test From Args Sets Properties
     */
    public function testFromArgsSetsProperties()
    {
        $file = File::fromArgs('route', 'content', 'expires');
        $this->assertEquals('route', $file->getRoute());
        $this->assertEquals('content', $file->getContent());
        $this->assertEquals('expires', $file->getExpires());
    }
}
