<?php

use PHPUnit\Framework\TestCase;

use Framewub\Http\Message\Stream\PHPOutput;

class PHPOutputTest extends TestCase
{
    public function testWritable()
    {
        $output = new PHPOutput();
        $this->assertTrue($output->isWritable());
        $output->close();
    }

    public function testWrite()
    {
        $output = new PHPOutput();
        ob_start();
        $output->write("Hello world");
        ob_end_clean();
        $this->assertEquals("Hello world", $output->getMockContents());
        $output->close();
    }

    public function testEncoder()
    {
        $output = new PHPOutput('json_encode');
        ob_start();
        $output->write([ "content" => "Hello world" ]);
        ob_end_clean();
        $this->assertEquals('{"content":"Hello world"}', $output->getMockContents());
        $output->close();
    }
}
