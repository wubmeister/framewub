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
	 *   An unquoted identifier. Passing an already quoted identifier may lead to unexpected results!
	 *
	 * @return string
	 *   The quoted identifier
	 */
	public function quoteIdentifier($identifier)
	{
		return '`' . str_replace('.', '`.`', $identifier) . '`';
	}
}