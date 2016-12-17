<?php

use PHPUnit\Framework\TestCase;

use Framewub\Http\Message\PHPInputStream;

class PHPInputStreamTest extends TestCase
{
    public function setUp()
    {
        PHPInputStream::mockCliContents('foo=bar&lorem=ipsum');
    }

    public function testMock()
    {
        $input = new PHPInputStream();
        $this->assertEquals('foo=bar&lorem=ipsum', (string)$input);
    }
}
