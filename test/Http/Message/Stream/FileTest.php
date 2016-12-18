<?php

use PHPUnit\Framework\TestCase;

use Framewub\Http\Message\Stream\File;

class FileTest extends TestCase
{
    protected $inputFilename;
    protected $outputFilename;

    public function setUp()
    {
        $this->inputFilename = dirname(dirname(dirname(__DIR__))) . '/data/phpinput';
        $this->outputFilename = dirname(dirname(dirname(__DIR__))) . '/data/phpoutput';
    }

    public function testToString()
    {
        $stream = new File($this->inputFilename, 'r');
        $this->assertEquals('Hello this is my PHP input stream, nice huh?', (string)$stream);
        $stream->close();
    }

    public function testIsReadableWritable()
    {
        $stream = new File($this->inputFilename, 'r');
        $this->assertTrue($stream->isReadable());
        $this->assertFalse($stream->isWritable());
        $stream->close();

        $stream = new File($this->outputFilename, 'w');
        $this->assertFalse($stream->isReadable());
        $this->assertTrue($stream->isWritable());
        $stream->close();
    }

    public function testRead()
    {
        $stream = new File($this->inputFilename, 'r');
        $this->assertEquals('Hello this', $stream->read(10));
        $this->assertEquals(' is my PHP input', $stream->read(16));
        $stream->close();
    }

    public function testWrite()
    {
        $stream = new File($this->outputFilename, 'w');
        $stream->write('Lorem ipsum');
        $stream->close();

        $stream = new File($this->outputFilename, 'r');
        $this->assertEquals('Lorem ipsum', (string)$stream);
        $stream->close();
    }
}