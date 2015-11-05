<?php

namespace SNicholson\SlimFileCache;

class File
{

    private $route;
    private $expires;
    private $content;
    private $headers = [];
    private $status = 200;
    private static $fileRequiredProperties = ['route', 'status', 'content', 'headers', 'expires'];

    public static function create()
    {
        return new File();
    }

    public static function fromString($content)
    {
        $file = new File();
        $fileContents = json_decode($content, true);

        foreach (self::$fileRequiredProperties as $property) {
            if (!isset($fileContents[$property])) {
                throw new \InvalidArgumentException("No $property was set in cache file");
            }
        }
        $file->setRoute($fileContents['route']);
        $file->setStatus($fileContents['status']);
        $file->setContent($fileContents['content']);
        $file->setHeaders($fileContents['headers']);
        $file->setExpires($fileContents['expires']);
        if ($fileContents['expires'] < time() && $fileContents['expires'] !== -1) {
            throw new CacheExpiredException("Cache had expired");
        }
        return $file;
    }

    private function __construct()
    {

    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @return mixed
     */
    public function getExpires()
    {
        return $this->expires;
    }

    /**
     * @param mixed $expires
     */
    public function setExpires($expires)
    {
        $this->expires = $expires;
    }

    /**
     * @return mixed
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * @param mixed $route
     */
    public function setRoute($route)
    {
        $this->route = $route;
    }

    public function toString()
    {
        return json_encode(
            [
                'route'   => $this->getRoute(),
                'status'  => $this->getStatus(),
                'content' => $this->getContent(),
                'headers' => $this->getHeaders(),
                'expires' => $this->getExpires()
            ]
        );
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param int $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @param array $headers
     */
    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
    }
}