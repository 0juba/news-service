<?php namespace NewsService\Core;

/**
 * Class Json
 * @package NewsService\Core
 */
class Json
{
    /**
     * @param $data
     * @return string
     */
    public static function ok ($data)
    {
        return json_encode([
            'status' => 'ok',
            'data'   => $data
        ]);
    }

    /**
     * @param $data
     * @return string
     */
    public static function error ($data)
    {
        return json_encode([
            'status' => 'error',
            'data'   => $data
        ]);
    }

}