<?php

namespace SNicholson\SlimFileCache;

class File
{

    private $route;
    private $expires;
    private $content;

    public static function create()
    {
        return new File();
    }

    public static function fromArgs($route, $content, $expires)
    {
        $file = new File();
        $file->setRoute($route);
        $file->setContent($content);
        $file->setExpires($expires);
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
}