<?php

/**
 * MySQL PDO wrapper
 *
 * @package    framewub/db
 * @author     Wubbo Bos <wubbo@wubbobos.nl>
 * @copyright  Copyright (c) Wubbo Bos
 * @license    GPL
 * @link       https://github.com/wubmeister/framewub
 */

namespace Framewub\Db;

/**
 * MySQL PDO wrapper
 */
class MySQL extends Generic
{

	/**
	 * The DSN prefix
	 *
	 * @var string
	 */
	protected $dsnPrefix = 'mysql';

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
		$identifier = '`' . str_replace('.', '`.`', $identifier) . '`';
		return str_replace('`*`', '*', $identifier);
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
		return $string[0] == '`' && substr($string, -1) == '`';
	}
}