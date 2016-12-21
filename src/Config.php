<?php

/**
 * Class to contain configuration variables
 *
 * @package    framewub
 * @author     Wubbo Bos <wubbo@wubbobos.nl>
 * @copyright  Copyright (c) Wubbo Bos
 * @license    GPL
 * @link       https://github.com/wubmeister/framewub
 */

namespace Framewub;

class Config
{
	/**
	 * Constructor
	 *
	 * @param array $data
	 *   The data
	 */
	public function __construct(array $data = [])
	{
		foreach (array_keys($data) as $key) {
			if (is_array($data[$key]) && !array_key_exists(0, $data[$key])) {
				$this->{$key} = new static($data[$key]);
			} else {
				$this->{$key} = $data[$key];
			}
		}
	}

	/**
	 * Getter: undefined keys/properties should return null
	 *
	 * @param string $name
	 *   The propety name
	 *
	 * @return null
	 */
	public function __get($name)
	{
		return null;
	}

	/**
	 * Injects another configuration into this configuration. This configuration
	 * will be the merged version of both
	 *
	 * @param Config $other
	 *   The other configuration
	 */
	public function mergeWith(Config $other)
	{
		foreach ($other as $key => $value) {
			if (is_object($this->{$key}) && is_object($value)) {
				$this->{$key}->mergeWith($value);
			} else {
				$this->{$key} = $value;
			}
		}
	}
}
