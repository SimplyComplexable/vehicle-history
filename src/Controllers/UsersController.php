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
            return $u->login($data['username'], $data['password']);
        }
        return false;
    }

    public function register($data) {
        if(array_key_exists('username', $data) && array_key_exists('password', $data) && array_key_exists('confirmpassword', $data)) {
            if($data['password'] === $data['confirmpassword']){
                $u = new User();
                $u->register($data['username'], $data['password']);
                return true;
            }
        }
        return false;
    }
}