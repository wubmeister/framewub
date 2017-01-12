<?php

/**
 * Abstract query class
 *
 * @package    framewub/db
 * @author     Wubbo Bos <wubbo@wubbobos.nl>
 * @copyright  Copyright (c) Wubbo Bos
 * @license    GPL
 * @link       https://github.com/wubmeister/framewub
 */

namespace Framewub\Db\Query;

use Framewub\Db\Generic as GenericDb;

/**
 * Literal function or expression wrapper
 */
class AbstractQuery
{
    /**
     * The table
     *
     * @var string
     */
    protected $tableName;

    /**
     * The 'where' clause
     *
     * @var string
     */
    protected $whereClause = '';

    /**
     * The current bind paramter index. Will be increased by one each time a
     * named bind paramter is added
     *
     * @var int
     */
    protected $bindIndex = 0;

    /**
     * The bind paramters
     *
     * @var array
     */
    protected $bind = [];

    /**
     * The keys for the values to set
     *
     * @var array
     */
    protected $valueKeys = [];

    /**
     * The database adapter, which is user for quoting identifiers and such
     *
     * @var Framewub\Db\Generic
     */
    protected $db;

    /**
     * Construct a Query object with a database adapter, which is user for
     * quoting identifiers and such
     *
     * @param Framewub\Db\Generic $db
     *   The database adapter
     */
    public function __construct(GenericDb $db)
    {
        $this->db = $db;
    }

    /**
     * Generates an qualified (aliased) table name and adds the columns to the
     * query if needed
     *
     * @param string|array $table
     *   Either a table name or an associative array with one element. In case
     *   of an array, the element's key should be the alias and the value the
     *   real table name.
     * @param string|array $columns
     *   OPTIONAL. The column(s) to select from the table. Defaults to '*'.
     *
     * @return static
     *   Provides method chaining
     */
    protected function tableStr($table, $columns = '*')
    {
        $aliases = [];

        if (is_array($table)) {
            $qualifiedNames = [];
            foreach ($table as $alias => $name) {
                $qualifiedNames[] = $this->db->quoteIdentifier($name) . (!is_numeric($alias) ? " AS " . $this->db->quoteIdentifier($alias) : "");
                $aliases[] = is_numeric($alias) ? $name : $alias;
            }
            $qualifiedName = implode(', ', $qualifiedNames);
        } else {
            $qualifiedName = $this->db->quoteIdentifier($table);
            $aliases[] = $table;
        }

        if ($columns) {
            if (!is_array($columns)) {
                $columns = [ $columns ];
            }

            foreach ($aliases as $alias) {
                $this->columns[$alias] = [];
            }

            $this->addColumns($columns, $aliases);
        }

        return $qualifiedName;
    }

    /**
     * Adds one or more columns to the query
     *
     * @param string|array $columns
     *   The column(s) to select from the table
     * @param string $table
     *   OPTIONAL. The table the columns belong to. Specify '*' if the column's
     *   don't belong to any table. This is also the default value.
     */
    protected function addColumns($columns, $tables = '*')
    {
        if (!is_array($columns)) {
            $columns = [ $columns ];
        }

        $table = is_array($tables) ? $tables[0] : $tables;

        foreach ($columns as $key => $column) {
            if ($column == '*') {
                if (is_array($tables)) {
                    foreach ($tables as $t) {
                        $this->columns[$t][] = ($t != '*' ? $this->db->quoteIdentifier($t) . '.' : '') . "*";
                    }
                } else {
                    $this->columns[$table][] = ($table != '*' ? $this->db->quoteIdentifier($table) . '.' : '') . "*";
                }
            } else if ($column instanceof Func) {
                $this->columns[$table][] = (string)$column . (!is_numeric($key) ? " AS " . $this->db->quoteIdentifier($key) : "");
            } else {
                $t = $table;
                if (strpos($column, '.') === FALSE && $table != '*') {
                    $column = "{$table}.{$column}";
                } else if (strpos($column, '.')) {
                    list($t, $c) = explode('.', $column, 2);
                }
                $this->columns[$t][] = $this->db->quoteIdentifier($column) . (!is_numeric($key) ? " AS " . $this->db->quoteIdentifier($key) : "");
            }
        }
    }

