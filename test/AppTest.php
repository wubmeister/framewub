<?php

use PHPUnit\Framework\TestCase;

use Framewub\App;
use Framewub\Config;
use Framewub\Container;
use Framewub\Route\Literal;
use Framewub\Route\Router;
use Framewub\Http\Message\ServerRequest;
use Framewub\Http\Message\Response;

class MockCode
{
    public function __invoke(ServerRequest $request)
    {
        $response = new Response();
        $response->getBody()->write('Hello MockCode');
        return $response;
    }
}

class MockCodePlain
{
    public function __invoke(ServerRequest $request)
    {
        return 'Hello MockCodePlain';
    }
}

class AppTest extends TestCase
{
    public function setUp()
    {
        $_SERVER['REQUEST_URI'] = '/';
    }

    public function testHandleRequest()
    {
        $config = new Config([
            'dependencies' => [
                'factories' => [
                    Router::class => function ($container, $name) {
                        $router = Router::fromConfig($container->get('config'));
                        return $router;
                    },
                    App::class => function ($container, $name) {
                        $app = new App($container);
                        $app->setRouter($container->get(Router::class));
                        return $app;
                    }
                ],
                'invokables' => [
                    MockCode::class => MockCode::class,
                    MockCodePlain::class => MockCodePlain::class
                ]
            ],
            'router' => [
                'fallback' => MockCode::class,
                'mockplain' => [
                    'type' => 'Literal',
                    'descriptor' => '/plain',
                    'middleware' => MockCodePlain::class
                ]
            ]
        ]);
        $container = new Container($config->dependencies);
        $container->set('config', $config);

        $app = $container->get(App::class);

        $request = new ServerRequest();
        ob_start();
        $response = $app->handleRequest($request);
        ob_end_clean();
        $this->assertInstanceOf(Response::class, $response);

        $this->assertEquals('Hello MockCode', $response->getBody()->getMockContents());

        ob_start();
        $response = $app->handleRequest($request->withRequestTarget('/plain'));
        ob_end_clean();
        $this->assertInstanceOf(Response::class, $response);

        $this->assertEquals('Hello MockCodePlain', $response->getBody()->getMockContents());
    }
}
