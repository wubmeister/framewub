<?php

use PHPUnit\Framework\TestCase;

use Framewub\Route\AbstractRoute;
use Framewub\Route\Literal;

class LiteralTest extends TestCase
{
    public function testMatch()
    {
        $route = new Literal('/foo/bar', 'FooBar');
        $this->assertInstanceOf(AbstractRoute::class, $route);

        // Should not match
        $result = $route->match('/lorem/ipsum');
        $this->assertEquals(null, $result);

        // Should match
        $result = $route->match('/foo/bar');
        $this->assertInternalType('array', $result);
        $this->assertEquals('FooBar', $route->getCode());

        // Should match
        $result = $route->match('/foo/bar/lorem/ipsum');
        $this->assertInternalType('array', $result);
        $this->assertEquals('FooBar', $route->getCode());
    }

    public function testBuild()
    {
        $route = new Literal('/foo/bar', 'FooBar');

        // Should not match
        $result = $route->build();
        $this->assertEquals('/foo/bar', $result);
    }

    public function testChildRoutes()
    {
        $route = new Literal('/foo', 'Foo');
        $route->addChildRoute('bar', new Literal('/bar', 'Bar'));
        $route->addChildRoute('lorem', new Literal('/lorem', 'Lorem'));

        // Should match
        $result = $route->match('/foo/ipsum');
        $this->assertInternalType('array', $result);
        $this->assertEquals('Foo', $result['code']);

        // Should match
        $result = $route->match('/foo/bar');
        $this->assertInternalType('array', $result);
        $this->assertEquals('Bar', $result['code']);

        // Should match
        $result = $route->match('/foo/lorem');
        $this->assertInternalType('array', $result);
        $this->assertEquals('Lorem', $result['code']);
    }
}
