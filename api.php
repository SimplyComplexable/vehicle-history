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
use VehicleHistory\Controllers\TokensController;

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

    /** TOKENS CLOSURE */
    $handlePostToken = function ($args) {
        $tokenController = new TokensController();
        //Is the data via a form?
        if (!empty($_POST['username'])) {
            $username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
            $password = $_POST['password'] ?? "";
        } else {
            //Attempt to parse json input
            $json = (object) json_decode(file_get_contents('php://input'));
            if (count((array)$json) >= 2) {
                $username = filter_var($json->username, FILTER_SANITIZE_STRING);
                $password = $json->password;
            } else {
                http_response_code(StatusCodes::BAD_REQUEST);
                exit();
            }
        }
        return $tokenController->buildToken($username, $password);

    };

    $getFileContent = function() {
        return json_decode(file_get_contents('php://input'));
    };
    $handleGet = function($args) use ($controllerFactory) {
        $controller = $controllerFactory($args['route']);
        if ($controller !== null) {
            return $controller->httpResponse();
        }
        http_response_code(StatusCodes::NOT_FOUND);
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

    $handleDeleteService = function ($args) use ($getFileContent) {
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

    $handleDeleteFuel = function ($args) use ($getFileContent) {
        $controller = new FuelController($args['vehicle_id']);
        return json_encode($controller->deleteFuel($args['id']));
    };


    $getHistoryContent = function($args) {
        $controller = new ServiceHistoryController($args['vehicle_id']);
        return $controller->httpResponse();
    };

    $getFuelContent = function($args) {
        $controller = new FuelController($args['vehicle_id']);
        return $controller->httpResponse();
    };


    $handleLogin = function() use ($baseURI) {
        $controller = new UsersController();
        if($controller->login($_POST)) {
            header('Location: https://icarus.cs.weber.edu'.$baseURI.'/vehicles');
        }
        else {
            return $controller->httpResponse();
        }
    };

    $handleRegister = function() use ($baseURI) {
        $controller = new UsersController();
        if($controller->register($_POST)) {
            header('Location: https://icarus.cs.weber.edu'.$baseURI.'/vehicles');
        }
        else {
            return $controller->httpResponse();
        }
    };

    $vehicleApiEndpoint = $baseURI.'/api/vehicles';
    $servicesApiEndpoint = $vehicleApiEndpoint. '/{vehicle_id:\d+}/history';
    $fuelsApiEndpoint = $vehicleApiEndpoint. '/{vehicle_id:\d+}/fuel';


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

    // page routes
    $r->addRoute(Methods::GET, $baseURI, $handleGetHome);
    $r->addRoute(Methods::GET, $baseURI.'/{route}', $handleGet);
    $r->addRoute(Methods::GET, $baseURI.'/vehicles/{vehicle_id:\d+}/history', $getHistoryContent);
    $r->addRoute(Methods::GET, $baseURI.'/vehicles/{vehicle_id:\d+}/fuel', $getFuelContent);

    // login form routes
    $r->addRoute(Methods::POST, $baseURI.'/login', $handleLogin);
    $r->addRoute(Methods::POST, $baseURI.'/register', $handleRegister);

    // TOKEN ROUTE
    $r->addRoute(Methods::POST, $baseURI . '/tokens', $handlePostToken);

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











