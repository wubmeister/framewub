<?php

use PHPUnit\Framework\TestCase;

use Framewub\Route\AbstractRoute;

class Route_MockRoute extends AbstractRoute
{
    public function match($url)
    {
        return null;
    }

    public function build($params = [])
    {
        return '';
    }
}

class AbstractRouteTest extends TestCase
{
    public function testChildRoutes()
    {
        $route = new Route_MockRoute('foo', 'Foo');

        $route->addChildRoute('bar', new Route_MockRoute('bar', 'Bar'));
    }
}
