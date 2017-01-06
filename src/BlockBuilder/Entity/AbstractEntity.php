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
use Framewub\BlockBuilder\Precomp\Css\Css;

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
     * Collects files for this entity from all configures directories with the
     * specified extension
     *
     * @param array|string $extension
     *   OPTIONAL. One or more extensions to search for. Can be an array or a
     *   comma-separated list of extensions. Note: extensions MUST NOT include
     *   a leading dot. Default value is '*', which searches for any extension.
     *
     * @return array
     *   An array of found file names, with a maximum of three (one for each
     *   configured directory)
     */
    protected function findFiles($extension = '*')
    {
        if (is_array($extension)) {
            $extension = '{' . implode(',', $extension) . '}';
        } else if ($extension != '*') {
            $extension = '{' . $extension . '}';
        }

        $filenames = [
            Config::$globalDir . '/' . $this->path . '/' . $this->name . '.' . $extension,
            Config::$themeDir . '/' . $this->path . '/' . $this->name . '.' . $extension,
            Config::$specificsDir . '/' . $this->path . '/' . $this->name . '.' . $extension
        ];

        $result = [];

        foreach ($filenames as $filename) {
            $files = glob($filename, GLOB_BRACE);
            if (count($files)) {
                $result[] = $files[0];
            }
        }

        return $result;
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

        if (!$content && count($this->children) > 0) {
            $content = '';
            foreach ($this->children as $child) {
                $content .= $child->getPhtml();
            }
        }

        if ($this->name && $this->path) {
            $mods = [];
            foreach ($this->mods as $key => $value) {
                $mods[] = "{$key}-{$value}";
            }

            $data = [
                'mods' => implode(' ', $mods),
                'content' => $content,
                'parent' => ''
            ];

            $files = $this->findFiles([ 'php', 'phtml' ]);

            foreach ($files as $file) {
                $transform = new Phtml($file);
                $data['parent'] = $transform->transform($data);
            }

            return $data['parent'];
        }

        return $content;
    }

    /**
     * Returns the precompiled CSS for this entity
     *
     * @return string
     *   The PHTML
     */
    public function getCss()
    {
        $css = '';
        // $content = $this->content;

        // if (!$content && count($this->children) > 0) {
        //     $content = '';
        //     foreach ($this->children as $child) {
        //         $content .= $child->getPhtml();
        //     }
        // }

        if ($this->name && $this->path) {
            $files = $this->findFiles([ 'css' ]);

            foreach ($files as $file) {
                $precomp = new Css($file);
                $compiled = ltrim($precomp->getCompiled());

                if (preg_match("/^@mode (override|extend)(;|\n)/", $compiled, $match)) {
                    if ($match[1] == 'override') {
                        $css = '';
                    }
                    $compiled = ltrim(substr($compiled, strlen($match[0])));
                }

                $css .= $compiled;
            }
        }

        return $css;
    }
}
