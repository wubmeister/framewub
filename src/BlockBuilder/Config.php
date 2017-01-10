<?php

/**
 * Configuration container for the BlockBuilder package
 *
 * @package    framewub/block-builder
 * @author     Wubbo Bos <wubbo@wubbobos.nl>
 * @copyright  Copyright (c) Wubbo Bos
 * @license    GPL
 * @link       https://github.com/wubmeister/framewub
 */

namespace Framewub\BlockBuilder;

class Config
{
	/**
	 * The directory which contains all the global templates
	 *
	 * @var string
	 */
	public static $globalDir;

	/**
	 * The directory which contains all the theme templates
	 *
	 * @var string
	 */
	public static $themeDir;

	/**
	 * The directory which contains all the specific template overrides
	 *
	 * @var string
	 */
	public static $specificsDir;

	/**
	 * Sets all the three directories
	 *
	 * @param string $global
	 *   The directory which contains all the global templates
	 * @param string $theme
	 *   The directory which contains all the theme templates
	 * @param string $specifics
	 *   The directory which contains all the specific template overrides
	 */
	public static function setDirs(string $global, string $theme, string $specifics)
	{
		self::$globalDir = $global;
		self::$themeDir = $theme;
		self::$specificsDir = $specifics;
	}
}
