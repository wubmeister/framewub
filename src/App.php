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
use Framewub\Http\Message\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\RequestInterface;

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
	 *
	 * @param Psr\Http\Message\RequestInterface $request
	 *   The request
	 *
	 * @return Psr\Http\Message\ResponseInterface
	 *   The response
	 */
	public function handleRequest(RequestInterface $request)
	{
		$response = null;

		$result = $this->router->match($request->getRequestTarget());
		if ($result['code']) {
			$obj = new $result['code']();
			if (is_array($result['params']) && count($result['params'])) {
				$request = $request->withAttributes($result['params']);
			}
			$response = $obj($request);
		}

		if ($response) {
			if (!($response instanceof ResponseInterface)) {
				$response = new Response($response);
			}
		} else {
			$response = new Response();
		}

		return $response;
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