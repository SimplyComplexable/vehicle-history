<?php
/**
 * Created by PhpStorm.
 * User: iamcaptaincode
 * Date: 10/13/2016
 * Time: 8:56 AM
 */

require_once 'config.php';
require_once 'vendor/autoload.php';
use VehicleHistory\Http\Methods as Methods;
use VehicleHistory\Http\StatusCodes as StatusCodes;

$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r)  use ($baseURI) {
    /** TOKENS CLOSURES */
    $handlePostToken = function ($args) {
        $tokenController = new \VehicleHistory\Controllers\TokensController();
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

    /** USER CLOSURES */
    $handleFullUpdateUser = function ($args) {
        return (new \VehicleHistory\Controllers\UsersController)->fullUpdateUser($args);
    };
    $handleDeleteUser = function ($args) {
        return (new \VehicleHistory\Controllers\UsersController)->deleteUser($args);
    };

    $handlePartialUpdateUser = function($args) {
        return (new \VehicleHistory\Controllers\UsersController)->updateUser($args);
    };

    $handleGetAllStudents = function(){
        return (new \VehicleHistory\Controllers\UsersController)->getAllStudents();
    };

    $handleGetUser = function($args){
        return (new VehicleHistory\Controllers\UsersController)->getUser($args);
    };

    $handleAddUser = function(){
        return (new VehicleHistory\Controllers\UsersController)->addUser();
    };

    /** USER ROUTE */
    $r->addRoute(Methods::PUT, $baseURI.'/users/{id:\d+}', $handleFullUpdateUser);
    $r->addRoute(Methods::PATCH, $baseURI.'/users/{id:\d+}', $handlePartialUpdateUser);
    $r->addRoute(Methods::GET, $baseURI.'/users/students', $handleGetAllStudents);
    $r->addRoute(Methods::GET,$baseURI.'/users/{id:\d+}', $handleGetUser);
    $r->addRoute(Methods::POST,$baseURI.'/users/', $handleAddUser);
    $r->addRoute(Methods::DELETE, $baseURI.'/users/{id:\d+}', $handleDeleteUser);
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
        http_response_code(VehicleHistory\Http\StatusCodes::NOT_FOUND);
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
        echo json_encode($response);
        break;
}











