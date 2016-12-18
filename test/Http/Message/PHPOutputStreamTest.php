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
}
