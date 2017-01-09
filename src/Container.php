<?php

/**
 * Service container
 *
 * @package    framewub
 * @author     Wubbo Bos <wubbo@wubbobos.nl>
 * @copyright  Copyright (c) Wubbo Bos
 * @license    GPL
 * @link       https://github.com/wubmeister/framewub
 */

namespace Framewub;

class Container
{
    /**
     * The configuration
     *
     * @var Framewub\Config
     */
    protected $config;

    /**
     * The created service instances
     *
     * @var array
     */
    protected $services = [];

    /**
     * Initializes a container with the specified configuration
     *
     * @param Framewub\Config $config
     *   The configuration
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Checks whether a service is registered
     *
     * @param string $name
     *   The name of the service
     *
     * @return bool
     *   true if the service is registered, false if not
     */
    public function has(string $name)
    {
        return isset($this->config->factories->{$name});
    }

    /**
     * Returns an instance of a service. If the instance doesn't exist yet, it
     * will be created on the fly.
     *
     * @param string $name
     *   The name of the service
     *
     * @return bool
     *   true if the service is registered, false if not
     */
    public function get(string $name)
    {
        if (!isset($this->services[$name])) {
            if (isset($this->config->factories) && isset($this->config->factories->{$name})) {
                $func = $this->config->factories->{$name};
                if (is_string($func)) {
                    $func = new $func();
                }
                $this->services[$name] = $func($this, $name);

            } else if (isset($this->config->invokables) && isset($this->config->invokables->{$name})) {
                $className = $this->config->invokables->{$name};
                $this->services[$name] = new $className();

            } else {
                throw new \Exception("Service '{$name}' could not be created");
            }
        }

        return $this->services[$name];
    }

    /**
     * Registers a service instance under a given name. Any existing service
     * with the same name will be overwritten.
     *
     * @param string $name
     *   The name of the service
     * @param mixed $service
     *   The service
     */
    public function set(string $name, $service)
    {
        $this->services[$name] = $service;
    }
}