    /**
     * Generates a bind parameter name and binds the specified value to it
     *
     * @param mixed $value
     *   The value to bind
     *
     * @return string
     *   The generated parameter name (beginning with a colon)
     */
    protected function bindValue($value) {
        if (($value instanceof Func) || $this->db->isIdentifier((string)$value)) {
            return (string)$value;
        }

        $this->bindIndex++;
        $key = ":bind{$this->bindIndex}";
        $this->bind[$key] = $value;

        return $key;
    }

    /**
     * Binds a value to a specified parameter. Overwrites a previously bound
     * value to that parameter if any
     *
     * @param string $param
     *   The parameter name
     * @param mixed $value
     *   The value to bind
     */
    protected function bindParam(string $param, $value) {
        $this->bind[$param] = $value;
    }

    /**
     * Returns the bind parameters
     *
     * @return array
     *   The bind parameters ([ ':bindX' => $value ])
     */
    public function getBind()
    {
        return $this->bind;
    }

    /**
     * Converts conditions into a part of a 'where' clause
     *
     * @param array $conditions
     *   The conditions
     * @param string $glue
     *   OPTIONAL. Either 'AND' or 'OR'. Defaults to 'OR'
     *
     * @return string
     *   The part of the 'where' clause
     */
    protected function whereStr(&$conditions, string $glue = 'AND')
    {
        $str = '';
        foreach ($conditions as $key => $value) {
            if ($key == '$or' || $key == '$and') {
                if ($str) {
                    $str .= $key == '$or' ? ' OR ' : ' AND ';
                }
                $str .= "(" . $this->whereStr($value) . ")";
            } else {
                if ($str) {
                    $str .= " {$glue} ";
                }

                $operator = $value === null ? 'IS' : '=';
                $operand = null;

                if (is_array($value) && !array_key_exists(0, $value)) {
                    $k = array_keys($value)[0];
                    $bindValue = $value[$k];

                    switch ($k) {
                        case '$gt':
                            $operator = '>';
                            break;
                        case '$gte':
                            $operator = '>=';
                            break;
                        case '$lt':
                            $operator = '<';
                            break;
                        case '$lte':
                            $operator = '<=';
                            break;
                        case '$ne':
                            $operator = $bindValue === null ? 'IS NOT' : '<>';
                            break;
                        case '$between':
                            $operator = 'BETWEEN';
                            $operand = $this->bindValue($bindValue[0]) . ' AND ' . $this->bindValue($bindValue[1]);
                            break;
                    }
                } else {
                    $bindValue = $value;
                }

                if (!$operand) {
                    $operand = $bindValue === null ? 'NULL' : $this->bindValue($bindValue);
                }

                $str .= "(" . $this->db->quoteIdentifier($key) . " {$operator} {$operand})";
            }
        }

        return $str;
    }

    /**
     * Adds one or more conditions to the 'where' clause
     *
     * @param array $conditions
     *   The conditions to add
     *
     * @return static
     *   Provides method chaining
     */
    public function where(array $conditions)
    {
        if ($this->whereClause) {
            $this->whereClause .= " AND ";
        }
        $this->whereClause .= $this->whereStr($conditions);

        return $this;
    }

    /**
     * Adds one or more conditions to the 'where' clause, appended with 'OR'
     *
     * @param array $conditions
     *   The conditions to add
     *
     * @return static
     *   Provides method chaining
     */
    public function orWhere(array $conditions)
    {
        if ($this->whereClause) {
            $this->whereClause .= " OR ";
        }
        $this->whereClause .= $this->whereStr($conditions, 'OR');

        return $this;
    }

    /**
     * Adds values to set
     *
     * @param array $values
     *   An associative array of values
     *
     * @return static
     *   Provides method chaining
     */
    public function values(array $values) {
        foreach ($values as $key => $value) {
            $this->bindParam(":{$key}", $value);
            if (!in_array($key, $this->valueKeys)) {
                $this->valueKeys[] = $key;
            }
        }
        return $this;
    }
}