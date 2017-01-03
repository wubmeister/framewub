<?php

/**
 * Session adapter for CLI
 *
 * @package    framewub/session
 * @author     Wubbo Bos <wubbo@wubbobos.nl>
 * @copyright  Copyright (c) Wubbo Bos
 * @license    GPL
 * @link       https://github.com/wubmeister/framewub
 */

namespace Framewub\Session;

class Cli implements SessionInterface
{
    /**
     * The session status
     *
     * @var int
     */
    protected $status = PHP_SESSION_NONE;

    /**
     * Starts a session
     */
    public function start()
    {
        $this->status = PHP_SESSION_ACTIVE;
    }

    /**
     * Returns the session status, which is one of the PHP_SESSION_* constants
     *
     * @link http://php.net/manual/en/function.session-status.php
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }
}
