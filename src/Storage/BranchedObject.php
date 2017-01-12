<?php

/**
 * Class to represent branched (tree model) storage objects
 *
 * @package    framewub/storage
 * @author     Wubbo Bos <wubbo@wubbobos.nl>
 * @copyright  Copyright (c) Wubbo Bos
 * @license    GPL
 * @link       https://github.com/wubmeister/framewub
 */

namespace Framewub\Storage;

class BranchedObject extends StorageObject
{
    /**
     * All the child nodes
     *
     * @var array
     */
    protected $children = [];

    /**
     * Appends a child to the node
     *
     * @param static $child
     */
    public function appendChild(BranchedObject $child)
    {
        $this->children[] = $child;
    }

    /**
     * Returns all the children in an array
     *
     * @return array
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Checks if this object has any children
     *
     * @return bool
     */
    public function hasChildren()
    {
        return count($this->children) > 0;
    }

    /**
     * Returns the size of the node, i.e. the deifference between the left and
     * right bound plus one.
     *
     * @return int
     */
    public function getSize() {
        $size = 2; // Including left and right
        foreach ($this->children as $child) {
            $size += $child->getSize();
        }

        return $size;
    }

    /**
     * Sets the left and right bounds according to how the nodes are ordered
     * at the moment.
     *
     * @param int $startLeft
     *   OPTIONAL. The left bound of the root. Defaults to 1.
     */
    public function resetTreeLayout(int $startLeft = 1)
    {
        $leftKey = $this->storage->getLeftKey();
        $rightKey = $this->storage->getRightKey();

        $this->{$leftKey} = $startLeft;
        $this->{$rightKey} = $startLeft + $this->getSize() - 1;

        $startLeft++;
        foreach ($this->children as $child) {
            $child->resetTreeLayout($startLeft);
            $startLeft += $child->getSize();
        }
    }
}
