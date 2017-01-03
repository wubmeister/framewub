<?php

/**
 * Session adapter for HTTP requests
 *
 * @package    framewub/session
 * @author     Wubbo Bos <wubbo@wubbobos.nl>
 * @copyright  Copyright (c) Wubbo Bos
 * @license    GPL
 * @link       https://github.com/wubmeister/framewub
 */

namespace Framewub\Session;

class Http implements SessionInterface
{
    /**
     * Starts a session
     */
    public function start()
    {
        session_start();
    }

    /**
     * Returns the session status, which is one of the PHP_SESSION_* constants
     *
     * @link http://php.net/manual/en/function.session-status.php
     * @return int
     */
    public function getStatus()
    {
        return session_status();
    }
}
