<?php

/**
 * Helper to construct INSERT queries for SQL
 *
 * @package    framewub/db
 * @author     Wubbo Bos <wubbo@wubbobos.nl>
 * @copyright  Copyright (c) Wubbo Bos
 * @license    GPL
 * @link       https://github.com/wubmeister/framewub
 */

namespace Framewub\Db\Query;

/**
 * Insert query builder
 */
class Insert extends AbstractQuery
{
    const EXCEPTION = 0;
    const IGNORE = 1;
    const UPDATE = 2;

    /**
     * The 'ignore' flag
     *
     * @var bool
     */
    protected $ignoreFlag = false;

    /**
     * The 'on duplicate key update' flag
     *
     * @var bool
     */
    protected $onDupKeyUpdateFlag = false;

    /**
     * Columns to update 'on duplicate key'. If this is null, all provided columns will be updated
     *
     * @var array
     */
    protected $dupUpdateColumns = null;

    /**
     * Sets the table for this query
     *
     * @param string|array $table
     *   Either a table name or an associative array with one element. In case of an array, the element's key should be the alias and the value the real table name.
     * @param string|array $columns
     *   OPTIONAL. The column(s) to select from the table. Defaults to '*'.
     *
     * @return static
     *   Provides method chaining
     */
    public function into($table)
    {
        $this->tableName = $this->tableStr($table, []);

        return $this;
    }

    /**
     * Sets the 'ignore' flag for the insert
     *
     * @param bool $ignore
     *   OPTIONAL. The ignore flag. Defaults to true. Provide false to cancel ignoring.
     *
     * @return static
     *   Provides method chaining
     */
    public function ignore($ignore = true)
    {
        $this->ignoreFlag = (bool)$ignore;

        return $this;
    }

    /**
     * Determines what to do in case of a duplicate key
     *
     * @param int $action
     *   One of the class constants: EXCEPTION, IGNORE, UPDATE. Default behaviour is to throw an exception
     *
     * @return static
     *   Provides method chaining
     */
    public function onDuplicateKey($action, $columns = null)
    {
        if ($action == self::EXCEPTION || $action == self::UPDATE) {
            $this->ignoreFlag = false;
        }
        if ($action == self::EXCEPTION || $action == self::IGNORE) {
            $this->onDuplicateKey = false;
            $this->dupUpdateColumns = null;
        }
        if ($action == self::IGNORE) {
            $this->ignoreFlag = true;
        }
        else if ($action == self::UPDATE) {
            $this->onDupKeyUpdateFlag = true;
            $this->dupUpdateColumns = $columns ? (is_array($columns) ? $columns : [ $columns ]) : null;
        }
        return $this;
    }

    /**
     * ToString function returns the INSERT query
     *
     * @return string
     *   The literal expression
     */
    public function __toString()
    {
        $sql = "INSERT " . ($this->ignoreFlag ? "IGNORE " : "") . "INTO {$this->tableName}";
        if (count($this->valueKeys)) {
        	$sql .= " (`" . implode("`, `", $this->valueKeys) . "`) VALUES (:" . implode(", :", $this->valueKeys) . ")";
        }
        if ($this->onDupKeyUpdateFlag) {
            $dupUpdateColumns = $this->dupUpdateColumns ? $this->dupUpdateColumns : $this->valueKeys;
            $sql .= " ON DUPLICATE KEY UPDATE " . implode(', ', array_map(function ($key) { return "`{$key}` = :{$key}"; }, $dupUpdateColumns));
        }

        return $sql;
    }
}