<?php

/**
 * Router class
 *
 * @package    framewub/storage
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
	 * The constructor should take a route descriptor and a piece of code (usually a class name).
	 *
	 * @param string $descriptor
	 *   The route descriptor (pattern, resource name, literal, etc)
	 * @param mixed $code
	 *   The code to map to this route
	 */
	public function __construct($descriptor = null, $code = null)
	{
		parent::__construct('router', 'NUL');
	}

	/**
	 * Compares the specified URL to this route to see if it matches
	 *
	 * @param string $url
	 *   The URL, starting with a slash ('/')
	 *
	 * @return array
	 *   If the URL matches the route, it returns an array with the code, the params and rest of the URL. If the URL doesn't match, it returns null.
	 */
	public function match($url)
	{
		$result = [ 'code' => null, 'params' => [], 'tail' => $url ];
		$this->matchChildRoutes($url, $result);
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
	public function build($params = [])
	{
		return '';
	}
}