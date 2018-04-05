<?php

use Illuminate\Routing\Router;

/**
 * Class Route
 */
class Route
{
    /**
     * @var Router
     */
    private static $router;

    /**
     * @param Router $router
     */
    public static function setInstance(Router $router)
    {
        self::$router = $router;
    }

    /**
     * @return Router
     */
    public static function instance()
    {
        if (null === self::$router) {
            self::$router = app('Illuminate\Routing\Router');
        }

        return self::$router;
    }

    /**
     * @param string $method
     * @param array $args
     *
     * @return mixed
     */
    public static function __callStatic($method, $args)
    {
        return call_user_func_array([self::instance(), $method], $args);
    }
}
