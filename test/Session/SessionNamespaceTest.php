<?php

use PHPUnit\Framework\TestCase;

use Framewub\Services;
use Framewub\Session\SessionNamespace;

class SessionNamespaceTest extends TestCase
{
    private $session;

    public function setUp()
    {
        $this->session = Services::get('Session');
    }

    public function testCreate()
    {
        $namespace = new SessionNamespace('namespace');
        $this->assertEquals(PHP_SESSION_NONE, $this->session->getStatus());
    }

    public function testSetValue()
    {
        $namespace = new SessionNamespace('namespace');
        $namespace->foo = 'bar';
        $this->assertEquals(PHP_SESSION_ACTIVE, $this->session->getStatus());
        $this->assertInternalType('array', $_SESSION['namespace']);
        $this->assertEquals('bar', $_SESSION['namespace']['foo']);
    }

    public function testArray()
    {
        $namespace = new SessionNamespace('namespace');
        $namespace->foo = new stdClass();
        $this->assertEquals(PHP_SESSION_ACTIVE, $this->session->getStatus());
        $this->assertInternalType('array', $_SESSION['namespace']);
        $this->assertInternalType('object', $_SESSION['namespace']['foo']);

        $namespace->foo->lorem = 'ipsum';
        $this->assertEquals('ipsum', $_SESSION['namespace']['foo']->lorem);
    }
}
