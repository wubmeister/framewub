<?php

/**
 * Class to render PHTML templates
 *
 * @package    framewub/template
 * @author     Wubbo Bos <wubbo@wubbobos.nl>
 * @copyright  Copyright (c) Wubbo Bos
 * @license    GPL
 * @link       https://github.com/wubmeister/framewub
 */

namespace Framewub\Template;

class Phtml extends AbstractTemplate
{
    /**
     * Renders the template and stored the output in the $content property
     *
     * @param array|null $data
     *   OPTIONAL. Additional data for the template
     */
    public function render($data = null)
    {
        $data = $data ? array_merge($this->data, $data) : $this->data;
        extract($data);
        ob_start();
        include $this->uri;
        $this->content = ob_get_contents();
        ob_end_clean();
    }
}
