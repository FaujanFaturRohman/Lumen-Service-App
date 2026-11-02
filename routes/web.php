<?php
/** @var \Laravel\Lumen\Routing\Router $router */

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
    $router->get('/', function () use ($router) {
    return $router->app->version();
});
}