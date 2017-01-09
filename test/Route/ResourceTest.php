<?php

use PHPUnit\Framework\TestCase;

use Framewub\Route\AbstractRoute;
use Framewub\Route\Resource;

class ResourceTest extends TestCase
{
    public function testMatch()
    {
        $route = new Resource('things', 'Things');
        $this->assertInstanceOf(AbstractRoute::class, $route);

        // Should not match
        $result = $route->match('/lorem/ipsum');
        $this->assertEquals(null, $result);

        // Should match
        $result = $route->match('/things');
        $this->assertInternalType('array', $result);
        $this->assertEquals('Things', $result['middleware']);

        // Should match
        $result = $route->match('/things/1');
        $this->assertInternalType('array', $result);
        $this->assertEquals('Things', $result['middleware']);
        $this->assertEquals('1', $result['params']['id']);
        $this->assertEquals('1', $result['params']['thing_id']);
    }

    public function testBuild()
    {
        $route = new Resource('things', 'Things');
        $route->addChildRoute('objects', new Resource('objects', 'Objects'));

        // Build without ID
        $result = $route->build();
        $this->assertEquals('/things', $result);

        // Build with ID
        $result = $route->build([ 'id' => 1 ]);
        $this->assertEquals('/things/1', $result);

        // Build with ID
        $result = $route->build([ 'thing_id' => 1 ]);
        $this->assertEquals('/things/1', $result);

        // Build without ID
        $result = $route->build([ 'foo' => 1 ]);
        $this->assertEquals('/things', $result);

        // Build with ID and child route
        $result = $route->build('objects', [ 'thing_id' => 1 ]);
        $this->assertEquals('/things/1/objects', $result);

        // Build with ID and child route with ID
        $result = $route->build('objects', [ 'thing_id' => 1, 'object_id' => 2 ]);
        $this->assertEquals('/things/1/objects/2', $result);
    }
}
