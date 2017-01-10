<?php

/**
 * Access control list
 *
 * @package    framewub/acl
 * @author     Wubbo Bos <wubbo@wubbobos.nl>
 * @copyright  Copyright (c) Wubbo Bos
 * @license    GPL
 * @link       https://github.com/wubmeister/framewub
 */

namespace Framewub\Acl;

use InvalidArgumentException;

class Acl
{
    /**
     * Roles by name
     *
     * @var array
     */
    protected $roles = [];

    /**
     * Creates a role with the specified name and adds it to the ACL
     *
     * @param string $role
     *   The role name
     * @param string $inherits
     *   OPTIONAL. The role name the new role should inherit from
     *
     * @return Role
     *   The newly created role
     * @throws InvalidArgumentException is the inherit role doesn't exit in the ACL
     */
    public function addRole(string $role, $inherits = null)
    {
        if ($inherits && !isset($this->roles[$inherits])) {
            throw new InvalidArgumentException("The role '{$role}' doesn't exist in the ACL");
        }
        $this->roles[$role] = new Role($role, $inherits ? $this->roles[$inherits] : null);
        return $this->roles[$role];
    }

    /**
     * Allows a role to perform the specified actions to a resource
     *
     * @param string $role
     *   The role name
     * @param string $resource
     *   The resource name
     * @param string|array $actions
     *   OPTIONAL. The action name(s). Default is access to all actions
     *
     * @throws InvalidArgumentException is the role doesn't exit in the ACL
     */
    public function allow(string $role, string $resource, $actions = '*')
    {
        if (!isset($this->roles[$role])) {
            throw new InvalidArgumentException("The role '{$role}' doesn't exist in the ACL");
        }
        $this->roles[$role]->allow($resource, $actions);
    }

    /**
     * Denies a role to perform the specified actions to a resource
     *
     * @param string $role
     *   The role name
     * @param string $resource
     *   The resource name
     * @param string|array $actions
     *   OPTIONAL. The action name(s). Default is access to all actions
     *
     * @throws InvalidArgumentException is the role doesn't exit in the ACL
     */
    public function deny(string $role, string $resource, $actions = '*')
    {
        if (!isset($this->roles[$role])) {
            throw new InvalidArgumentException("The role '{$role}' doesn't exist in the ACL");
        }
        $this->roles[$role]->deny($resource, $actions);
    }
}
