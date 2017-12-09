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
use VehicleHistory\Http\StatusCodes;
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

    $getFileContent = function() {
        return json_decode(file_get_contents('php://input'));
    };

    $handleGet = function($args) use ($controllerFactory) {
        $controller = $controllerFactory($args['route']);
        return $controller->httpResponse();
    };

    $handleGetHome = function() {
        $controller = new HomeController();
        return $controller->httpResponse();
    };

    $handleAddVehicle = function() use ($getFileContent) {
        $controller = new VehicleController();
        return json_encode(array(
            'success' => $controller->addVehicle(get_object_vars($getFileContent()))
        ));
    };

    $handleUpdateVehicle = function($args) use ($getFileContent) {
        $controller = new VehicleController();
        return json_encode(array(
            'success' => $controller->updateVehicle($args['id'], get_object_vars($getFileContent()))
        ));
    };

    $handleDeleteVehicle = function($args) {
        $controller = new VehicleController();
        return json_encode(array(
            'success' => $controller->deleteVehicle($args['id'])
        ));
    };

    $r->addRoute(Methods::GET, $baseURI, $handleGetHome);
    $r->addRoute(Methods::GET, $baseURI.'/{route}', $handleGet);
    $r->addRoute(Methods::POST, $baseURI.'/vehicles', $handleAddVehicle);
    $r->addRoute(Methods::PATCH, $baseURI.'/vehicles/{id:\d}', $handleUpdateVehicle);
    $r->addRoute(Methods::DELETE, $baseURI.'/vehicles/{id:\d}', $handleDeleteVehicle);
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
        http_response_code(StatusCodes::NOT_FOUND);
        //Handle 404
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        http_response_code(     StatusCodes::METHOD_NOT_ALLOWED);
        //Handle 403
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler  = $routeInfo[1];
        $vars = $routeInfo[2];

        $response = $handler($vars);
        echo $response;
        break;
}











