<?php

include('autoload.php');

use Framewub\App;
use Framewub\Route\Router;

class HandleRequest
{
	public function __invoke()
	{
		header('Content-Type: text/plain');
		echo 'Hello';
	}
}

$app = new App();
$router = new Router();
$router->setFallback(HandleRequest::class);
$app->setRouter($router);
$app->handleRequest();