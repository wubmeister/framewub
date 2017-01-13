<?php

/**
 * Class to represent a branched storage in the database
 *
 * @package    framewub/storage
 * @author     Wubbo Bos <wubbo@wubbobos.nl>
 * @copyright  Copyright (c) Wubbo Bos
 * @license    GPL
 * @link       https://github.com/wubmeister/framewub
 */

namespace Framewub\Storage\Db;

use Framewub\Storage\BranchedObject;
use Framewub\Db\Query\Select;
use Framewub\Storage\BranchedTrait;

class Branched extends AbstractStorage
{
    use BranchedTrait;

    /**
     * The class of the returned object rows
     *
     * @var string
     */
    protected $objectClass = BranchedObject::class;

    /**
     * Fetches a tree by it's root node id
     *
     * @param mixed $id
     *   OPTIONAL. The ID of the root node. If no ID is given, the full tree
     *   from the table will be read.
     *
     * @return BranchedObject
     *   The root node with all of its children
     */
    public function fetchTree($id = null)
    {
        $select = new Select($this->db);

        if ($id !== null) {
            $childLeft = 'child.' . $this->leftKey;
            $parentLeft = $this->db->quoteIdentifier('parent.' . $this->leftKey);
            $parentRight = $this->db->quoteIdentifier('parent.' . $this->rightKey);

            $select
                ->from([ 'parent' => $this->tableName, 'child' => $this->tableName ], 'child.*')
                ->where([ 'parent.id' => $id ])
                ->where([ $childLeft => [ '$between' => [ $parentLeft, $parentRight ] ] ]);
        } else {
            $childLeft = $this->leftKey;
            $select->from($this->tableName);
        }
        $select->order($childLeft);

        $resultSet = $this->findBySelect($select);

        return $this->getTreeFromData($resultSet);
    }

    /**
     * Appends a persistent node as direct child to another, updating the
     * left/right bounds.
     *
     * @param mixed $nodeId
     *   The ID of the node to append
     * @param mixed $parentId
     *   The ID of the parent node, to which the node should be appended
     */
    public function appendNode($nodeId, $parentId)
    {

        $node = $this->findOne($nodeId);
        $parent = $this->findOne($parentId);

        if ($node && $parent) {
            $queries = [];

            // Check if node has bounds
            if ($node->getLeft()) {
                // If so, first make its bounds negative
                $nodeRight = $node->getRight();
                $queries[] = "UPDATE `{$this->tableName}` SET `{$this->leftKey}` = -`{$this->leftKey}`, `{$this->rightKey}` = -`{$this->rightKey}` " .
                    "WHERE `{$this->leftKey}` BETWEEN " . $node->getLeft() . " AND " . $node->getRight();
                // Then shift everything between parent's right and node's orginal right (inclusive) by the size of the node
                $size = $node->getSize();
                if ($node->getLeft() > $parent->getRight()) {
                    $queries[] = "UPDATE `{$this->tableName}` SET `{$this->leftKey}` = `{$this->leftKey}` + {$size} WHERE `{$this->leftKey}` BETWEEN " . $parent->getRight() . " AND " . $node->getRight();
                    $queries[] = "UPDATE `{$this->tableName}` SET `{$this->rightKey}` = `{$this->rightKey}` + {$size} WHERE `{$this->rightKey}` BETWEEN " . $parent->getRight() . " AND " . $node->getRight();
                } else {
                    $queries[] = "UPDATE `{$this->tableName}` SET `{$this->leftKey}` = `{$this->leftKey}` - {$size} WHERE `{$this->leftKey}` BETWEEN " . $node->getRight() . " AND " . $parent->getRight();
                    $queries[] = "UPDATE `{$this->tableName}` SET `{$this->rightKey}` = `{$this->rightKey}` - {$size} WHERE `{$this->rightKey}` BETWEEN " . $node->getRight() . " AND " . $parent->getRight();
                }
                // Then update the negative bounds of the node
                $shift = $node->getLeft() - $parent->getRight();
                $shiftStr = $shift < 0 ? '- ' . (-$shift) : '+ ' . $shift;
                $queries[] = "UPDATE `{$this->tableName}` SET `{$this->leftKey}` = `{$this->leftKey}` {$shiftStr}, `{$this->rightKey}` = `{$this->rightKey}` {$shiftStr} WHERE `{$this->leftKey}` BETWEEN -" . $node->getRight() . " AND -" . $node->getLeft();
                // Then make the bounds of the node postive again
                $queries[] = "UPDATE `{$this->tableName}` SET `{$this->leftKey}` = -`{$this->leftKey}`, `{$this->rightKey}` = -`{$this->rightKey}` " .
                    "WHERE `{$this->leftKey}` BETWEEN " . (-$node->getRight() + $shift) . " AND " . (-$node->getLeft() + $shift);
            } else {
                // If not, shift everything from parent's right to the right by the size of the node (= 2)
                $queries[] = "UPDATE `{$this->tableName}` SET `{$this->leftKey}` = `{$this->leftKey}` + 2 WHERE `{$this->leftKey}` >= " . $node->getRight();
                $queries[] = "UPDATE `{$this->tableName}` SET `{$this->rightKey}` = `{$this->rightKey}` + 2 WHERE `{$this->rightKey}` >= " . $node->getRight();
                // Then update the bounds of the node
                $left = $parent->getRight();
                $right = $left + 1;
                $queries[] = "UPDATE `{$this->tableName}` SET `{$this->leftKey}` = {$left}, `{$this->rightKey}` = {$right} WHERE `id` = {$node->id}";
            }

            $this->db->commitTransaction($queries);
        }
    }
}
