<?php

/**
 * Class to represent a database search result
 *
 * @package    framewub/storage
 * @author     Wubbo Bos <wubbo@wubbobos.nl>
 * @copyright  Copyright (c) Wubbo Bos
 * @license    GPL
 * @link       https://github.com/wubmeister/framewub
 */

namespace Framewub\Storage\Db;

use PDO;
use PDOStatement;
use Iterator;
use Framewub\Storage\StorageObject;
use Framewub\Db\Query\Func;

/**
 * Abstract database storage class
 */
class Rowset implements Iterator
{
	/**
	 * The select query
	 *
	 * @var Framewub\Storage\Query\Select
	 */
	protected $select;

	/**
	 * The object class
	 *
	 * @var string
	 */
	protected $objectClass;

	/**
	 * The PDO adapter
	 *
	 * @var PDO
	 */
	protected $pdo;

	/**
	 * The storage
	 *
	 * @var Framewub\Storage\Db\AbstractStorage
	 */
	protected $storage;

	/**
	 * The executed PDO statement
	 *
	 * @var PDOStatement
	 */
	protected $statement;

	/**
	 * The number of rows in this row set
	 *
	 * @var int
	 */
	protected $numRows = -1;

	/**
	 * The fetched objects so far
	 *
	 * @var array
	 */
	protected $fetched = [];

	/**
	 * The current iterator position
	 *
	 * @var int
	 */
	protected $currPos = 0;

	public function __construct($select, $storage)
	{
		$this->select = $select;
		$this->storage = $storage;
	}

	/**
	 * Sets the class name for the fetched objects
	 *
	 * @param string $class
	 *   The class name
	 */
	public function setObjectClass($class)
	{
		$this->objectClass = $class;
	}

	/**
	 * Prepares and executes the statement
	 */
	protected function prepare()
	{
		if (!$this->objectClass) {
			$this->objectClass = StorageObject::class;
		}

		$this->statement = $this->storage->getPdo()->prepare((string)$this->select);
		$params = $this->select->getBind();
		foreach ($params as $key => $value) {
			$this->statement->bindValue($key, $value);
		}

		$this->statement->execute();
		$this->statement->setFetchMode(PDO::FETCH_CLASS, $this->objectClass, [ $this->storage ]);
	}

	/**
	 * Fetches the object at the cursor and advances the cursor to the next result
	 *
	 * @return Framewub\Storage\StorageObject
	 *   The object at the cursor or null if there are no other objects to fetch
	 */
	public function fetchOne()
	{
		if (!$this->statement) {
			$this->prepare();
		}

		return $this->statement->fetch();
	}

	/**
	 * Fetches the object at the cursor and advances the cursor to the next result
	 *
	 * @return Framewub\Storage\StorageObject
	 *   The object at the cursor or null if there are no other objects to fetch
	 */
	public function fetchAll()
	{
		if (!$this->statement) {
			$this->prepare();
		}

		return $this->statement->fetchAll();
	}

	/**
	 * Returns the numer of items in the row set
	 *
	 * @return int
	 *   The number of rows
	 */
	public function count()
	{
		if ($this->numRows == -1) {
			$select = clone $this->select;
			$select->columns([ 'count' => new Func('COUNT(*)') ], true);

			$statement = $this->storage->getPdo()->prepare((string)$select);
			$params = $select->getBind();
			foreach ($params as $key => $value) {
				$statement->bindValue($key, $value);
			}
			$statement->execute();
			$row = $statement->fetch(PDO::FETCH_ASSOC);
			$this->numRows = $row ? $row['count'] : 0;
		}
		return $this->numRows;
	}

	/**
	 * Converts the entire rowset into an array
	 *
	 * @return array
	 *   The array
	 */
	public function toArray()
	{
		$result = [];
		foreach ($this as $row) {
			$result[] = $row->toArray();
		}

		return $result;
	}

	/**
	 * The current item
	 *
	 * @return
	 *   The item at the cursor
	 */
	public function current()
	{
		if ($this->currPos >= count($this->fetched)) {
			$this->fetched[$this->currPos] = $this->fetchOne();
		}
		return $this->fetched[$this->currPos];
	}

	/**
	 * The key of current item
	 *
	 * @return
	 *   The key at the cursor
	 */
	public function key()
	{
		return $this->currPos;
	}

	/**
	 * Advance to the next item
	 */
	public function next()
	{
		$this->currObject = null;
		$this->currPos++;
	}

	/**
	 * Rewind to the beginning
	 */
	public function rewind()
	{
		$this->currPos = 0;
	}

	/**
	 * Check to see if the current position is valid
	 *
	 * @return bool
	 */
	public function valid()
	{
		if ($this->currPos >= count($this->fetched)) {
			$this->fetched[$this->currPos] = $this->fetchOne();
		}
		return $this->fetched[$this->currPos] ? true : false;
	}
}