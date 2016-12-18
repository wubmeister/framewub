<?php

use PHPUnit\Framework\TestCase;

use Framewub\Http\Message\PHPOutputStream;

class PHPOutputStreamTest extends TestCase
{
    public function testWritable()
    {
        $output = new PHPOutputStream();
        $this->assertTrue($output->isWritable());
        $output->close();
    }

    public function testWrite()
    {
        $output = new PHPOutputStream();
        $output->write("Hello world");
        $this->assertEquals("Hello world", $output->getMockContents());
        $output->close();
    }
}
