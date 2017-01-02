<?php

/**
 * Compiles Javascript by combining all added files together
 *
 * @package    framewub/block-builder
 * @author     Wubbo Bos <wubbo@wubbobos.nl>
 * @copyright  Copyright (c) Wubbo Bos
 * @license    GPL
 * @link       https://github.com/wubmeister/framewub
 */

namespace Framewub\BlockBuilder\Precomp\Js;

use Framewub\BlockBuilder\Precomp\AbstractPrecomp;

class Js extends AbstractPrecomp
{
    /**
     * Returns the combined contents of all the CSS files
     *
     * @return string
     */
    public function getCompiled()
    {
        $js = '';
        foreach ($this->files as $file) {
            $src = trim(file_get_contents($file));
            if (!empty($src)) {
                $js .= "(function(){\n{$src}\n})();\n";
            }
        }

        return $js;
    }
}
