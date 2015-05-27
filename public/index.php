<?php

/** CONFIGURE */
require_once __DIR__ . '/../app/config.dirs.php';

require_once ROOT . 'vendor/autoload.php';

$config = require_once APP . 'config.dev.php';

use NewsService\Middleware\Hash;
use NewsService\Core\DI;
use NewsService\Core\Json;


/** INIT */
DI::init(['redis','twig','doctrine']);
$di = DI::getDi();

$di->twig->getLoader()->addPath(TEMPLATES);

$app = new \Slim\Slim($config);

/** ROUTES */

//TWIG
$app->get('/',function() use ($di,$app) {
    $app->response->setBody($di->twig->render('test.twig',['version'=>phpversion()]));
});

//API
$app->group('/api', function() use ($app,$di) {

    $repo = new NewsService\Repo\News();
    $cond = '[0-9]+';

    /** GET news by id*/
    $app->post( '/:hash/get/:id', Hash::getMw(), function($hash,$id) use ($app,$di,$repo) {

        $record = $repo->get($id);

        if ( $record === null )
            $app->halt(400,Json::error('Record not found! Bad ID!'));

        $app->response->setBody(Json::ok($record));

    })->conditions(['id'=>$cond]);

    /** PUSH store/update by id*/
    $app->post('/:hash/push(/:id)', Hash::getMw(), function($hash,$id=null) use ($app,$di,$repo){

        $record = $repo->push($id,$app->request->post());

        if ( $record === null )
            $app->halt(400,Json::error('Record not found!'));

        $app->response->setBody(Json::ok($record));

    })->conditions(['id'=>$cond]);

    /** LIST list of news*/
    $app->post(
        '/:hash/list(/:limit(/:offset(/:sort(/:start(/:end)))))',
        Hash::getMw(),
        function($hash,$limit=10,$offset=0,$sort='-newsDate',$start=null,$end=null) use ($app,$di,$repo){

            $records = $repo->listNews($limit,$offset,$sort,$start,$end);

            if ( $records === null )
                $this->halt(400,Json::error('Records not found!'));

            $app->response->setBody(Json::ok($records));

    });

    /** 404 */
    $app->notFound(function () use ($app) {
        $app->halt(404,Json::error('404 Not found'));
    });

    /** 500 */
    $app->error(function (\Exception $e) use ($app) {
        $app->halt( 500, Json::error($e->getMessage()) );
    });

});


/** RUN */
$app->run();
