<?php

/**
 * Abstract base class for all template renderers
 *
 * @package    framewub/template
 * @author     Wubbo Bos <wubbo@wubbobos.nl>
 * @copyright  Copyright (c) Wubbo Bos
 * @license    GPL
 * @link       https://github.com/wubmeister/framewub
 */

namespace Framewub\Template;

abstract class AbstractTemplate
{
    /**
     * The URI for the template
     *
     * @var string
     */
    protected $uri = '';

    /**
     * The rendered content
     *
     * @var string
     */
    protected $content = '';

    /**
     * Variables for the templates
     *
     * @var array
     */
    protected $data = [];

    /**
     * Constructs a template with a given URI
     *
     * @param string $uri
     *   The URI of the tempalte file
     */
    public function __construct($uri)
    {
        $this->uri = $uri;
    }

    /**
     * Renders the template and stored the output in the $content property
     *
     * @param array|null $data
     *   OPTIONAL. Additional data for the template
     */
    abstract public function render($data = null);

    /**
     * Returns the rendered content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Sets a variable template
     *
     * @param string $name
     *   The variable name
     * @param mixed $value
     *   The value
     */
    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }
}
