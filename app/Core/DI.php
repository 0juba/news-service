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
     * @param array $list
     */
    public static function init(array $list = [])
    {
        static::$_di = \ServiceBuilder\ServiceBuilder::load($list);
    }

}