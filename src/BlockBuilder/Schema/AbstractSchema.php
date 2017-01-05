<?php

/**
 * Abstract base class for all schema classes
 *
 * @package    framewub/block-builder
 * @author     Wubbo Bos <wubbo@wubbobos.nl>
 * @copyright  Copyright (c) Wubbo Bos
 * @license    GPL
 * @link       https://github.com/wubmeister/framewub
 */

namespace Framewub\BlockBuilder\Schema;

class AbstractSchema
{
    /**
     * The loaded schema
     *
     * @var array
     */
    protected $schema;

    /**
     * Returns the schema as an array
     *
     * @return array
     */
    public function toArray()
    {
        return $this->schema;
    }
}
