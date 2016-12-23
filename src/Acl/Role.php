<?php

/**
 * Class to represent a role in the ACL
 *
 * @package    framewub/acl
 * @author     Wubbo Bos <wubbo@wubbobos.nl>
 * @copyright  Copyright (c) Wubbo Bos
 * @license    GPL
 * @link       https://github.com/wubmeister/framewub
 */

namespace Framewub\Acl;

class Role
{
    /**
     * The name of the role
     *
     * @var string
     */
    protected $name;

    /**
     * The role this role inherits from
     *
     * @var Acl\Role
     */
    protected $inherits;

    /**
     * The defined access to resources
     *
     * @var array
     */
    protected $access = [];

    /**
     * Constructs a role with the given name
     *
     * @param string $name
     *   The name of the role
     * @param string $inherits
     *   The role this role inherits from
     */
    public function __construct($name, $inherits = null)
    {
        $this->name = $name;
        $this->inherits = $inherits;
    }

    /**
     * Allows the role to perform the specified actions to a resource
     *
     * @param string $resource
     *   The resource name
     * @param string|array $actions
     *   OPTIONAL. The action name(s). Default is access to all actions
     */
    public function allow($resource, $actions = '*')
    {
        if (!is_array($actions)) {
            $actions = [ $actions ];
        }
        if (!array_key_exists($resource, $this->access)) {
            $this->access[$resource] = [];
        }
        foreach ($actions as $action) {
            $this->access[$resource][$action] = true;
        }
    }

    /**
     * Denies the role to perform the specified actions to a resource
     *
     * @param string $resource
     *   The resource name
     * @param string|array $actions
     *   OPTIONAL. The action name(s). Default is access to all actions
     */
    public function deny($resource, $actions = '*')
    {
        if (!is_array($actions)) {
            $actions = [ $actions ];
        }
        if (!array_key_exists($resource, $this->access)) {
            $this->access[$resource] = [];
        }
        foreach ($actions as $action) {
            $this->access[$resource][$action] = false;
        }
    }

    /**
     * Checks if this role is allowed access to the specified resource
     *
     * @param string $resource
     *   The resource name
     * @param string $action
     *   The action name
     *
     * @return bool
     *   Returns true if access is granted, false if access is denied
     */
    public function isAllowed($resource, $action = '*')
    {
        if (array_key_exists($resource, $this->access)) {
            if (array_key_exists($action, $this->access[$resource])) {
                return $this->access[$resource][$action];
            } else if ($action != '*' && array_key_exists('*', $this->access[$resource])) {
                return $this->access[$resource]['*'];
            }
        }

        if ($this->inherits) {
            return $this->inherits->isAllowed($resource, $action);
        }

        return false;
    }

    /**
     * Returns the name of the role
     *
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }
}
