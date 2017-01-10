<?php

/**
 * Class for literal routes
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
class Literal extends AbstractRoute
{
	/**
	 * The descriptor for this route
	 *
	 * @var string
	 */
	protected $descriptor;

	/**
	 * The constructor should take a route descriptor and a piece of middleware
	 * (usually a class name).
	 *
	 * @param string $descriptor
	 *   The route descriptor (pattern, resource name, literal, etc)
	 * @param mixed $middleware
	 *   The middleware to map to this route
	 */
	public function __construct(string $descriptor, $middleware)
	{
		parent::__construct($descriptor, $middleware);
		$this->descriptor = $descriptor;
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
		$len = strlen($this->descriptor);
		if (substr($url, 0, $len) == $this->descriptor) {
			$result = [ 'middleware' => $this->middleware, 'params' => [], 'tail' => substr($url, $len) ];
			$this->matchChildRoutes($result['tail'], $result);
			return $result;
		}
		return null;
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
		$url = $this->descriptor . $this->buildChildRoutes($args);
		return $url;
	}
}