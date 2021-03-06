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
use VehicleHistory\Models\Token;

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

    $checkToken = function() {
        return Token::getIDFromToken();
    };

    $getFileContent = function() {
        return json_decode(file_get_contents('php://input'));
    };

    //
    // MAIN ROUTE HANDLERS
    //

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

    //
    // VEHICLE ROUTE HANDLERS
    //

    $handleGetAllVehicles = function() use ($checkToken) {
        $user_id = $checkToken();
        if(!$user_id)
            return false;
        $controller = new VehicleController();
        return json_encode($controller->getAll($user_id));
    };

    $handleAddVehicle = function() use ($getFileContent, $checkToken) {
        $user_id = $checkToken();
        if(!$user_id)
            return false;
        $controller = new VehicleController();
        return json_encode($controller->addVehicle($user_id, get_object_vars($getFileContent())));
    };

    $handleUpdateVehicle = function($args) use ($getFileContent, $checkToken) {
        $user_id = $checkToken();
        if(!$user_id)
            return false;
        $controller = new VehicleController();
        return json_encode($controller->updateVehicle($user_id, $args['id'], get_object_vars($getFileContent())));
    };

    $handleDeleteVehicle = function($args) use ($checkToken) {
        $user_id = $checkToken();
        if(!$user_id)
            return false;
        $controller = new VehicleController();
        return json_encode($controller->deleteVehicle($user_id, $args['id']));
    };

    //
    // SERVICE ROUTE HANDLERS
    //

    $handleGetAllServices = function ($args) use ($checkToken) {
        $user_id = $checkToken();
        if(!$user_id)
            return false;
        $controller = new ServiceHistoryController($args['vehicle_id']);
        return json_encode($controller->getAll($user_id));
    };

    $handleAddService = function ($args) use ($getFileContent, $checkToken) {
        $user_id = $checkToken();
        if(!$user_id)
            return false;
        $controller = new ServiceHistoryController($args['vehicle_id']);
        return json_encode($controller->addService($user_id, get_object_vars($getFileContent())));
    };

    $handleUpdateService = function ($args) use ($getFileContent, $checkToken) {
        $user_id = $checkToken();
        if(!$user_id)
            return false;
        $controller = new ServiceHistoryController($args['vehicle_id']);
        return json_encode($controller->updateService($user_id, $args['id'], get_object_vars($getFileContent())));
    };

    $handleDeleteService = function ($args) use ($getFileContent, $checkToken) {
        $user_id = $checkToken();
        if(!$user_id)
            return false;
        $controller = new ServiceHistoryController($args['vehicle_id']);
        return json_encode($controller->deleteService($user_id, $args['id']));
    };

    //
    // FUEL ROUTE HANDLERS
    //

    $handleGetAllFuels = function ($args) use ($checkToken) {
        $user_id = $checkToken();
        if(!$user_id)
            return false;
        $controller = new FuelController($args['vehicle_id']);
        return json_encode($controller->getAll($user_id));
    };

    $handleAddFuel = function ($args) use ($getFileContent, $checkToken) {
        $user_id = $checkToken();
        if(!$user_id)
            return false;
        $controller = new FuelController($args['vehicle_id']);
        return json_encode($controller->addFuel($user_id, get_object_vars($getFileContent())));
    };

    $handleUpdateFuel = function ($args) use ($getFileContent, $checkToken) {
        $user_id = $checkToken();
        if(!$user_id)
            return false;
        $controller = new FuelController($args['vehicle_id']);
        return json_encode($controller->updateFuel($user_id, $args['id'], get_object_vars($getFileContent())));
    };

    $handleDeleteFuel = function ($args) use ($getFileContent, $checkToken) {
        $user_id = $checkToken();
        if(!$user_id)
            return false;
        $controller = new FuelController($args['vehicle_id']);
        return json_encode($controller->deleteFuel($user_id, $args['id']));
    };

    //
    // PARTS ROUTE HANDLERS
    //

    $handleGetAllParts = function ($args) use ($checkToken){
        $user_id = $checkToken();
        if (!$user_id)
            return false;
        $controller = new PartsController($args['vehicle_id']);
        return json_encode($controller->getAll($user_id));
    };

    $handleAddPart = function ($args) use ($getFileContent, $checkToken) {
        $user_id = $checkToken();
        if (!$user_id)
            return false;
        $controller = new PartsController($args['vehicle_id']);
        return json_encode($controller->addPart($user_id, get_object_vars($getFileContent())));
    };

    $handleUpdatePart = function ($args) use ($getFileContent, $checkToken) {
        $user_id = $checkToken();
        if (!$user_id)
            return false;
        $controller = new PartsController($args['vehicle_id']);
        return json_encode($controller->updatePart($user_id, $args['id'], get_object_vars($getFileContent())));
    };

    $handleDeletePart = function ($args) use ($checkToken) {
        $user_id = $checkToken();
        if (!$user_id)
            return false;
        $controller = new PartsController($args['vehicle_id']);
        return json_encode($controller->deletePart($user_id, $args['id']));
    };

    //
    // MISC ROUTE HANDLERS
    //

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

    //
    // LOGIN/REGISTER ROUTE HANDLERS
    //

    $handleLogin = function() use ($baseURI) {
        $controller = new UsersController();
        $token = $controller->login($_POST);
        if($token !== false) {
            header('Location: https://icarus.cs.weber.edu'.$baseURI.'/vehicles?token='.$token);
        }
        $controller->setLoginError(true);
        return $controller->httpResponse();
    };

    $handleRegister = function() use ($baseURI) {
        $controller = new UsersController();
        if($controller->register($_POST)) {
            header('Location: https://icarus.cs.weber.edu'.$baseURI.'/vehicles');
        }
        $controller->setRegisterError(true);
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

    // login/register routes
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











