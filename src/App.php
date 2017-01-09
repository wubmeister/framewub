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
     * The service container to use
     *
     * @var Framewub\Container;
     */
    protected $container;

    /**
     * Initializes the app with a container
     *
     * @param Framewub\Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

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
        if ($result['middleware']) {
            $obj = $this->container->get($result['middleware']);
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