# Slim File Cache
A file based cache middleware for the slim framework (3.0). The cache is a simple file based cache, I needed something 
for a very specific situation where I had many external API requests (essentially an API aggregator) 
to make and within a given time period they always made my routes give the same output.
I didn't need a database or anything fancy, and wasn't too fussed about performance.
So a file based cache made a lot of sense.

## Installation
Installation is done via composer
```php
composer require snicholson/slimfilecache "~1.0"
```

## Requirements
This middleware works with slim 3.0 Release Candidate.

## Setup
The middleware conforms to slims normal injection requirements. The example below is the basic setup of the cache.
Once this has been implemented, it will automatically load any active cache records for the route currently being called and 
halt the execution of the application.

```php
//Start up a new slim app
$slim = new App($container);

// Add file cache middleware
$cache = new \SNicholson\SlimFileCache\Cache($slim);
$slim->add($cache);
```

## Usage
To use the cache you simple need to cache the output of your route before returning it. The add method takes up to 6 arguments,
the first one is the route name. The second one is the content, and the third argument is the response code status (e.g. 200) and fourthly is an array of headers to send, the fifth is the duration of the cache in seconds.
The default length is 1 hour. The second argument for the cache is the directory to store the cache files in.
The directory must be writeable to the web user.

```php
//Start up a new slim app
$slim = new App($container);

// Add file cache middleware
$cache = new \SNicholson\SlimFileCache\Cache($slim, 'relate/path/to/cache/directory');
$slim->add($cache);

//Configure the "foo" route and cache the output
$slim->get(
    '/foo',
    function(\Slim\Http\Request $request, \Slim\Http\Response $response, $args) use ($cache) {
      $response = 'foo response string';
      $cache->add('/foo', $response);
      return $response;
    }
);

```

Alternatively you can tell the cache to store all routes called by simple placing the following line after you have
run the slim app
```php
//Run the slim app (like normal)
$slim->run();

//Place the global cache afterwards, the next request at this route will be cached
$cache->add($slim->request->getUri()->getPath(), $slim->response->getBody()->__toString());

```

The cache has a few other simple methods. They are flush, get, remove. They essentially do what they say on the tin:

```php 

$cache = new \SNicholson\SlimFileCache\Cache($slim)

//Flush removes all entries
$cache->flush();

//Remove a single cache entry
$cache->remove('/foo');

//Get returns what is stored in the cache
$response = $cache->get('/foo');

```
