<?php

/**
 * The interface for all routes
 *
 * @package    framewub/route
 * @author     Wubbo Bos <wubbo@wubbobos.nl>
 * @copyright  Copyright (c) Wubbo Bos
 * @license    GPL
 * @link       https://github.com/wubmeister/framewub
 */

namespace Framewub\Route;

use Framewub\Config;

/**
 * Route interface
 */
abstract class AbstractRoute
{
	/**
	 * The code to map to this route
	 *
	 * @var mixed
	 */
	protected $code;

	/**
	 * The child routes
	 *
	 * @var array
	 */
	protected $children = [];

	/**
	 * The constructor should take a route descriptor and a piece of code
	 * (usually a class name). Subclasses should decide for themselves what to
	 * do with the descriptor, since the abstract constructor doens't store it
	 * anywhere.
	 *
	 * @param string $descriptor
	 *   The route descriptor (pattern, resource name, literal, etc)
	 * @param mixed $code
	 *   The code to map to this route
	 */
	public function __construct($descriptor, $code)
	{
		$this->code = $code;
	}

	/**
	 * Gets the mapped code. Subclasses can override this method to choose the
	 * code based on the matching state
	 *
	 * @return mixed
	 *   The mapped code
	 */
	public function getCode()
	{
		return $this->code;
	}

	/**
	 * Adds a child route
	 *
	 * @param string $name
	 *   The name of the route
	 * @param Framewub\Route\AbstractRoute $route
	 *   The child route
	 */
	public function addChildRoute($name, AbstractRoute $route)
	{
		$this->children[$name] = $route;
	}

	/**
	 * Matches the URL to all the child routes until one matches.
	 *
	 * @param string $url
	 *   The URL to match
	 * @param array $result
	 *   The array to store the result in
	 *
	 * @return Framewub\Route\AbstractRoute
	 *   The matching route or null if nothing matched
	 */
	protected function matchChildRoutes($url, array &$result)
	{
		foreach ($this->children as $child) {
			if ($res = $child->match($url)) {
				if (count($res['params'])) {
					$result['params'] = array_merge($result['params'], $res['params']);
				}
				$result['code'] = $res['code'];
				$result['tail'] = $res['tail'];
				return $child;
			}
		}
	}

	/**
	 * Builds the child routes. If the first element of $args is a string and an
	 * existing key in the $children property, then that child route will be
	 * built with the remaining arguments.
	 *
	 * @param array $args
	 *   The arguments
	 *
	 * @return string
	 *   The built route
	 */
	protected function buildChildRoutes(&$args)
	{
		$child = array_shift($args);
		if (!is_array($child) && isset($this->children[$child])) {
			return call_user_func_array([ $this->children[$child], 'build' ], $args);
		}
		return '';
	}

    /**
     * Loads routes from a configuration
     *
     * @param Framewub\Config $config
     *   The configuration or router
     */
    public function loadConfig(Config $config)
    {
        foreach ($config as $key => $routeConfig) {
            if ($key == 'fallback') {
                $this->setFallback($routeConfig);
            } else {
                $className = $routeConfig->type;
                if (strpos('\\', $className) === FALSE) {
                    $className = 'Framewub\\Route\\' . $className;
                }
                $route = new $className($routeConfig->descriptor, $routeConfig->code);
                if ($routeConfig->childRoutes) {
                    $route->loadConfig($routeConfig->childRoutes);
                }
                $this->addChildRoute($key, $route);
            }
        }
    }

	/**
	 * Compares the specified URL to this route to see if it matches
	 *
	 * @param string $url
	 *   The URL, starting with a slash ('/')
	 *
	 * @return array
	 *   If the URL matches the route, it returns an array with the code, the
	 *   params and rest of the URL. If the URL doesn't match, it returns null.
	 */
	abstract public function match($url);

	/**
	 * Builds a URL based on this route
	 *
	 * @param array $params
	 *   OPTIONAL. The parameters to use. Defaults to an empty array
	 *
	 * @return string
	 *   The built URL
	 */
	abstract public function build();
}