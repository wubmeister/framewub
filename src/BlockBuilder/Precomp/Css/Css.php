<?php

/**
 * This precompiler just returns the combined contents of the CSS files
 *
 * @package    framewub/block-builder
 * @author     Wubbo Bos <wubbo@wubbobos.nl>
 * @copyright  Copyright (c) Wubbo Bos
 * @license    GPL
 * @link       https://github.com/wubmeister/framewub
 */

namespace Framewub\BlockBuilder\Precomp\Css;

use Framewub\BlockBuilder\Precomp\AbstractPrecomp;

class Css extends AbstractPrecomp
{
	/**
	 * Returns the combined contents of all the CSS files
	 *
	 * @return string
	 */
	public function getCompiled()
	{
		$css = '';
		foreach ($this->files as $file) {
			$css .= file_get_contents($file) . "\n";
		}

		return $css;
	}
}
