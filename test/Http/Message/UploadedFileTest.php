<?php

use PHPUnit\Framework\TestCase;

use Framewub\Http\Message\UploadedFile;
use Framewub\Http\Message\FileStream;

class UploadedFileTest extends TestCase
{
    protected $fileData = [
        'name' => 'foobar.txt',
        'type' => 'text/plain',
        'size' => 42,
        'tmp_name' => '',
        'error' => UPLOAD_ERR_OK
    ];

    public function setUp()
    {
        $this->fileData['tmp_name'] = dirname(dirname(__DIR__)) . '/data/uploaded_file';
    }

    public function testMoveTo()
    {
        $destPath = dirname(dirname(__DIR__)) . '/data/moved_uploaded_file';
        if (file_exists($destPath)) {
            unlink($destPath);
        }

        $file = new UploadedFile($this->fileData);
        $file->moveTo($destPath);
        $this->assertTrue(file_exists($destPath));
    }

    public function testSize()
    {
        $file = new UploadedFile($this->fileData);
        $this->assertEquals(42, $file->getSize());
    }

    public function testError()
    {
        $file = new UploadedFile($this->fileData);
        $this->assertEquals(UPLOAD_ERR_OK, $file->getError());
    }

    public function testStream()
    {
        $file = new UploadedFile($this->fileData);
        $stream = $file->getStream();
        $this->assertInstanceOf(FileStream::class, $stream);
        $this->assertTrue($stream->isReadable());
        $this->assertFalse($stream->isWritable());
    }

    public function testClientFilename()
    {
        $file = new UploadedFile($this->fileData);
        $this->assertEquals('foobar.txt', $file->getClientFilename());
    }

    public function testClientMediaType()
    {
        $file = new UploadedFile($this->fileData);
        $this->assertEquals('text/plain', $file->getClientMediaType());
    }
}
