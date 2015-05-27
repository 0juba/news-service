<?php

require_once __DIR__ . '/../app/config.dirs.php';

/** CONFIGURE */
define('ROOT',__DIR__.'/../');


require_once ROOT . 'vendor/autoload.php';


use NewsService\Core\DI;

/** INIT */
DI::init(['redis','doctrine']);
$di = DI::getDi();

$repo = new \NewsService\Repo\News();

$repo->createTable();



$tokens = [
    '0c1eff79bed398dbff29d65febc0cbbf',
    '1c1a52703fadb064b3744158be7e5557',
    'c6f9cab6a13278ef9e2279110f650b68',
    '4a79e442f9645bf45c3be48a27939ab1',
    '90bf4fe4316e4e091c72f735c7d0ee30',
    'b99ddfd048997ce25db2d7b26e6398cb',
    '92da5a98e588a19f02bf871754c39423',
    '8815dcb4f43d4135ab9b0566ed35b1bd',
    '98223d9cc2ed58d6e9667bd1714236f8',
    '109f2f734f397f33a4bac4174aa91e31',
    'dadeb9610fca814afb5bf98c1dac0d79',
    '09d01c69c3a1b636f20cd5e8360e3556',
    'bdfd668044ac6a7b02334bdc6f9b50a1',
    'de892cc6041109c75e975db9840d579f',
    '6ef82ad9a1de52ede98e556aa3997373',
    'ee9c846b7defe33cb0164e9661221168',
    'bfebe25ea1fee4ec5d0b5ad5d4a2c2c7',
    '79a3c90cfa8799aab8888e05cd63b560',
    '4119d1ccaedc3e411712781b920b9761',
    'a7d3c395e4fc1c2885126d911ffb75f1'
];


$redis = $di->redis;


foreach ( $tokens as $t )
    $redis->sadd(REDIS_KEY);