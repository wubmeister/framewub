<?php

/**
 * Factory for global service semi-singletons
 *
 * @package    framewub
 * @author     Wubbo Bos <wubbo@wubbobos.nl>
 * @copyright  Copyright (c) Wubbo Bos
 * @license    GPL
 * @link       https://github.com/wubmeister/framewub
 */

namespace Framewub;

class Services
{
    /**
     * The created services
     *
     * @var array
     */
    protected static $services = [];

    /**
     * The registered factories
     *
     * @var array
     */
    protected static $factories = [];

    /**
     * Returns a service with the given class name
     *
     * @param string $className
     *   The class name
     */
    public static function get($className)
    {
        if (!isset(self::$services[$className])) {
            if (isset(self::$factories[$className])) {
                self::$services[$className] = self::$factories[$className]();
            } else {
                self::$services[$className] = new $className();
            }
        }
        return self::$services[$className];
    }

    /**
     * Registers a service factory under a given name. The factory must be a
     * function which returns an instance of the service. The factory doesn't
     * need to check for the existance of an instance to create one, as it
     * will only be called once by the service factory.
     *
     * @param string $name
     *   The name to register the factory under
     * @param Closure $factory
     *   The factory function
     */
    public static function register($name, $factory)
    {
        self::$factories[$name] = $factory;
    }
}
