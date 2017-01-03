<?php

/**
 * Class representing a namespace in the SESSION variable
 *
 * @package    framewub/session
 * @author     Wubbo Bos <wubbo@wubbobos.nl>
 * @copyright  Copyright (c) Wubbo Bos
 * @license    GPL
 * @link       https://github.com/wubmeister/framewub
 */

namespace Framewub\Session;

use RuntimeException;
use Framewub\Services;

class SessionNamespace
{
    /**
     * The name of the namespace
     *
     * @var string
     */
    protected $name;

    /**
     * The session adapter
     *
     * @var Framewub\Session\SessionInterface
     */
    protected $session;

    /**
     * Constructs a session namespace with the specified name
     *
     * @param string $namespace
     *   The namespace
     */
    public function __construct(string $namespace)
    {
        $this->name = $namespace;
        $this->session = Services::get('Session');
    }

    /**
     * Checks if the session is started. If not, the session will be started.
     *
     * @throws RuntimeException When sessions are disabled
     */
    protected function ensureSession()
    {
        $status = $this->session->getStatus();
        if ($status != PHP_SESSION_ACTIVE) {
            if ($status == PHP_SESSION_DISABLED) {
                throw new RuntimeException("Sessions are disabled");
            }
            $this->session->start();
        }
    }

    /**
     * Gets a value in the namespace
     *
     * @param string $name
     * @return mixed The value
     */
    public function __get($name)
    {
        return isset($_SESSION[$this->name][$name]) ? $_SESSION[$this->name][$name] : null;
    }

    /**
     * Sets a value in the namespace
     *
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        $this->ensureSession();
        $_SESSION[$this->name][$name] = $value;
    }
}
