<?php

/**
 * Abstract storage class for branched (tree model) storages
 *
 * @package    framewub/storage
 * @author     Wubbo Bos <wubbo@wubbobos.nl>
 * @copyright  Copyright (c) Wubbo Bos
 * @license    GPL
 * @link       https://github.com/wubmeister/framewub
 */

namespace Framewub\Storage;

/**
 * Abstract database storage class
 */
abstract class AbstractBranchedStorage implements StorageInterface
{
    /**
     * The key in the data that represents the left bound of a node in a nested
     * set.
     *
     * @var string
     */
    protected $leftKey = 'left';

    /**
     * The key in the data that represents the right bound of a node in a nested
     * set.
     *
     * @var string
     */
    protected $rightKey = 'right';

    /**
     * Returns the key in the data that represents the left bound of a node in a
     * nested set.
     *
     * @return string
     */
    public function getLeftKey()
    {
        return $this->leftKey;
    }

    /**
     * Returns the key in the data that represents the right bound of a node in
     * a nested set.
     *
     * @return string
     */
    public function getRightKey()
    {
        return $this->rightKey;
    }

    /**
     * Builds a tree from a nested set
     *
     * @param array|Resultset $data
     *   The nested set
     *
     * @return StorageObject
     *   The root
     */
    public function getTreeFromData($data)
    {
        $root = $top = new BranchedObject($this);
        $right = -1;
        $topStack = [];

        $leftKey = $this->leftKey;
        $rightKey = $this->rightKey;

        foreach ($data as $item) {
            $top->appendChild($item);
            if ($item->{$rightKey} > $item->{$leftKey} + 1) {
                $topStack[] = $top;
                $top = $item;
            } else if ($top->{$rightKey} !== null && $item->right == $top->{$rightKey} - 1) {
                $oldRight = $item->{$rightKey};
                while ($oldRight == $top->{$rightKey} - 1) {
                    $top = array_pop($topStack);
                    $oldRight = $top->{$rightKey};
                }
            }
        }

        if (count($root->getChildren()) == 1) {
            $root = $root->getChildren()[0];
        }
        return $root;
    }
}