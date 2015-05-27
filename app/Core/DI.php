<?php namespace NewsService\Core;


/**
 * Class DI (container)
 * @package NewsService\Core
 */
class DI
{
    /**
     * @var \ServiceBuilder\ServiceBuilder
     */
    private static $_di;

    /**
     *
     */
    private function __construct()
    {
    }

    /**
     * @return mixed
     */
    public static function getDi()
    {
        return static::$_di;
    }

    /**
     * Init container
     */
    public static function init()
    {
        static::$_di = \ServiceBuilder\ServiceBuilder::load(['redis','twig','doctrine']);
    }

}