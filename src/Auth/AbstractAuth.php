<?php

/**
 * Abstract base class for authentication services
 *
 * @package    framewub/auth
 * @author     Wubbo Bos <wubbo@wubbobos.nl>
 * @copyright  Copyright (c) Wubbo Bos
 * @license    GPL
 * @link       https://github.com/wubmeister/framewub
 */

namespace Framewub\Auth;

abstract class AbstractAuth
{
    /**
     * The authenticated identity
     *
     * @var mixed
     */
    protected $identity;

    /**
     * Constructor tries to pull the identity from the session and store it in
     * $this->identity
     */
    public function __construct()
    {
        if (isset($_SESSION['auth_identity'])) {
            $this->identity = unserialize(base64_decode($_SESSION['auth_identity']));
        }
    }

    /**
     * Checks if and identity has authenticated
     *
     * @return bool
     *   Returns true if an identity has authenticated, false if not
     */
    public function hasIdentity()
    {
        return $this->identity != null;
    }

    /**
     * Returns the authenticated identity, if any
     *
     * @return mixed|null
     *   Returns the authenticated identity, if any. Returns null if no identity
     *   is authenticated
     */
    public function getIdentity()
    {
        return $this->identity;
    }

    /**
     * Removes the authenticated identity, meaning that after calling this method
     * no identity is considered authenticated
     */
    public function removeIdentity()
    {
        $this->identity = null;
        unset($_SESSION['auth_identity']);
    }

    /**
     * Sets the identity and stores it in the session
     *
     * @param mixed $identity
     *   The identity
     */
    protected function setIdentity($identity)
    {
        $this->identity = $identity;
        $_SESSION['auth_identity'] = base64_encode(serialize($identity));
    }

    /**
     * Authenticates an identity with the given credentials.
     * If the credentials are correct, after this method 'hasIdentity' should
     * return true and 'getIdentity' should return the identity matching these
     * credentials
     *
     * @param array $credentials
     *   The credentials
     */
    abstract public function authenticate(array $credentials);
}
