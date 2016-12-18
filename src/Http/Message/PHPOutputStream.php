<?php

/**
 * Representation of the php://output stream
 *
 * @package    framewub/http-message
 * @author     Wubbo Bos <wubbo@wubbobos.nl>
 * @copyright  Copyright (c) Wubbo Bos
 * @license    GPL
 * @link       https://github.com/wubmeister/framewub
 */

namespace Framewub\Http\Message;

class PHPOutputStream extends AbstractStream
{
    /**
     * Flag to check if we are in CLI mode
     *
     * @var bool
     */
    protected $cliMode = false;

    /**
     * Constructs an instance by opening the real php://input stream when in
     * server-mode, or mock it using the mock content when in CLI-mode
     */
    public function __construct()
    {
        $this->writable = true;
        $this->readable = false;

        if (substr(php_sapi_name(), 0, 3) == 'cli') {
            $this->filename = 'php://memory';
            $this->file = fopen($this->filename, 'w+');
            $this->cliMode = true;
        } else {
            $this->filename = 'php://output';
            $this->file = fopen($this->filename, 'w');
        }
    }

    /**
     * Performs a write so that output will go to the client or the stdout
     *
     * @param string $string
     *   The string that is to be written.
     *
     * @return int
     *   Returns the number of bytes written to the stream.
     *
     * @throws \RuntimeException on failure.
     */
    public function write($string)
    {
        if ($this->cliMode) {
            echo $string;
        }
        return parent::write($string);
    }

    /**
     * Gets the output written to php://memory in CLI mode
     *
     * @return string
     */
    public function getMockContents()
    {
        if ($this->cliMode) {
            $length = ftell($this->file);
            if ($length > 0) {
                fseek($this->file, 0);
                return fread($this->file, $length);
            }
        }
        return '';
    }
}
