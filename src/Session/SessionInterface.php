<?php

/**
 * Interface for session adapters
 *
 * @package    framewub/session
 * @author     Wubbo Bos <wubbo@wubbobos.nl>
 * @copyright  Copyright (c) Wubbo Bos
 * @license    GPL
 * @link       https://github.com/wubmeister/framewub
 */

namespace Framewub\Session;

interface SessionInterface
{
    /**
     * This method should start a session
     */
    public function start();

    /**
     * This method should return the session status, compatible with the
     * PHP_SESSION_* constants
     *
     * @link http://php.net/manual/en/function.session-status.php
     * @return int
     */
    public function getStatus();
}
