<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 12/5/2017
 * Time: 1:54 PM
 */

namespace VehicleHistory\Controllers;

use VehicleHistory\Models\User;

class UsersController extends Controller
{
    protected $viewFileName = 'LoginRegister';

    public function login($data) {
        if(array_key_exists('username', $data) && array_key_exists('password', $data)) {
            $u = new User();
            $username = filter_var($data['username'], FILTER_SANITIZE_STRING);
            $password = $data['password'] ?? "";
            return $u->login($username, $password);
        }
        return false;
    }

    public function register($data) {
        if(array_key_exists('username', $data) && array_key_exists('password', $data) && array_key_exists('confirmpassword', $data) && $data['password'] === $data['confirmpassword']) {
            $u = new User();
            $username = filter_var($data['username'], FILTER_SANITIZE_STRING);
            $password = $data['password'] ?? "";
            return $u->register($username, $password);
        }
        return false;
    }
}