<?php

namespace SNicholson\SlimFileCache;

class FileHandler
{

    private $directory;

    public function __construct($baseDirectory)
    {
        $this->directory = $baseDirectory;
    }

    /**
     * Saves the File $file to disk in the caches base directory, using a sha has of the route name
     * @param File $file
     *
     * @throws CacheFileSystemException
     */
    public function write(File $file)
    {
        $success = file_put_contents(
            $this->getRouteCachePath($file->getRoute()),
            $file->toString()
        );
        if ($success === false) {
            throw new CacheFileSystemException(
                "Unable to save Cache file to disk in file " . $this->getRouteCachePath($file->getRoute())
            );
        }
    }

    /**
     * See's if the route provided has a cached version, if it does it returns a file object representing the cache.
     * If no file is present it returns false
     * @param $route
     *
     * @return bool|File
     */
    public function read($route)
    {
        $routePath = $this->getRouteCachePath($route);
        if (!file_exists($routePath)) {
            return false;
        }
        try {
            return File::fromString(file_get_contents($routePath));
        } catch (\Exception $e) {
            //Delete the cache file
            unlink($routePath);
            return false;
        }
    }

    /**
     * Removes the cache for a given route
     * @param $route
     */
    public function delete($route)
    {
        if (file_exists($this->getRouteCachePath($route))) {
            unlink($this->getRouteCachePath($route));
        }
    }

    /**
     * Removes all cache entries
     */
    public function deleteAll()
    {
        foreach (scandir($this->directory) as $file) {
            if (!in_array($file, ['.', '..', 'keep'])) {
                unlink($this->directory . '/' . $file);
            }
        }
    }

    /**
     * Provides the file path for a cache file based on the route to the resource
     * @param $route
     *
     * @return string
     */
    public function getRouteCachePath($route)
    {
        return $this->directory . '/' . sha1($route);
    }
}