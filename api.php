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
            case 'login':
            case 'register':
                return new UsersController();
            default:
                break;
        }
    };


    $getFileContent = function() {
        return json_decode(file_get_contents('php://input'));
    };
    $handleGet = function($args) use ($controllerFactory) {
        $controller = $controllerFactory($args['route']);
        if ($controller !== null) {
            return $controller->httpResponse();
        }
    };

    $handleGetHome = function() {
        $controller = new HomeController();
        return $controller->httpResponse();
    };

    $handleGetAllVehicles = function() {
        $controller = new VehicleController();
        return json_encode($controller->getAll());
    };

    $handleAddVehicle = function() use ($getFileContent) {
        $controller = new VehicleController();
        return json_encode($controller->addVehicle(get_object_vars($getFileContent())));
    };

    $handleUpdateVehicle = function($args) use ($getFileContent) {
        $controller = new VehicleController();
        return json_encode($controller->updateVehicle($args['id'], get_object_vars($getFileContent())));
    };

    $handleDeleteVehicle = function($args) {
        $controller = new VehicleController();
        return json_encode($controller->deleteVehicle($args['id']));
    };

    $handleGetAllServices = function ($args) {
        $controller = new ServiceHistoryController($args['vehicle_id']);
        return json_encode($controller->getAll());
    };

    $handleAddService = function ($args) use ($getFileContent) {
        $controller = new ServiceHistoryController($args['vehicle_id']);
        return json_encode($controller->addService(get_object_vars($getFileContent())));
    };

    $handleUpdateService = function ($args) use ($getFileContent) {
        $controller = new ServiceHistoryController($args['vehicle_id']);
        return json_encode($controller->updateService($args['id'], get_object_vars($getFileContent())));
    };

    $handleDeleteService = function ($args) {
        $controller = new ServiceHistoryController($args['vehicle_id']);
        return json_encode($controller->deleteService($args['id']));
    };

    $handleGetAllFuels = function ($args) {
        $controller = new FuelController($args['vehicle_id']);
        return json_encode($controller->getAll());
    };

    $handleAddFuel = function ($args) use ($getFileContent) {
        $controller = new FuelController($args['vehicle_id']);
        return json_encode($controller->addFuel(get_object_vars($getFileContent())));
    };

    $handleUpdateFuel = function ($args) use ($getFileContent) {
        $controller = new FuelController($args['vehicle_id']);
        return json_encode($controller->updateFuel($args['id'], get_object_vars($getFileContent())));
    };

    $handleDeleteFuel = function ($args) {
        $controller = new FuelController($args['vehicle_id']);
        return json_encode($controller->deleteFuel($args['id']));
    };

    $handleGetAllParts = function ($args) {
        $controller = new PartsController($args['vehicle_id']);
        return json_encode($controller->getAll());
    };

    $handleAddPart = function ($args) use ($getFileContent) {
        $controller = new PartsController($args['vehicle_id']);
        return json_encode($controller->addPart(get_object_vars($getFileContent())));
    };

    $handleUpdatePart = function ($args) use ($getFileContent) {
        $id = $checkToken();
        if ($id) {

        }
        $controller = new PartsController($args['vehicle_id']);
        return json_encode($controller->updatePart($args['id'], get_object_vars($getFileContent())));
    };

    $handleDeletePart = function ($args) {
        $controller = new PartsController($args['vehicle_id']);
        return json_encode($controller->deletePart($args['id']));
    };


    $getHistoryContent = function($args) {
        $controller = new ServiceHistoryController($args['vehicle_id']);
        return $controller->httpResponse();
    };

    $getFuelContent = function($args) {
        $controller = new FuelController($args['vehicle_id']);
        return $controller->httpResponse();
    };

    $getPartsContent = function($args) {
        $controller = new PartsController($args['vehicle_id']);
        return $controller->httpResponse();
    };


    $handleLogin = function() use ($baseURI) {
        $controller = new UsersController();
        $token = $controller->login($_POST);
        if($token !== false) {
            header('Location: https://icarus.cs.weber.edu'.$baseURI.'/vehicles?token='.$token);
        }
        return $controller->httpResponse();
    };

    $handleRegister = function() use ($baseURI) {
        $controller = new UsersController();
        if($controller->register($_POST)) {
            header('Location: https://icarus.cs.weber.edu'.$baseURI.'/vehicles');
        }
        return $controller->httpResponse();
    };

    $vehicleApiEndpoint = $baseURI.'/api/vehicles';
    $servicesApiEndpoint = $vehicleApiEndpoint. '/{vehicle_id:\d+}/history';
    $fuelsApiEndpoint = $vehicleApiEndpoint. '/{vehicle_id:\d+}/fuel';
    $partsApiEndpoint = $vehicleApiEndpoint. '/{vehicle_id:\d+}/parts';


    // api routes
    $r->addRoute(Methods::GET, $servicesApiEndpoint, $handleGetAllServices);
    $r->addRoute(Methods::POST, $servicesApiEndpoint, $handleAddService);
    $r->addRoute(Methods::PATCH, $servicesApiEndpoint.'/{id:\d+}', $handleUpdateService);
    $r->addRoute(Methods::DELETE, $servicesApiEndpoint.'/{id:\d+}', $handleDeleteService);

    $r->addRoute(Methods::GET, $vehicleApiEndpoint, $handleGetAllVehicles);
    $r->addRoute(Methods::POST, $vehicleApiEndpoint, $handleAddVehicle);
    $r->addRoute(Methods::PATCH, $vehicleApiEndpoint.'/{id:\d+}', $handleUpdateVehicle);
    $r->addRoute(Methods::DELETE, $vehicleApiEndpoint.'/{id:\d+}', $handleDeleteVehicle);

    $r->addRoute(Methods::GET, $fuelsApiEndpoint, $handleGetAllFuels);
    $r->addRoute(Methods::POST, $fuelsApiEndpoint, $handleAddFuel);
    $r->addRoute(Methods::PATCH, $fuelsApiEndpoint.'/{id:\d+}', $handleUpdateFuel);
    $r->addRoute(Methods::DELETE, $fuelsApiEndpoint.'/{id:\d+}', $handleDeleteFuel);

    $r->addRoute(Methods::GET, $partsApiEndpoint, $handleGetAllParts);
    $r->addRoute(Methods::POST, $partsApiEndpoint, $handleAddPart);
    $r->addRoute(Methods::PATCH, $partsApiEndpoint.'/{id:\d+}', $handleUpdatePart);
    $r->addRoute(Methods::DELETE, $partsApiEndpoint.'/{id:\d+}', $handleDeletePart);

    // page routes
    $r->addRoute(Methods::GET, $baseURI, $handleGetHome);
    $r->addRoute(Methods::GET, $baseURI.'/{route}', $handleGet);
    $r->addRoute(Methods::GET, $baseURI.'/vehicles/{vehicle_id:\d+}/history', $getHistoryContent);
    $r->addRoute(Methods::GET, $baseURI.'/vehicles/{vehicle_id:\d+}/fuel', $getFuelContent);
    $r->addRoute(Methods::GET, $baseURI.'/vehicles/{vehicle_id:\d+}/parts', $getPartsContent);

    // login form routes
    $r->addRoute(Methods::POST, $baseURI.'/login', $handleLogin);
    $r->addRoute(Methods::POST, $baseURI.'/register', $handleRegister);
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











