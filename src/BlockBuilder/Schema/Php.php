<?php

/**
 * Class te represent a schema loaded from a PHP file
 *
 * @package    framewub/block-builder
 * @author     Wubbo Bos <wubbo@wubbobos.nl>
 * @copyright  Copyright (c) Wubbo Bos
 * @license    GPL
 * @link       https://github.com/wubmeister/framewub
 */

namespace Framewub\BlockBuilder\Schema;

class Php extends AbstractSchema
{
    /**
     * Loads the schema from a file
     *
     * @param string $filename
     *   The path to the file
     */
    public function __construct(string $filename)
    {
        $this->schema = include $filename;
    }
}
