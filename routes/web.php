<?php

/** @var \Laravel\Lumen\Routing\Router $router */


$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => 'api'], function () use ($router) {
    $router->post('register', 'UserController@register');
    $router->post('login', 'UserController@login');

    $router->group(['middleware' => 'auth:api'], function () use ($router) {
        $router->post('score', 'UserController@score');
    });

    $router->get('scores', 'UserController@scores');


});
