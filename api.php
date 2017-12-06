<?php
/**
 * Created by PhpStorm.
 * User: iamcaptaincode
 * Date: 10/13/2016
 * Time: 8:56 AM
 */

require_once 'config.php';
require_once 'vendor/autoload.php';
use VehicleHistory\Http\Methods;
use VehicleHistory\Controllers\FuelController;
use VehicleHistory\Controllers\HomeController;
use VehicleHistory\Controllers\PartsController;
use VehicleHistory\Controllers\ServiceHistoryController;
use VehicleHistory\Controllers\VehicleController;
use VehicleHistory\Controllers\UsersController;

$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r)  use ($baseURI) {

    $controllerFactory = function($type) {
        switch ($type) {
            case 'fuel':
                return new FuelController();
            case 'parts':
                return new PartsController();
            case 'vehicles':
                return new VehicleController();
            case 'history':
                return new ServiceHistoryController();
            case 'login':
            case 'register':
                return new UsersController();
        }
    };

    $handleGet = function($args) use ($controllerFactory) {
        $controller = $controllerFactory($args['route']);
        return $controller->httpResponse();
    };

    $handleGetHome = function() {
        $controller = new HomeController();
        return $controller->httpResponse();
    };

    $r->addRoute(Methods::GET, $baseURI, $handleGetHome);
    $r->addRoute(Methods::GET, $baseURI.'/{route}', $handleGet);
});

$method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

$pos = strpos($uri, '?');
if ($pos !== false) {
    $uri = substr($uri, 0, $pos);
}
$uri = rtrim($uri, "/");

$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($method, $uri);

switch($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        var_dump($routeInfo);
//        http_response_code(VehicleHistory\Http\StatusCodes::NOT_FOUND);
        //Handle 404
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        http_response_code(VehicleHistory\Http\StatusCodes::METHOD_NOT_ALLOWED);
        //Handle 403
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler  = $routeInfo[1];
        $vars = $routeInfo[2];

        $response = $handler($vars);
        echo $response;
        break;
}











