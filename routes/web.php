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
    $router->post('/forgot','AuthController@postForgot');
    $router->get('/reset/{token}','AuthController@getReset');
    $router->post('/reset','AuthController@postReset');
});

$router->group(['prefix' => 'users'], function($router) {

    $router->group(['prefix' => 'me'], function($router) {
        $router->get('/','Users\\MeController@getMe');
        $router->put('/','Users\\MeController@putMe');
        $router->put('/password','Users\\MeController@putMePassword');
    });
    $router->get('/','Users\\UserController@getUsers');
    $router->post('/','Users\\UserController@postUser');
    $router->get('/{userId}','Users\\UserController@getUser');
    $router->put('/{userId}','Users\\UserController@putUser');
    $router->delete('/{userId}','Users\\UserController@deleteUser');
});

$router->get('/genders','Users\\GenderController@getGenders');
