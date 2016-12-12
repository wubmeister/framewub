<?php

/**
 * A class that handles application logic
 *
 * @package    framewub
 * @author     Wubbo Bos <wubbo@wubbobos.nl>
 * @copyright  Copyright (c) Wubbo Bos
 * @license    GPL
 * @link       https://github.com/wubmeister/framewub
 */

namespace Framewub;

use Framewub\Route\Router;

/**
 * App class
 */
class App
{
	/**
	 * The router
	 *
	 * @var Framewub\Route\Router;
	 */
	protected $router;

	/**
	 * Handles a request
	 */
	public function handleRequest()
	{
		$result = $this->router->match('/');
		if ($result['code']) {
			$obj = new $result['code']();
			$obj();
		}
	}

	/**
	 * Sets the router to use
	 *
	 * @param Framewub\Route\Router $router
	 *   The router
	 */
	public function setRouter(Router $router)
	{
		$this->router = $router;
	}
}