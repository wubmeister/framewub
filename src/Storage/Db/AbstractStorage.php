<?php

/**
 * Abstract database storage class
 *
 * @package    framewub/storage
 * @author     Wubbo Bos <wubbo@wubbobos.nl>
 * @copyright  Copyright (c) Wubbo Bos
 * @license    GPL
 * @link       https://github.com/wubmeister/framewub
 */

namespace Framewub\Storage\Db;

use PDO;
use Framewub\Db\Generic as GenericDb;
use Framewub\Storage\StorageInterface;
use Framewub\Db\Query\AbstractQuery;
use Framewub\Db\Query\Select;
use Framewub\Db\Query\Insert;
use Framewub\Db\Query\Update;
use Framewub\Db\Query\Delete;

/**
 * Abstract database storage class
 */
class AbstractStorage implements StorageInterface
{
    /**
     * The table name
     *
     * @var string
     */
    protected $tableName;

    /**
     * The class of the returned object rows
     *
     * @var string
     */
    protected $objectClass;

    /**
     * The database adapter
     *
     * @var Framewub\Db\Generic
     */
    protected $db;

    public function __construct(GenericDb $db)
    {
        $this->db = $db;
    }

    /**
     * Prepares a PDO statement with bind values
     *
     * @param Framewub\Db\Query\AbstractQuery $query
     *   The SQL query
     *
     * @return PDOStatement
     *   The prepared PDO statement
     */
    protected function prepare(AbstractQuery $query)
    {
        $statement = $this->db->prepare($query, $query->getBind());

        return $statement;
    }

    /**
     * Finds rows in the table matching the specified set of conditions.
     *
     * @param array $where
     *   OPTIONAL. The conditions. If no conditions are specified, all rows in the table are fetched
     * @param string|array $order
     *   OPTIONAL. The order column(s)
     */
    public function find($where = null, $order = null)
    {
        $select = new Select($this->db);
        $select->from($this->tableName);

        if ($where) {
            $select->where($where);
        }
        if ($order) {
            $select->order($order);
        }

        $rowset = new Rowset($select, $this);
        if ($this->objectClass) {
            $rowset->setObjectClass($this->objectClass);
        }
        return $rowset;
    }

    /**
     * Finds a single row in the table matching the specified ID or set of conditions.
     *
     * @param int|string|array $idOrWhere
     *   An ID or set of conditions
     */
    public function findOne($idOrWhere = null)
    {
        $select = new Select($this->db);
        $select->from($this->tableName);

        if (is_array($idOrWhere)) {
            $select->where($idOrWhere);
        } else {
            $select->where([ 'id' => $idOrWhere ]);
        }

        $rowset = new Rowset($select, $this);
        if ($this->objectClass) {
            $rowset->setObjectClass($this->objectClass);
        }
        return $rowset->fetchOne();
    }

    /**
     * Inserts a row in the table
     *
     * @param array $values
     *   The values to insert
     *
     * @return int
     *   The ID of the newly inserted item or null on failure
     */
    public function insert(array $values)
    {
        $insert = new Insert($this->db);
        $insert->into($this->tableName)->values($values);

        $statement = $this->prepare($insert);

        $id = null;
        if ($statement->execute()) {
            $id = $this->db->getPdo()->lastInsertId();
        }

        return $id;
    }

    /**
     * Performs a DELETE or UPDATE, based on the passed argument, and returns the number of affected rows
     *
     * @param Framewub\Db\Query\AbstractQuery $query
     *   The Delete or Update query
     * @param int|string|array
     *   An ID or a set of conditions for the update query. If nothing is specified, all rows in the table will be updated.
     *
     * @return int
     *   The number of affected rows
     */
    protected function deleteOrUpdate(AbstractQuery $query, &$idOrWhere)
    {
        if ($idOrWhere) {
            if (is_array($idOrWhere)) {
                $query->where($idOrWhere);
            } else {
                $query->where([ 'id' => $idOrWhere ]);
            }
        }

        $statement = $this->prepare($query);

        $numRows = 0;
        if ($statement->execute()) {
            $numRows = $statement->rowCount();
        }

        return $numRows;
    }

    /**
     * Updates a row in the table
     *
     * @param array $values
     *   The values to update
     * @param int|string|array
     *   OPTIONAL. An ID or a set of conditions for the update query. If nothing is specified, all rows in the table will be updated.
     *
     * @return int
     *   The number of affected rows
     */
    public function update(array $values, $idOrWhere = null)
    {
        $update = new Update($this->db);
        $update->table($this->tableName)->values($values);

        return $this->deleteOrUpdate($update, $idOrWhere);
    }

    /**
     * Deletes a row from the table
     *
     * @param int|string|array
     *   An ID or a set of conditions for the update query. If nothing is specified, all rows in the table will be updated.
     *
     * @return int
     *   The number of affected rows
     */
    public function delete($idOrWhere = null)
    {
        $delete = new Delete($this->db);
        $delete->from($this->tableName);

        return $this->deleteOrUpdate($delete, $idOrWhere);
    }

    /**
     * Inserts or updates a row in the table. If there is an ID in the values, it will try to update the row with that ID
     *
     * @param array $values
     *   The values to save
     *
     * @return int|string
     *   The ID of the saved element
     */
    public function save(array $values)
    {
        if (isset($values['id'])) {
            $id = $values['id'];
            unset($values['id']);
            $this->update($values, $id);
        } else {
            $id = $this->insert($values);
        }

        return $id;
    }

    /**
     * Gets the PDO object
     *
     * @return PDO
     */
    public function getPdo()
    {
        return $this->db->getPdo();
    }
}