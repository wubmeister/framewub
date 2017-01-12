<?php

/**
 * Generic PDO wrapper, also the base class and interface for any PDO wrapper
 *
 * @package    framewub/db
 * @author     Wubbo Bos <wubbo@wubbobos.nl>
 * @copyright  Copyright (c) Wubbo Bos
 * @license    GPL
 * @link       https://github.com/wubmeister/framewub
 */

namespace Framewub\Db;

use PDO;
use Exception;

/**
 * Generic PDO wrapper
 */
class Generic
{
	/**
	 * The PDO object
	 *
	 * @var PDO
	 */
	protected $pdo;

	/**
	 * The DSN prefix
	 *
	 * @var string
	 */
	protected $dsnPrefix = 'generic';

	/**
	 * Constructor, takes DSN params, a user name and password
	 *
	 * @param array $dsnParams
	 *   The parameters for the DSN, in most cases a host and database name.
	 *   E.G. [ 'host' => 'localhost', 'dbname' => 'my_database' ]
	 * @param string $username
	 *   OPTIONAL. The user name to authenticate with the database server
	 * @param string $password
	 *   OPTIONAL. The password to authenticate with the database server
	 */
	public function __construct(array $dsnParams, $username = null, $password = null)
	{
		if ($this->dsnPrefix != 'generic') {
			$dsn = $this->dsnPrefix . ':';
			$dsnPairs = [];
			foreach ($dsnParams as $key => $value) {
				$dsnPairs[] = "{$key}={$value}";
			}
			$dsn .= implode(';', $dsnPairs);
			$this->pdo = new PDO($dsn, $username, $password);
		}
	}

	/**
	 * Gets the internal PDO object
	 *
	 * @return
	 *   The PDO object
	 */
	public function getPdo()
	{
		return $this->pdo;
	}

	/**
	 * Quotes an identifier for safe use in SQL queries
	 *
	 * @param string $identifier
	 *   An unquoted identifier. Passing an already quoted identifier may lead
	 *   to unexpected results!
	 *
	 * @return string
	 *   The quoted identifier
	 */
	public function quoteIdentifier(string $identifier)
	{
		return '"' . str_replace('.', '"."', $identifier) . '"';
	}

	/**
	 * Checks if a string is likely to be an identifier
	 *
	 * @param string $string
	 *   The string to check
	 *
	 * @return bool
	 *   Returns true if the string is most likely an identifier, false if definitely not
	 */
	public function isIdentifier(string $string)
	{
		return $string[0] == '"' && substr($string, -1) == '"';
	}

	/**
	 * Prepares a PDO statement and binds value parameters if any are specified
	 *
	 * @param string $query
	 *   The SQL query
	 * @param array $bind
	 *   OPTIONAL. THe bind parameters
	 *
	 * @return PDOStatement
	 *   The prepared PDOStatement
	 */
	public function prepare($query, array $bind = [])
	{
		$statement = $this->pdo->prepare((string)$query);

		if ($statement) {
			foreach ($bind as $key => $value) {
				$statement->bindValue($key, $value);
			}
		}

		return $statement;
	}

	/**
	 * Prepares and executes a PDO statement and binds value parameters if any
	 * are specified
	 *
	 * @param string $query
	 *   The SQL query
	 * @param array $bind
	 *   OPTIONAL. THe bind parameters
	 *
	 * @return PDOStatement
	 *   The prepared PDOStatement or false if the preparing or execution failed
	 *
	 * @throws Exception If the execution fails
	 */
	public function execute($query, array $bind = [])
	{
		$statement = $this->prepare($query, $bind);
		if ($statement && $statement->execute()) {
			return $statement;
		} else {
			$errorInfo = $this->pdo->errorInfo();
			throw new Exception("[SQLSTATE {$errorInfo[0]}] {$errorInfo[1]}");
		}
	}
}