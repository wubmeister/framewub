<?php

use PHPUnit\Framework\TestCase;

use Framewub\Util\VarExp;

class VarExpTest extends TestCase
{
    public function testMatch()
    {
        // The expression to test
        $exp = new VarExp('expression {foo} with optional {bar}?');
        $match = $exp->match('expression FOO with optional BAR');

        // Assert
        $this->assertInternalType('array', $match);
        $this->assertArrayHasKey('foo', $match);
        $this->assertEquals('FOO', $match['foo']);
        $this->assertArrayHasKey('bar', $match);
        $this->assertEquals('BAR', $match['bar']);
        $this->assertArrayHasKey('*', $match);
        $this->assertEquals('expression FOO with optional BAR', $match['*']);

        // The expression to test
        $match = $exp->match('expression FOO with optional ');

        // Assert
        $this->assertInternalType('array', $match);
        $this->assertArrayHasKey('foo', $match);
        $this->assertEquals('FOO', $match['foo']);
        $this->assertArrayHasKey('bar', $match);
        $this->assertEquals('', $match['bar']);

        // The expression to test
        $match = $exp->match('not matching expression FOO with optional BAR');
        $this->assertEquals(null, $match);
    }

    public function testMatchTypes()
    {
        // The expression to test
        $exp = new VarExp('expression {foo:int} with optional {bar:string}?');
        $match = $exp->match('expression FOO with optional BAR');

        // Assert
        $this->assertEquals(null, $match);

        // Assert
        $match = $exp->match('expression 1234 with optional BAR');
        $this->assertInternalType('array', $match);
        $this->assertArrayHasKey('foo', $match);
        $this->assertEquals('1234', $match['foo']);
        $this->assertArrayHasKey('bar', $match);
        $this->assertEquals('BAR', $match['bar']);
    }

    public function testStaticMatch()
    {
        // The expression to test
        $match = VarExp::matchPattern('/^expression (.*) with optional (.*)?$/', [ 'foo', 'bar' ], 'expression FOO with optional BAR');

        // Assert
        $this->assertInternalType('array', $match);
        $this->assertArrayHasKey('foo', $match);
        $this->assertEquals('FOO', $match['foo']);
        $this->assertArrayHasKey('bar', $match);
        $this->assertEquals('BAR', $match['bar']);

        // The expression to test
        $match = VarExp::matchPattern('/^expression (.*) with optional (.*)?$/', [ 'foo', 'bar' ], 'expression FOO with optional ');

        // Assert
        $this->assertInternalType('array', $match);
        $this->assertArrayHasKey('foo', $match);
        $this->assertEquals('FOO', $match['foo']);
        $this->assertArrayHasKey('bar', $match);
        $this->assertEquals('', $match['bar']);

        // The expression to test
        $match = VarExp::matchPattern('/^expression (.*) with optional (.*)?$/', [ 'foo', 'bar' ], 'not matching expression FOO with optional BAR');
        $this->assertEquals(null, $match);
    }

    public function testUrlMatch()
    {
        // The expression to test
        $exp = new VarExp('/controller/{action}?/{id}', true);
        $match = $exp->match('/controller/show/1');

        // Assert
        $this->assertInternalType('array', $match);
        $this->assertArrayHasKey('action', $match);
        $this->assertEquals('show', $match['action']);
        $this->assertArrayHasKey('id', $match);
        $this->assertEquals('1', $match['id']);

        // The expression to test
        $match = $exp->match('/controller/1');

        // Assert
        $this->assertInternalType('array', $match);
        $this->assertArrayHasKey('action', $match);
        $this->assertEquals('', $match['action']);
        $this->assertArrayHasKey('id', $match);
        $this->assertEquals('1', $match['id']);

        // The expression to test
        $match = $exp->match('/controller');
        $this->assertEquals(null, $match);
    }

    public function testBuild()
    {
        // The expression to test
        $exp = new VarExp('expression {foo} with optional {bar}?');
        $result = $exp->build([ 'foo' => 'FOO', 'bar' => 'BAR' ]);
        // Assert
        $this->assertEquals('expression FOO with optional BAR', $result);

        // The expression to test
        $result = $exp->build([ 'foo' => 'FOO' ]);
        // Assert
        $this->assertEquals('expression FOO with optional ', $result);

        // The expression to test
        $result = $exp->build([ 'bar' => 'BAR' ]);
        // Assert
        $this->assertEquals('expression {foo} with optional BAR', $result);
    }

    public function testBuildTypes()
    {
        // The expression to test
        $exp = new VarExp('expression {foo:int} with optional {bar:string}?');
        $result = $exp->build([ 'foo' => '1234', 'bar' => 'BAR' ]);
        $this->assertEquals('expression 1234 with optional BAR', $result);
    }

    public function testStaticBuild()
    {
        // The expression to test
        $result = VarExp::buildPattern('expression {foo} with optional {bar}?', [ 'foo' => 'FOO', 'bar' => 'BAR' ]);
        // Assert
        $this->assertEquals('expression FOO with optional BAR', $result);

        // The expression to test
        $result = VarExp::buildPattern('expression {foo} with optional {bar}?', [ 'foo' => 'FOO' ]);
        // Assert
        $this->assertEquals('expression FOO with optional ', $result);

        // The expression to test
        $result = VarExp::buildPattern('expression {foo} with optional {bar}?', [ 'bar' => 'BAR' ]);
        // Assert
        $this->assertEquals('expression {foo} with optional BAR', $result);
    }

    public function testUrlBuild()
    {
        // The expression to test
        $exp = new VarExp('/controller/{action}?/{id}', true);
        $result = $exp->build([ 'action' => 'show', 'id' => 1 ]);
        // Assert
        $this->assertEquals('/controller/show/1', $result);

        // The expression to test
        $result = $exp->build([ 'id' => 1 ]);
        // Assert
        $this->assertEquals('/controller/1', $result);

        // The expression to test
        $result = $exp->build([ 'action' => 'show' ]);
        // Assert
        $this->assertEquals('/controller/show/{id}', $result);
    }

    public function testGetRegex()
    {
        // The expression to test
        $exp = new VarExp('expression {foo} with optional {bar}?');
        // Assert
        $this->assertEquals('/^expression (.*) with optional (.*)?$/', $exp->getRegex());
    }

    public function testGetPattern()
    {
        // The expression to test
        $exp = new VarExp('expression {foo} with optional {bar}?');
        // Assert
        $this->assertEquals('expression {foo} with optional {bar}?', $exp->getPattern());
    }

    public function testGetParams()
    {
        // The expression to test
        $exp = new VarExp('expression {foo} with optional {bar}?');
        $result = $exp->getParams();
        // Assert
        $this->assertInternalType('array', $result);
        $this->assertContains('foo', $result);
        $this->assertContains('bar', $result);

    }
}
