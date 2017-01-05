<?php

/**
 * Class to represent an element
 *
 * @package    framewub/block-builder
 * @author     Wubbo Bos <wubbo@wubbobos.nl>
 * @copyright  Copyright (c) Wubbo Bos
 * @license    GPL
 * @link       https://github.com/wubmeister/framewub
 */

namespace Framewub\BlockBuilder\Entity;

class Element extends AbstractEntity
{
    /**
     * The parent block
     *
     * @var Framewub\BlockBuilder\Entity\Block
     */
    protected $block;

    /**
     * Constructs an entity with a definition
     *
     * @param array $definition
     *   The definition
     */
    public function __construct(array $definition)
    {
        $this->name = $this->path = $definition['element'];
        parent::__construct($definition);
    }

    /**
     * Sets the parent block
     *
     * @param Framewub\BlockBuilder\Entity\Block $block
     */
    public function setParent(AbstractEntity $parent)
    {
        parent::setParent($parent);
        if ($parent instanceof Block) {
            $this->path = $parent->path . '/' . $this->name;
        } else {
            $this->path = $this->name;
        }
    }
}
