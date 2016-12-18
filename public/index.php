<?php

include('autoload.php');

use Framewub\App;
use Framewub\Route\Router;
use Framewub\Http\Message\Response;

class HandleRequest
{
	public function __invoke()
	{
		$response = new Response();
        $response = $response->withHeader('Content-Type', 'text/plain');
		$response->getBody()->write('Hello');
        return $response;
	}
}

$app = new App();
$router = new Router();
$router->setFallback(HandleRequest::class);
$app->setRouter($router);
$response = $app->handleRequest();

$response->flush();