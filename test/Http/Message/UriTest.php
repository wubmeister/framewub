<?php

use PHPUnit\Framework\TestCase;

use Framewub\Http\Message\Uri;

class UriTest extends TestCase
{
    protected $testUri = 'admin@http://www.example.com:8080/foo/bar?lorem=ipsum&sit=doler#anchor';

    public function testConformsToPSR7()
    {
        $uri = new Uri($this->testUri);
        $this->assertInstanceOf('Psr\Http\Message\UriInterface', $uri);
    }

    public function testToString()
    {
        $uri = new Uri($this->testUri);
        $this->assertEquals($this->testUri, (string)$uri);
    }

    public function testScheme()
    {
        $uri = new Uri($this->testUri);
        $this->assertEquals('http', $uri->getScheme());

        // Test other scheme
        $uri = new Uri(str_replace('http://', 'https://', $this->testUri));
        $this->assertEquals('https', $uri->getScheme());

        // Test normalize
        $uri = new Uri(str_replace('http://', 'HTTP://', $this->testUri));
        $this->assertEquals('http', $uri->getScheme());

        // Test without scheme
        $uri = new Uri(str_replace('http://', '', $this->testUri));
        $this->assertEquals('', $uri->getScheme());

        // Test withScheme
        $uri2 = $uri->withScheme('http');
        $this->assertEquals('http', $uri2->getScheme());
        $this->assertEquals($this->testUri, (string)$uri2);
    }

    public function testHost()
    {
        $uri = new Uri($this->testUri);
        $this->assertEquals('www.example.com', $uri->getHost());

        // Test other scheme
        $uri = new Uri(str_replace('www.example.com', 'localhost', $this->testUri));
        $this->assertEquals('localhost', $uri->getHost());

        // Test IP
        $uri = new Uri(str_replace('www.example.com', '127.0.0.1', $this->testUri));
        $this->assertEquals('127.0.0.1', $uri->getHost());

        // Test without scheme
        $uri = new Uri(str_replace('www.example.com', '', $this->testUri));
        $this->assertEquals('', $uri->getHost());

        // Test withHost
        $uri2 = $uri->withHost('www.example.com');
        $this->assertEquals('www.example.com', $uri2->getHost());
        $this->assertEquals($this->testUri, (string)$uri2);
    }

    public function testPort()
    {
        $uri = new Uri($this->testUri);
        $this->assertEquals(8080, $uri->getPort());

        // Test other port
        $uri = new Uri(str_replace(':8080', ':943', $this->testUri));
        $this->assertEquals(943, $uri->getPort());

        // Test without port
        $uri = new Uri(str_replace(':8080', '', $this->testUri));
        $this->assertEquals(null, $uri->getPort());

        // Test withPort
        $uri2 = $uri->withPort(8080);
        $this->assertEquals(8080, $uri2->getPort());
        $this->assertEquals($this->testUri, (string)$uri2);
    }

    public function testPath()
    {
        $uri = new Uri($this->testUri);
        $this->assertEquals('/foo/bar', $uri->getPath());

        // Test other path
        $uri = new Uri(str_replace('/foo/bar', '/lorem/ipsum', $this->testUri));
        $this->assertEquals('/lorem/ipsum', $uri->getPath());

        // Test rootless
        $uri = new Uri('foo/bar/baz?lorem=ipsum');
        $this->assertEquals('foo/bar/baz', $uri->getPath());

        // Test without path
        $uri = new Uri(str_replace('/foo/bar', '', $this->testUri));
        $this->assertEquals('', $uri->getPath());

        // Test withPath
        $uri2 = $uri->withPath('/foo/bar');
        $this->assertEquals('/foo/bar', $uri2->getPath());
        $this->assertEquals($this->testUri, (string)$uri2);
    }

    public function testQuery()
    {
        $uri = new Uri($this->testUri);
        $this->assertEquals('lorem=ipsum&sit=doler', $uri->getQuery());

        // Test other query
        $uri = new Uri(str_replace('?lorem=ipsum&', '?', $this->testUri));
        $this->assertEquals('sit=doler', $uri->getQuery());

        // Test without query
        $uri = new Uri(str_replace('?lorem=ipsum&sit=doler', '', $this->testUri));
        $this->assertEquals('', $uri->getQuery());

        // Test withQuery
        $uri2 = $uri->withQuery('?lorem=ipsum&sit=doler');
        $this->assertEquals('lorem=ipsum&sit=doler', $uri2->getQuery());
        $this->assertEquals($this->testUri, (string)$uri2);
    }

    public function testFragment()
    {
        $uri = new Uri($this->testUri);
        $this->assertEquals('anchor', $uri->getFragment());

        // Test other fragment
        $uri = new Uri(str_replace('#anchor', '#permalink', $this->testUri));
        $this->assertEquals('permalink', $uri->getFragment());

        // Test without fragment
        $uri = new Uri(str_replace('#anchor', '', $this->testUri));
        $this->assertEquals('', $uri->getFragment());

        // Test withFragment
        $uri2 = $uri->withFragment('#anchor');
        $this->assertEquals('anchor', $uri2->getFragment());
        $this->assertEquals($this->testUri, (string)$uri2);
    }

    public function testAuthority()
    {
        $uri = new Uri($this->testUri);
        $this->assertEquals('admin@www.example.com', $uri->getAuthority());

        // Test other authority
        $uri = new Uri(str_replace('admin@', 'wubbobos@', $this->testUri));
        $this->assertEquals('wubbobos@www.example.com', $uri->getAuthority());

        // Test withour authority
        $uri = new Uri(str_replace('admin@', '', $this->testUri));
        $this->assertEquals('www.example.com', $uri->getAuthority());

        // Test withScheme
        // $uri2 = $uri->withScheme('http');
        // $this->assertEquals('http', $uri2->getAuthority());
        // $this->assertEquals($this->testUri, (string)$uri2);
    }

    public function testUserInfo()
    {
        $uri = new Uri($this->testUri);
        $this->assertEquals('admin', $uri->getUserInfo());

        // Test other authority
        $uri = new Uri(str_replace('admin@', 'wubbobos@', $this->testUri));
        $this->assertEquals('wubbobos', $uri->getUserInfo());

        // Test without authority
        $uri = new Uri(str_replace('admin@', '', $this->testUri));
        $this->assertEquals('', $uri->getUserInfo());

        // Test with password
        $uri = new Uri(str_replace('admin@', 'admin:passwd@', $this->testUri));
        $this->assertEquals('admin:passwd', $uri->getUserInfo());

        // Test withUserInfo
        $uri2 = $uri->withUserInfo('admin');
        $this->assertEquals('admin', $uri2->getUserInfo());
        $this->assertEquals($this->testUri, (string)$uri2);
    }
}
