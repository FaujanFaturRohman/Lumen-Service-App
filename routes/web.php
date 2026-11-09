<?php
/** @var \Laravel\Lumen\Routing\Router $router */

$router->get('/', function () use ($router) {
    return $router->app->version();
});

// Authentication (tanpa token)
$router->group(['prefix' => 'auth'], function () use ($router) {
    $router->post('/register', 'AuthController@register');
    $router->post('/login', 'AuthController@login');
});

// Semua route di bawah ini wajib login (header Authorization: Bearer <token>)
$router->group(['middleware' => 'auth'], function () use ($router) {
    $router->delete('/auth/logout', 'AuthController@logout');

    foreach ([
        'categories' => 'CategoriesController',
        'products'   => 'ProductsController',
        'customers'  => 'CustomersController',
        'orders'     => 'OrdersController',
        'users'      => 'UsersController',
    ] as $path => $controller) {
        $router->get("/$path",         "$controller@index");
        $router->get("/$path/{id}",    "$controller@show");
        $router->post("/$path",        "$controller@store");
        $router->put("/$path/{id}",    "$controller@update");
        $router->delete("/$path/{id}", "$controller@destroy");
    }
});
