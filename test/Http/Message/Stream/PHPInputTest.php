<?php

use PHPUnit\Framework\TestCase;

use Framewub\Http\Message\Stream\PHPInput;

class PHPInputTest extends TestCase
{
    public function setUp()
    {
        PHPInput::mockCliContents('foo=bar&lorem=ipsum');
    }

    public function testMock()
    {
        $input = new PHPInput();
        $this->assertEquals('foo=bar&lorem=ipsum', (string)$input);
    }
}
