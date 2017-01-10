<?php

/**
 * Router class
 *
 * @package    framewub/route
 * @author     Wubbo Bos <wubbo@wubbobos.nl>
 * @copyright  Copyright (c) Wubbo Bos
 * @license    GPL
 * @link       https://github.com/wubmeister/framewub
 */

namespace Framewub\Route;

/**
 * Route interface
 */
class Router extends AbstractRoute
{
    /**
     * The fallback middleware
     *
     * @var string
     */
    protected $fallback;

    /**
     * The constructor should take a route descriptor and a piece of middleware
     * (usually a class name).
     *
     * @param string $descriptor
     *   The route descriptor (pattern, resource name, literal, etc)
     * @param mixed $middleware
     *   The middleware to map to this route
     */
    public function __construct(string $descriptor = '', $middleware = null)
    {
        parent::__construct('router', null);
    }

    /**
     * Compares the specified URL to this route to see if it matches
     *
     * @param string $url
     *   The URL, starting with a slash ('/')
     *
     * @return array
     *   If the URL matches the route, it returns an array with the middleware, the
     *   params and rest of the URL. If the URL doesn't match, it returns null.
     */
    public function match($url)
    {
        $result = [ 'middleware' => null, 'params' => [], 'tail' => $url ];
        $this->matchChildRoutes($url, $result);
        if (!$result['middleware'] && $this->fallback) {
            $result['middleware'] = $this->fallback;
        }
        return $result;
    }

    /**
     * Builds a URL based on this route
     *
     * @param array $params
     *   OPTIONAL. Params are not used for this class
     *
     * @return string
     *   The built URL
     */
    public function build()
    {
        $args = func_get_args();
        return $this->buildChildRoutes($args);
    }

    /**
     * Sets the middleware to fall back on if match() doesn't find any matches
     *
     * @param mixed $middleware
     *   The middleware
     */
    public function setFallback($middleware)
    {
        $this->fallback = $middleware;
    }

    /**
     * Builds a router from a configuration or an unserialized router
     *
     * @param Framewub\Config|Router $config
     *   The configuration or router
     *
     * @return static
     *   The router
     */
    public static function fromConfig($config)
    {
        if ($config->router instanceof Router) {
            return $config->router;
        } else {
            $router = new Router();
            $router->loadConfig($config->router);
            $config->router = $router;
            return $router;
        }
    }
}