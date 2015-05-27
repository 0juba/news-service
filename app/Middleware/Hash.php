<?php namespace NewsService\Middleware;

use NewsService\Core\DI;
use NewsService\Core\Json;

class Hash
{
    public static function getMw()
    {
        return function(\Slim\Route $route)
        {
            $app   = \Slim\Slim::getInstance();

            $app->response->header('Content-type','application/json');

            $hash  = $route->getParam('hash');
            $redis = DI::getDi()->redis;
            $key   = REDIS_KEY;

            if ( !$redis->sismember($key, $hash ) )
                $app->halt(401,Json::error('Authentication required'));

        };
    }
}