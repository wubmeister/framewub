<?php

/**
 * A class with some utility functions
 *
 * @package    framewub
 * @author     Wubbo Bos <wubbo@wubbobos.nl>
 * @copyright  Copyright (c) Wubbo Bos
 * @license    GPL
 * @link       https://github.com/wubmeister/framewub
 */

namespace Framewub;

/**
 * Utility functions
 */
class Util
{
	/**
	 * Transforms a plural into a singular
	 *
	 * @param string $plural
	 *   The plural
	 *
	 * @return string
	 *   The singular
	 */
    public static function getSingular($plural)
    {
        $singular = $plural;

        if (substr($plural, -1) == 'a') {
            $singular = substr($plural, 0, strlen($plural) - 1) . 'um';
        } else if (substr($plural, -2) == 'es') {
            $chr = substr($plural, -3, 1);
            if ($chr == 'h') {
                $singular = substr($plural, 0, strlen($plural) - 2);
            } else {
                $singular = substr($plural, 0, strlen($plural) - 1);
            }
        } else if (substr($plural, -1) == 's') {
            $singular = substr($plural, 0, strlen($plural) - 1);
        }

        return $singular;
    }

}