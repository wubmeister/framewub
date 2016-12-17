<?php

use PHPUnit\Framework\TestCase;

use Framewub\Http\Message\ServerRequest;
use Framewub\Http\Message\UploadedFile;
use Framewub\Http\Message\PHPInputStream;

class ServerRequestTest extends TestCase
{
    public function setUp()
    {
        PHPInputStream::mockCliContents('{"mode":"json","foo":"bar"}');

        $_POST['foo'] = 'bar';
        $_POST['lorem'] = 'ipsum';
        $_GET['dingen'] = 'zaken';
        $_COOKIE['foo'] = 'bar';
        $_FILES['foobar'] = [
            'name' => 'foobar.txt',
            'type' => 'text/plain',
            'size' => 42,
            'tmp_name' => dirname(dirname(__DIR__)) . '/data/uploaded_file',
            'error' => UPLOAD_ERR_OK
        ];
        $_FILES['multi'] = [
            'name' => [ 'foobar.txt', 'loremipsum.pdf' ],
            'type' => [ 'text/plain', 'application/pdf' ],
            'size' => [ 42, 84 ],
            'tmp_name' => [ dirname(dirname(__DIR__)) . '/data/uploaded_file', dirname(dirname(__DIR__)) . '/data/uploaded_file_2' ],
            'error' => [ UPLOAD_ERR_OK, UPLOAD_ERR_OK ]
        ];
    }

    public function testServerParams()
    {
        $request = new ServerRequest();

        $server = $request->getServerParams();
        foreach ($_SERVER as $key => $value) {
            $this->assertEquals($value, $server[$key]);
        }
    }

    public function testCookieParams()
    {
        $request = new ServerRequest();

        $cookies = $request->getCookieParams();
        foreach ($_COOKIE as $key => $value) {
            $this->assertEquals($value, $cookies[$key]);
        }

        $request2 = $request->withCookieParams([ 'lorem' => 'ipsum' ]);
        $cookies = $request2->getCookieParams();
        $this->assertEquals('ipsum', $cookies['lorem']);
    }

    public function testQueryParams()
    {
        $request = new ServerRequest();

        $query = $request->getQueryParams();
        foreach ($_GET as $key => $value) {
            $this->assertEquals($value, $query[$key]);
        }

        $request2 = $request->withQueryParams([ 'lorem' => 'ipsum' ]);
        $query = $request2->getQueryParams();
        $this->assertEquals('ipsum', $query['lorem']);
    }

    public function testUploadedFiles()
    {
        $request = new ServerRequest();
        $files = $request->getUploadedFiles();
        $this->assertInternalType('array', $files);
        $this->assertEquals('foobar.txt', $files['foobar']->getClientFilename());
        $this->assertInternalType('array', $files['multi']);
        $this->assertEquals('foobar.txt', $files['multi'][0]->getClientFilename());
        $this->assertEquals('loremipsum.pdf', $files['multi'][1]->getClientFilename());

        $newFiles = [ 'dingen' => new UploadedFile([
            'name' => 'dingen.txt',
            'type' => 'text/plain',
            'size' => 42,
            'tmp_name' => dirname(dirname(__DIR__)) . '/data/uploaded_file',
            'error' => UPLOAD_ERR_OK
        ]) ];
        $request2 = $request->withUploadedFiles($newFiles);
        $this->assertEquals('dingen.txt', $request2->getUploadedFiles()['dingen']->getClientFilename());
    }

    public function testParsedBody()
    {
        $request = new ServerRequest();

        $post = $request->getParsedBody();
        foreach ($_POST as $key => $value) {
            $this->assertEquals($value, $post[$key]);
        }

        $request2 = $request->withParsedBody([ 'lorem' => 'ipsum' ]);
        $post = $request2->getParsedBody();
        $this->assertEquals('ipsum', $post['lorem']);

        $request3 = $request->withHeader('Content-Type', 'application/json');
        $post = $request3->getParsedBody();
        $this->assertEquals('json', $post['mode']);
        $this->assertEquals('bar', $post['foo']);
    }

    public function testAttributes()
    {
        $request = new ServerRequest([ 'attr' => 'My attribute' ]);

        $attrs = $request->getAttributes();
        $this->assertEquals('My attribute', $attrs['attr']);
        $this->assertEquals('My attribute', $request->getAttribute('attr'));
        $this->assertEquals(null, $request->getAttribute('attr2'));
        $this->assertEquals('My other attribute', $request->getAttribute('attr2', 'My other attribute'));

        $request2 = $request->withAttribute('lorem', 'ipsum');
        $this->assertEquals('ipsum', $request2->getAttribute('lorem'));

        $request3 = $request2->withoutAttribute('lorem');
        $this->assertEquals(null, $request3->getAttribute('lorem'));
    }
}