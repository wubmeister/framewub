<?php

/**
 * Class for PHTML transformations
 *
 * @package    framewub/block-builder
 * @author     Wubbo Bos <wubbo@wubbobos.nl>
 * @copyright  Copyright (c) Wubbo Bos
 * @license    GPL
 * @link       https://github.com/wubmeister/framewub
 */

namespace Framewub\BlockBuilder\Transform;

class Phtml extends AbstractTransform
{
    /**
     * Transforms scheme data to PHTML
     *
     * @param array $data
     *   The data
     */
    public function transform(array $data)
    {
        extract($data);

        ob_start();
        include $this->filename;
        $phtml = ob_get_contents();
        ob_end_clean();

        return $phtml;
    }
}
