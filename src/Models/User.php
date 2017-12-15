<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 12/5/2017
 * Time: 1:39 PM
 */

namespace VehicleHistory\Models;

use VehicleHistory\Utilities\DatabaseConnection;
use VehicleHistory\Models\Token;

class User
{
    private $user_id;
    private $username;
    private $password;
    private $token;


    public function __construct(){
    }

    public function login($username, $password) {
        $this->username = $username;
        $this->password = $password;
        $user = $this->getUserInfo($username);
        if(!$user)
            return false;
        if (password_verify($password, $user['password'])) {
            return $this->buildToken($user['user_id'], $username);
        }
        return false;
    }

    public function register($username, $password) {
        $this->username = $username;
        $this->password = $password;
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $userId = $this->insertUserInfo($username, $hashedPassword);
        if($userId)
            return $this->buildToken($userId, $username);
        return false;
    }

    private function getUserInfo($username) {
        $db = DatabaseConnection::getInstance();
        $statement = $db->prepare('SELECT * FROM `user` WHERE `username` = :username');
        $statement->bindParam(':username', $username);
        $statement->setFetchMode(\PDO::FETCH_ASSOC);

        $statement->execute();
        return $statement->fetch();
    }

    private function insertUserInfo($username, $password) {
        $db = DatabaseConnection::getInstance();
        $statement = $db->prepare('INSERT INTO `user` (`username`, `password`) VALUES(:username, :password)');
        $statement->bindParam(':username', $username);
        $statement->bindParam(':password', $password);
        if(!$statement->execute())
            return false;
        return $db->lastInsertId();
    }

    public function buildToken($userId, $username) {
        $this->token = Token::buildToken($userId, $username);
        var_dump($this->token);
        return $this->token;
    }

    private function constructWithToken() {
        $this->user_id = 1;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     */
    public function setUsername(string $username)
    {
        $this->username = $username;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword(string $password)
    {
        $this->password = $password;
    }
}