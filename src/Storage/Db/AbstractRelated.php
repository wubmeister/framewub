<?php

/**
 * Abstract base class for database storages with relations
 *
 * @package    framewub/storage
 * @author     Wubbo Bos <wubbo@wubbobos.nl>
 * @copyright  Copyright (c) Wubbo Bos
 * @license    GPL
 * @link       https://github.com/wubmeister/framewub
 */

namespace Framewub\Storage\Db;

use InvalidArgumentException;
use Framewub\Util;
use Framewub\Db\Query\Select;

class AbstractRelated extends AbstractStorage
{
    /**
     * Defines a one-to-one relationship
     */
    const ONE_TO_ONE = 1;

    /**
     * Defines a one-to-many relationship
     */
    const ONE_TO_MANY = 2;

    /**
     * Defines a many-to-one relationship
     */
    const MANY_TO_ONE = 3;

    /**
     * Defines a many-to-many relationship
     */
    const MANY_TO_MANY = 4;

    /**
     * The relations for this storage
     *
     * @var array
     */
    protected $relatons = [];

    /**
     * Finds all records in the storage related to an object with the given ID
     * and the given relationship
     *
     * @param string $relation
     *   The key of the relation, as it is defined in the $relations property.
     * @param mixed $id
     *   The ID of the related object
     *
     * @return Framewub\Storage\Db\Rowset
     *   The rowset with the results
     */
    protected function findByRelated($relation, $otherId)
    {
        if (isset($this->relations[$relation])) {
            extract($this->relations[$relation]);

            $select = new Select($this->db);
            $select->from($this->tableName);
            $storageObj = new $storage($this->db);

            switch ($type) {
                case self::MANY_TO_ONE:
                    $select->where([ $fkToOther => $otherId ]);
                    break;

                case self::ONE_TO_MANY:
                    $fk = $storageObj->tableName.'.id';
                    $select
                        ->from($storageObj->tableName, [])
                        ->joinLeft($this->tableName, $this->tableName.'.id = '.$storageObj->tableName.'.'.$fkToSelf)
                        ->where([ $fk => $otherId ]);
                    break;

                default:
                    throw new InvalidArgumentException("Relationship type not implemented");

            }

            return $this->findBySelect($select);
        }

        return null;
    }

    /**
     * Finds all records in related to an object in this with the given ID
     * and the given relationship
     *
     * @param string $relation
     *   The key of the relation, as it is defined in the $relations property.
     * @param mixed $id
     *   The ID of the object in this storage
     *
     * @return Framewub\Storage\Db\Rowset|null
     *   The rowset with the results of null if nothing was found
     */
    protected function findRelated($relation, $id)
    {
        if (!isset($this->relations[$relation])) {
            $relation = Util::getPlural($relation);
        }

        if (isset($this->relations[$relation])) {
            extract($this->relations[$relation]);

            $storageObj = new $storage($this->db);
            $select = new Select($this->db);
            $select->from($storageObj->tableName);

            switch ($type) {
                case self::ONE_TO_MANY:
                    $select->where([ $fkToSelf => $id ]);
                    break;

                case self::MANY_TO_ONE:
                    $fk = $this->tableName.'.id';
                    $select
                        ->from($this->tableName, [])
                        ->joinLeft($storageObj->tableName, $storageObj->tableName.'.id = '.$this->tableName.'.'.$fkToOther)
                        ->where([ $fk => $id ]);
                    break;

            }

            return $storageObj->find($where);
        }

        return null;
    }

    /**
     * Handles all 'findBy*', 'find*', 'add*' and 'remove*' function calls.
     *
     * @param string $name
     *   The function name
     * @param array $args
     *   The arguments
     *
     * @return mixed
     *   Whatever the sub-function returns
     */
    public function __call($name, $args)
    {
        if (substr($name, 0, 6) == 'findBy' && strlen($name) > 6) {
            $relName = Util::getPlural(lcfirst(substr($name, 6)));
            return $this->findByRelated($relName, $args[0]);
        } else if (substr($name, 0, 4) == 'find' && strlen($name) > 4) {
            $relName = lcfirst(substr($name, 4));
            return $this->findRelated($relName, $args[0]);
        }

        throw new InvalidArgumentException("Invalid method '{$name}'");
    }
}