<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->get('/test','ExampleController@generateUser');


$router->group(['prefix' => 'auth'], function($router) {
    $router->post('/login','AuthController@postAuthenticate');
    $router->post('/refresh','AuthController@postRefresh');
});

$router->group(['prefix' => 'users'], function($router) {
    $router->get('/','Users\\UserController@getUsers');
    $router->post('/','Users\\UserController@postUser');
    $router->get('/{userId}','Users\\UserController@getUser');
    $router->put('/{userId}','Users\\UserController@putUser');
    $router->delete('/{userId}','Users\\UserController@deleteUser');
});

$router->group(['prefix' => 'classes'], function($router) {
    $router->get('/','Classes\\ClassesController@getClasses');
    $router->post('/','Classes\\ClassesController@postClass');
    $router->get('/{classId}','Classes\\ClassesController@getClass');
    $router->put('/{classId}','Classes\\ClassesController@putClass');
    $router->delete('/{classId}','Classes\\ClassesController@deleteClass');
});
