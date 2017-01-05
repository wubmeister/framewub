<?php

/**
 * Class to represent layouts
 *
 * @package    framewub/block-builder
 * @author     Wubbo Bos <wubbo@wubbobos.nl>
 * @copyright  Copyright (c) Wubbo Bos
 * @license    GPL
 * @link       https://github.com/wubmeister/framewub
 */

namespace Framewub\BlockBuilder\Entity;

class Layout extends AbstractEntity
{
    /**
     * Constructs an entity with a definition
     *
     * @param array $definition
     *   The definition
     */
    public function __construct(array $definition)
    {
        parent::__construct([ 'content' => $definition ]);
    }
}
