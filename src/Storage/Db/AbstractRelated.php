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
use Framewub\Services;
use Framewub\Db\Query\Select;
use Framewub\Db\Query\Update;
use Framewub\Db\Query\Insert;
use Framewub\Db\Query\Delete;

class AbstractRelated extends AbstractStorage
{
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

            switch ($type) {
                case self::MANY_TO_ONE:
                    $select->where([ $fkToOther => $otherId ]);
                    break;

                case self::ONE_TO_MANY:
                    $storageObj = Services::get($storage, $this->db);
                    $fk = $storageObj->tableName.'.id';
                    $select
                        ->from($storageObj->tableName, [])
                        ->joinLeft($this->tableName, $this->tableName.'.id = '.$storageObj->tableName.'.'.$fkToSelf)
                        ->where([ $fk => $otherId ]);
                    break;

                case self::MANY_TO_MANY:
                    $fk = $linkTable.'.'.$fkToOther;
                    $select
                        ->from($linkTable, [])
                        ->joinLeft($this->tableName, $this->tableName.'.id = '.$linkTable.'.'.$fkToSelf)
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

            $storageObj = Services::get($storage, $this->db);
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

                case self::MANY_TO_MANY:
                    $fk = $linkTable.'.'.$fkToSelf;
                    $select
                        ->from($linkTable, [])
                        ->joinLeft($storageObj->tableName, $storageObj->tableName.'.id = '.$linkTable.'.'.$fkToOther)
                        ->where([ $fk => $id ]);
                    break;

                default:
                    throw new InvalidArgumentException("Relationship type not implemented");

            }

            return $storageObj->findBySelect($select);
        }

        return null;
    }

    /**
     * Adds a related record to a record from this storage by creating or
     * updating a relation
     *
     * @param string $relation
     *   The key of the relation, as it is defined in the $relations property.
     * @param mixed $id
     *   The ID of the object in this storage
     * @param mixed $otherId
     *   The ID of the object in the other storage
     * @param array $extraData
     *   Extra data to insert into the link table for many-to-many relationships
     *
     * @return Framewub\Storage\Db\Rowset|null
     *   The rowset with the results of null if nothing was found
     */
    protected function addRelated($relation, $id, $otherId, $extraData = [])
    {
        if (isset($this->relations[$relation])) {
            extract($this->relations[$relation]);

            switch ($type) {
                case self::ONE_TO_MANY:
                    $storageObj = Services::get($storage, $this->db);
                    $query = new Update($this->db);
                    $query
                        ->table($storageObj->tableName)
                        ->values([ $fkToSelf => $id ])
                        ->where([ 'id' => $otherId ]);
                    break;

                case self::MANY_TO_ONE:
                    $query = new Update($this->db);
                    $query
                        ->table($this->tableName)
                        ->values([ $fkToOther => $otherId ])
                        ->where([ 'id' => $id ]);
                    break;

                case self::MANY_TO_MANY:
                    $extraData[$fkToSelf] = $id;
                    $extraData[$fkToOther] = $otherId;
                    $query = new Insert($this->db);
                    $query->ignore()->into($linkTable)->values($extraData);
                    break;

                default:
                    throw new InvalidArgumentException("Relationship type not implemented");
            }

            $this->db->execute($query, $query->getBind());
        }
    }

    /**
     * Unlinks a related record to a record from this storage by deleting or
     * updating a relation
     *
     * @param string $relation
     *   The key of the relation, as it is defined in the $relations property.
     * @param mixed $id
     *   The ID of the object in this storage
     * @param mixed $otherId
     *   The ID of the object in the other storage
     *
     * @return Framewub\Storage\Db\Rowset|null
     *   The rowset with the results of null if nothing was found
     */
    protected function unlinkRelated($relation, $id, $otherId, $extraData = [])
    {
        if (isset($this->relations[$relation])) {
            extract($this->relations[$relation]);

            switch ($type) {
                case self::ONE_TO_MANY:
                    $storageObj = Services::get($storage, $this->db);
                    $query = new Update($this->db);
                    $query
                        ->table($storageObj->tableName)
                        ->values([ $fkToSelf => null ])
                        ->where([ 'id' => $otherId, $fkToSelf => $id ]);
                    break;

                case self::MANY_TO_ONE:
                    $query = new Update($this->db);
                    $query
                        ->table($this->tableName)
                        ->values([ $fkToOther => null ])
                        ->where([ 'id' => $id, $fkToOther => $otherId ]);
                    break;

                case self::MANY_TO_MANY:
                    $query = new Delete($this->db);
                    $query
                        ->from($linkTable)
                        ->where([ $fkToSelf => $id, $fkToOther => $otherId ]);
                    break;

                default:
                    throw new InvalidArgumentException("Relationship type not implemented");
            }

            $this->db->execute($query, $query->getBind());
        }
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
        } else if (substr($name, 0, 3) == 'add' && strlen($name) > 3) {
            $relName = Util::getPlural(lcfirst(substr($name, 3)));
            return $this->addRelated($relName, $args[0], $args[1], count($args) > 2 ? $args[2] : []);
        } else if (substr($name, 0, 6) == 'unlink' && strlen($name) > 6) {
            $relName = Util::getPlural(lcfirst(substr($name, 6)));
            return $this->unlinkRelated($relName, $args[0], $args[1]);
        }

        throw new InvalidArgumentException("Invalid method '{$name}'");
    }
}
