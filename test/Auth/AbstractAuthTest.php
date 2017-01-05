<?php

use PHPUnit\Framework\TestCase;

use Framewub\Auth\AbstractAuth;

class Auth_MockAuth extends AbstractAuth
{
    public function authenticate(array $credentials)
    {
        $this->setIdentity($credentials);
    }
}

class AbstractAuthTest extends TestCase
{
    public function testConstruct()
    {
        $auth = new Auth_MockAuth();
        $this->assertFalse($auth->hasIdentity());
    }

    public function testAuthenticate()
    {
        $auth = new Auth_MockAuth();
        $auth->authenticate([ 'username' => 'user', 'password' => 'supersecretpassword' ]);
        $this->assertTrue($auth->hasIdentity());
        $this->assertArrayHasKey('auth_identity', $_SESSION);

        $auth2 = new Auth_MockAuth();
        $this->assertTrue($auth2->hasIdentity());
        $identity = $auth2->getIdentity();
        $this->assertInternalType('array', $identity);
        $this->assertEquals('user', $identity['username']);
        $this->assertEquals('supersecretpassword', $identity['password']);
    }

    public function testRemoveIdentity()
    {
        $auth = new Auth_MockAuth();
        $auth->authenticate([ 'username' => 'user', 'password' => 'supersecretpassword' ]);
        $this->assertTrue($auth->hasIdentity());
        $this->assertArrayHasKey('auth_identity', $_SESSION);

        $auth->removeIdentity();
        $this->assertFalse($auth->hasIdentity());
        $this->assertFalse(array_key_exists('auth_identity', $_SESSION));
    }
}
