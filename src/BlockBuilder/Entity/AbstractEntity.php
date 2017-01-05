<?php

/**
 * Abstract base class for entities
 *
 * @package    framewub/block-builder
 * @author     Wubbo Bos <wubbo@wubbobos.nl>
 * @copyright  Copyright (c) Wubbo Bos
 * @license    GPL
 * @link       https://github.com/wubmeister/framewub
 */

namespace Framewub\BlockBuilder\Entity;

use Framewub\BlockBuilder\Config;
use Framewub\BlockBuilder\Transform\Phtml;

class AbstractEntity
{
    /**
     * The name of this entity
     *
     * @var string
     */
    protected $name;

    /**
     * The relative path to this entity
     *
     * @var string
     */
    protected $path;

    /**
     * The parent entity
     *
     * @var static
     */
    protected $parent;

    /**
     * The content of this entity
     *
     * @var mixed
     */
    protected $content = '';

    /**
     * The modifiers for this entity
     *
     * @var array
     */
    protected $mods = [];

    /**
     * Child entities
     *
     * @var array
     */
    protected $children = [];

    /**
     * Constructs an entity with a definition
     *
     * @param array $definition
     *   The definition
     */
    public function __construct(array $definition)
    {
        if (isset($definition['mods']) && is_array($definition['mods'])) {
            $this->mods = $definition['mods'];
        }
        if (isset($definition['content'])) {
            if (is_array($definition['content'])) {
                foreach ($definition['content'] as $entity) {
                    if (isset($entity['block'])) {
                        $this->addChild(new Block($entity));
                    } else if (isset($entity['element'])) {
                        $this->addChild(new Element($entity));
                    }
                }
            } else {
                $this->content = $definition['content'];
            }
        }
    }

    /**
     * Adds a child entity
     *
     * @param static $entity
     *   The child entity
     */
    public function addChild(AbstractEntity $entity)
    {
        $entity->setParent($this);
        $this->children[] = $entity;
    }

    /**
     * Sets the entity
     *
     * @param static $entity
     *   The parent entity
     */
    public function setParent(AbstractEntity $entity)
    {
        $this->parent = $entity;
    }

    /**
     * Returns the precompiled PHTML for this entity
     *
     * @return string
     *   The PHTML
     */
    public function getPhtml()
    {
        $content = $this->content;

        if ($this->name && $this->path) {
            $filenames = [
                Config::$globalDir . '/' . $this->path . '/' . $this->name . '.*',
                Config::$themeDir . '/' . $this->path . '/' . $this->name . '.*',
                Config::$specificsDir . '/' . $this->path . '/' . $this->name . '.*'
            ];

            $mods = [];
            foreach ($this->mods as $key => $value) {
                $mods[] = "{$key}-{$value}";
            }
        }

        if (!$content && count($this->children) > 0) {
            $content = '';
            foreach ($this->children as $child) {
                $content .= $child->getPhtml();
            }
        }

        if ($this->name && $this->path) {
            $data = [
                'mods' => implode(' ', $mods),
                'content' => $content,
                'parent' => ''
            ];

            foreach ($filenames as $filename) {
                $files = glob($filename);
                if (count($files)) {
                    $file = $files[0];

                    $transform = new Phtml($file);
                    $data['parent'] = $transform->transform($data);
                }
            }

            return $data['parent'];
        }

        return $content;
    }
}
