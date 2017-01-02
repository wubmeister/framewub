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
            $this->content = $definition['content'];
        }
    }

    /**
     * Returns the precompiled PHTML for this entity
     *
     * @return string
     *   The PHTML
     */
    public function getPhtml()
    {
        $filenames = [
            Config::$specificsDir . '/' . $this->name . '/' . $this->name . '.*',
            Config::$themeDir . '/' . $this->name . '/' . $this->name . '.*',
            Config::$globalDir . '/' . $this->name . '/' . $this->name . '.*'
        ];

        $mods = [];
        foreach ($this->mods as $key => $value) {
            $mods[] = "{$key}-{$value}";
        }
        $data = [
            'mods' => implode(' ', $mods),
            'content' => $this->content
        ];

        foreach ($filenames as $filename) {
            $files = glob($filename);
            if (count($files)) {
                $file = $files[0];

                $transform = new Phtml($file);
                return $transform->transform($data);
            }
        }
    }
}
