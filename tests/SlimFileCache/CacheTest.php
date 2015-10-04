<?php

use Slim\App;
use Slim\Container;
use Slim\Http\Environment;

class CacheTest extends PHPUnit_Framework_TestCase
{

    /**
     * @test Test cache saves request file in specified directory
     */
    public function testCacheSavesRequestFileInSpecifiedDirectory()
    {
        $slim = $this->getSampleSlimApp('/foo', 'slim-cache.slim');

        // Add file cache middleware
        $slim->add($cache = new \SNicholson\SlimFileCache\Cache($slim));

        //Run the app
        $slim->run(true);

        $cache->add('/foo.cache', 'something', 200);

        //Check that the file was created in the expected place
        $expectedCacheFile = $cache->getDirectory() . sha1('/foo.cache');
        $this->assertTrue(file_exists($expectedCacheFile));
        unlink($expectedCacheFile);
    }

    /**
     * @test Test cache loads request file when present in specified directory
     */
    public function testCacheLoadsRequestFileWhenPresentInSpecifiedDirectory()
    {
        $slim = $this->getSampleSlimApp('/foo', 'slim-cache.slim');
        // Add file cache middleware
        $slim->add($cache = new \SNicholson\SlimFileCache\Cache($slim));

        //Manually clear the cache of any entries
        $cache->flush();

        //Manually create the cache file we expect
        $cache->add('/foo', 'foo-bar', 201);

        $slim->run(true);

        //Check that the file was created in the expected place
        $this->assertEquals('foo-bar', $slim->response->getBody()->__toString());
//        $this->assertEquals(201, $slim->response->getStatusCode());

        $cache->remove('/foo');
    }

    /**
     * @throws Exception
     */
    public function testRemoveWorks()
    {
        $slim = $this->getSampleSlimApp('/foo', 'slim-cache.slim');
        // Add file cache middleware
        $cache = new \SNicholson\SlimFileCache\Cache($slim);
        $cache->add('/foo', 'cached', 200);
        $slim->add($cache);

        $cache->remove('/foo');

        $slim->run(true);

        //Check that the file was created in the expected place
        $this->assertEquals('foo', $slim->response->getBody()->__toString());

        $cache->remove('/foo');
    }

    public function testPurgeRemovesAllCacheEntries()
    {
        $slim = $this->getSampleSlimApp('/foo', 'slim-cache.slim');
        // Add file cache middleware
        $cache = new \SNicholson\SlimFileCache\Cache($slim);
        $cache->add('/foo', 'cached', 200);
        $cache->add('/foo2', 'cached', 200);
        $cache->add('/foo3', 'cached', 200);
        $cache->flush();
        $this->assertFalse($cache->get('/foo'));
        $this->assertFalse($cache->get('/foo2'));
        $this->assertFalse($cache->get('/foo3'));
    }

    /**
     * @param        $uri
     * @param        $serverName
     * @param string $requestMethod
     *
     * @return App
     */
    private function getSampleSlimApp($uri, $serverName, $requestMethod = 'GET')
    {
        $container = new Container(
            [
                'environment' => Environment::mock(
                    [
                        'REQUEST_METHOD' => $requestMethod,
                        'REQUEST_URI'    => $uri,
                        'SERVER_NAME'    => $serverName,
                    ]
                )
            ]
        );
        $slim = new App($container);
        $slim->get('/foo',function(\Slim\Http\Request $request, \Slim\Http\Response $response, $args) {
            return 'foo';
        });
        $slim->get('/bar',function(\Slim\Http\Request $request, \Slim\Http\Response $response, $args) {
            return 'bar';
        });
        return $slim;
    }
}
