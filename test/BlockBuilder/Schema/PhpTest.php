<?php

use PHPUnit\Framework\TestCase;

use Framewub\BlockBuilder\Schema\Php;

class PhpTest extends TestCase
{
    public function testConstruct()
    {
        $schema = new Php(dirname(dirname(__DIR__)) . '/data/bb/schema.php');
        $layout = $schema->toArray();

        $this->assertInternalType('array', $layout);
        $this->assertEquals('mockblock', $layout[0]['block']);
        $this->assertEquals('blue', $layout[0]['mods']['color']);
        $this->assertEquals('mockelement', $layout[0]['content'][0]['element']);
        $this->assertEquals('medium', $layout[0]['content'][0]['mods']['size']);
    }
}
