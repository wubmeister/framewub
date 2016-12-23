<?php

use PHPUnit\Framework\TestCase;

use Framewub\Acl\Role;

class RoleTest extends TestCase
{
    public function testRole()
    {
        $role = new Role('User');
        $this->assertEquals('User', (string)$role);
    }

    public function testAllow()
    {
        $role = new Role('User');
        $role->allow('Things');
        $this->assertFalse($role->isAllowed('Foobar'));
        $this->assertTrue($role->isAllowed('Things'));
    }

    public function testDeny()
    {
        $role = new Role('User');
        $role->allow('Things');
        $role->deny('Things', 'write');

        $this->assertTrue($role->isAllowed('Things'));
        $this->assertFalse($role->isAllowed('Things', 'write'));
        $this->assertTrue($role->isAllowed('Things', 'read'));
    }

    public function testAllowActions()
    {
        $role = new Role('User');
        $role->allow('Things', 'read');
        $this->assertFalse($role->isAllowed('Things'));
        $this->assertFalse($role->isAllowed('Things', 'write'));
        $this->assertTrue($role->isAllowed('Things', 'read'));

        $role = new Role('OtherUser');
        $role->allow('Things');
        $this->assertTrue($role->isAllowed('Things'));
        $this->assertTrue($role->isAllowed('Things', 'write'));
        $this->assertTrue($role->isAllowed('Things', 'read'));
    }

    public function testInherit()
    {
        $role = new Role('User');
        $role2 = new Role('Admin', $role);

        $role->allow('Things');
        $role2->allow('Foobar');
        $role2->deny('Things', 'write');

        $this->assertFalse($role->isAllowed('Foobar'));
        $this->assertTrue($role2->isAllowed('Things'));
        $this->assertFalse($role2->isAllowed('Things', 'write'));
    }
}
