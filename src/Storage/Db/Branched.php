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
}
