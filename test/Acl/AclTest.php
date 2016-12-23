<?php

use PHPUnit\Framework\TestCase;

use Framewub\Acl\Acl;

class AclTest extends TestCase
{
    public function testAddRole()
    {
        $acl = new Acl();
        $role = $acl->addRole('User');
        $this->assertEquals('User', (string)$role);
    }

    public function testAllow()
    {
        $acl = new Acl();
        $role = $acl->addRole('User');
        $acl->allow('User', 'Things');

        $this->assertFalse($role->isAllowed('Foobar'));
        $this->assertTrue($role->isAllowed('Things'));
    }

    public function testDeny()
    {
        $acl = new Acl();
        $role = $acl->addRole('User');
        $acl->allow('User', 'Things');
        $acl->deny('User', 'Things', 'write');

        $this->assertTrue($role->isAllowed('Things'));
        $this->assertFalse($role->isAllowed('Things', 'write'));
        $this->assertTrue($role->isAllowed('Things', 'read'));
    }

    public function testAllowActions()
    {
        $acl = new Acl();
        $role = $acl->addRole('User');
        $acl->allow('User', 'Things', 'read');

        $this->assertFalse($role->isAllowed('Things'));
        $this->assertFalse($role->isAllowed('Things', 'write'));
        $this->assertTrue($role->isAllowed('Things', 'read'));

        $acl = new Acl();
        $role = $acl->addRole('OtherUser');
        $acl->allow('OtherUser', 'Things');

        $this->assertTrue($role->isAllowed('Things'));
        $this->assertTrue($role->isAllowed('Things', 'write'));
        $this->assertTrue($role->isAllowed('Things', 'read'));
    }

    public function testInherit()
    {
        $acl = new Acl();
        $role = $acl->addRole('User');
        $role2 = $acl->addRole('Admin', 'User');

        $acl->allow('User', 'Things');
        $acl->allow('Admin', 'Foobar');
        $acl->deny('Admin', 'Things', 'write');

        $this->assertFalse($role->isAllowed('Foobar'));
        $this->assertTrue($role2->isAllowed('Things'));
        $this->assertFalse($role2->isAllowed('Things', 'write'));
    }
}
