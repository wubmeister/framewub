<?php

/**
 * Class to represent a block
 *
 * @package    framewub/block-builder
 * @author     Wubbo Bos <wubbo@wubbobos.nl>
 * @copyright  Copyright (c) Wubbo Bos
 * @license    GPL
 * @link       https://github.com/wubmeister/framewub
 */

namespace Framewub\BlockBuilder\Entity;

class Block extends AbstractEntity
{
    /**
     * Constructs an entity with a definition
     *
     * @param array $definition
     *   The definition
     */
    public function __construct(array $definition)
    {
        $this->name = $this->path = $definition['block'];
        parent::__construct($definition);
    }
}
