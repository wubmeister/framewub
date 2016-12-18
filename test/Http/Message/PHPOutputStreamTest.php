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
        ob_start();
        $output->write("Hello world");
        ob_end_clean();
        $this->assertEquals("Hello world", $output->getMockContents());
        $output->close();
    }

    public function testEncoder()
    {
        $output = new PHPOutputStream('json_encode');
        ob_start();
        $output->write([ "content" => "Hello world" ]);
        ob_end_clean();
        $this->assertEquals('{"content":"Hello world"}', $output->getMockContents());
        $output->close();
    }
}
