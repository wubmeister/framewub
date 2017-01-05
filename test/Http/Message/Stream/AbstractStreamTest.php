<?php

use PHPUnit\Framework\TestCase;

use Framewub\Http\Message\Stream\AbstractStream;

class Http_Message_Stream_MockStream extends AbstractStream
{
    public function __construct()
    {
        $this->filename = dirname(dirname(dirname(__DIR__))) . '/data/phpinput';
        parent::__construct();
    }
}

class AbstractStreamTest extends TestCase
{
    public function testToString()
    {
        $stream = new Http_Message_Stream_MockStream();
        $this->assertEquals('Hello this is my PHP input stream, nice huh?', (string)$stream);
    }

    /**
     * @expectedException RuntimeException
     */
    public function testClose()
    {
        $stream = new Http_Message_Stream_MockStream();
        $stream->close();
        $stream->read(10);
    }

    /**
     * @expectedException RuntimeException
     */
    public function testDetach()
    {
        $stream = new Http_Message_Stream_MockStream();
        $this->assertNull($stream->detach());
        $stream->read(10);
    }

    public function testGetSize()
    {
        $stream = new Http_Message_Stream_MockStream();
        $this->assertEquals(44, $stream->getSize());
    }

    public function testGetTell()
    {
        $stream = new Http_Message_Stream_MockStream();
        $this->assertEquals(0, $stream->tell());
    }

    public function testEOF()
    {
        $stream = new Http_Message_Stream_MockStream();
        $this->assertFalse($stream->eof());
    }

    public function testIsSeekable()
    {
        $stream = new Http_Message_Stream_MockStream();
        $this->assertTrue($stream->isSeekable());
    }

    public function testSeek()
    {
        $stream = new Http_Message_Stream_MockStream();
        // Seek from beginning
        $stream->seek(5);
        $this->assertEquals(5, $stream->tell());
        // Seek from current position
        $stream->seek(5, SEEK_CUR);
        $this->assertEquals(10, $stream->tell());
        // Seek from end
        $stream->seek(-5, SEEK_END);
        $this->assertEquals(39, $stream->tell());
    }

    public function testRewind()
    {
        $stream = new Http_Message_Stream_MockStream();
        $stream->rewind();
        $this->assertEquals(0, $stream->tell());
    }

    public function testIsWritable()
    {
        $stream = new Http_Message_Stream_MockStream();
        $this->assertFalse($stream->isWritable());
    }

    /**
     * @expectedException RuntimeException
     */
    public function testWrite()
    {
        $stream = new Http_Message_Stream_MockStream();
        $this->assertEquals(0, $stream->write('Hello'));
    }

    public function testIsReadable()
    {
        $stream = new Http_Message_Stream_MockStream();
        $this->assertTrue($stream->isReadable());
    }

    public function testRead()
    {
        $stream = new Http_Message_Stream_MockStream();
        $this->assertEquals('Hello this', $stream->read(10));
        $this->assertEquals(' is my PHP input', $stream->read(16));
    }

    public function testGetContents()
    {
        $stream = new Http_Message_Stream_MockStream();
        $stream->read(11);
        $this->assertEquals('is my PHP input stream, nice huh?', $stream->getContents());
    }

    public function testGetMetadata()
    {
        $stream = new Http_Message_Stream_MockStream();
        $this->assertFalse($stream->getMetadata('timed_out'));
    }
}