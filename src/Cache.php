<?php

namespace SNicholson\SlimFileCache;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App;

/**
 * Class Cache
 * @package SNicholson\SlimFileCache
 */
class Cache
{
    const HOUR = 3600;
    const DAY = 86400;
    const WEEK = 604800;
    const NEVER = -1;

    private $directory;
    private $app;

    public function __construct(App $app, $cacheDirectory = __DIR__ . '/../cache/')
    {
        $this->directory = $cacheDirectory;
        $this->fileHandler = new FileHandler($cacheDirectory);
        $this->app = $app;
    }

    /**
     * Slim required __invoke magic method for middleware
     *
     * @param  \Psr\Http\Message\ServerRequestInterface $request  PSR7 request
     * @param  \Psr\Http\Message\ResponseInterface      $response PSR7 response
     * @param  callable                                 $next     Next middleware
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $next)
    {
        $requestPath = $request->getUri()->getPath();
        /** @var File $cache */
        $cache = $this->get($requestPath);
        if ($cache instanceof File) {
            $response = $response->withStatus($cache->getStatus());
            return $response->getBody()->write($cache->getContent());
        }
        $response = $next($request, $response);
        return $response;
    }

    /**
     * Removes all cache entries in the given directory
     */
    public function flush()
    {
        $this->fileHandler->deleteAll();
    }

    /**
     * Returns the cached string for the given cacheKey
     *
     * @param $cacheKey
     *
     * @return bool|File
     */
    public function get($cacheKey)
    {
        return $this->fileHandler->read($cacheKey);
    }

    /**
     * Adds a cache entry with a given key, content and for a set amount of time
     * The time by default for the cache is an hour
     *
     * @param     $cacheKey
     * @param     $content
     * @param     $status
     * @param int $expires
     *
     * @throws CacheFileSystemException
     */
    public function add($cacheKey, $content, $status = 200, $expires = Cache::HOUR)
    {
        $file = File::create();
        $file->setStatus($status);
        $file->setContent($content);
        $file->setRoute($cacheKey);
        if ($expires > 0) {
            $file->setExpires(time() + $expires);
        } else {
            $file->setExpires($expires);
        }
        $this->fileHandler->write($file);
    }

    /**
     * Removes the cache entry for the given key
     * @param $cacheKey
     */
    public function remove($cacheKey)
    {
        $this->fileHandler->delete($cacheKey);
    }

    /**
     * @return mixed
     */
    public function getDirectory()
    {
        return $this->directory;
    }
}