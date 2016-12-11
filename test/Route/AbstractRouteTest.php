<?php

use PHPUnit\Framework\TestCase;

use Framewub\Route\AbstractRoute;

class MockRoute extends AbstractRoute
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
        $route = new MockRoute('foo', 'Foo');

        $route->addChildRoute('bar', new MockRoute('bar', 'Bar'));
    }
}
