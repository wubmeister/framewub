<?php

/**
 * Helper to construct SELECT queries for SQL
 *
 * @package    framewub/storage
 * @author     Wubbo Bos <wubbo@wubbobos.nl>
 * @copyright  Copyright (c) Wubbo Bos
 * @license    http://mit-license.org/
 * @link       https://github.com/wubmeister/framewub
 */

namespace Framewub\Storage\Query;

/**
 * Literal function or expression wrapper
 */
class Update extends AbstractQuery
{
	/**
	 * The keys for the data to set
	 *
	 * @var array
	 */
	protected $dataKeys = [];

    /**
     * Sets the table for this query
     *
     * @param string|array $table
     *   Either a table name or an associative array with one element. In case of an array, the element's key should be the alias and the value the real table name.
     * @param string|array $columns
     *   OPTIONAL. The column(s) to select from the table. Defaults to '*'.
     *
     * @return Framewub\Storage\Query\Select
     *   Provides method chaining
     */
    public function table($table, $columns = '*')
    {
        $this->tableName = $this->tableStr($table, $columns);

        return $this;
    }

    /**
     * Adds data to set
     *
     * @param array $data
     *   An associative array of data
     *
     * @return Framewub\Storage\Query\Select
     *   Provides method chaining
     */
    public function data($data) {
    	foreach ($data as $key => $value) {
    		$this->bindParam(":{$key}", $value);
    		if (!in_array($key, $this->dataKeys)) {
    			$this->dataKeys[] = $key;
    		}
    	}
    	return $this;
    }

    /**
     * ToString function returns the UPDATE query
     *
     * @return string
     *   The literal expression
     */
    public function __toString()
    {
        $sql = "UPDATE {$this->tableName}";
        if (count($this->dataKeys)) {
        	$sql .= " SET " . implode(', ', array_map(function ($key) { return "`{$key}` = :{$key}"; }, $this->dataKeys));
        }
        if ($this->whereClause) {
            $sql .= " WHERE {$this->whereClause}";
        }

        return $sql;
    }
}