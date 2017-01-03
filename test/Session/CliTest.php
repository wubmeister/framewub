<?php

use PHPUnit\Framework\TestCase;

use Framewub\Session\SessionInterface;
use Framewub\Session\Cli;

class CliTest extends TestCase
{
    public function testSession()
    {
        $session = new Cli();
        $this->assertInstanceOf(SessionInterface::class, $session);
    }

    public function testStart()
    {
        $session = new Cli();
        $this->assertEquals(PHP_SESSION_NONE, $session->getStatus());
        $session->start();
        $this->assertEquals(PHP_SESSION_ACTIVE, $session->getStatus());
    }
}
