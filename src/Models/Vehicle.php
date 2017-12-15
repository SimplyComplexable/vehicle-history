<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 12/5/2017
 * Time: 1:39 PM
 */

namespace VehicleHistory\Models;

use VehicleHistory\Utilities\DatabaseConnection;

class Vehicle
{
    private $vehicle_id;
    private $model_year;
    private $make;
    private $model;
    private $color;
    private $license_plate_number;
    private $vin;
    private $db;

    public function __construct(...$args)
    {
        $this->initializeDatabase();
        switch (count($args)) {
            case 0:
                break;
            case 1:
                $this->constructWithId($args[0]);
                break;
            default:
                $this->constructWithValues(...$args);
        }
    }

    private function constructWithId(int $id) {
        $this->vehicle_id = $id;
        $statement = $this->db->prepare('SELECT * FROM `vehicles` WHERE `vehicle_id` = :vehicle_id');
        $statement->bindParam(':vehicle_id', $this->vehicle_id);
        $statement->setFetchMode(\PDO::FETCH_ASSOC);
        $statement->execute();

        $data = $statement->fetch();
        foreach($data as $key => $value) {
            $this->set($key, $value);
        }

    }

    private function constructWithValues($model_year, $make, $model, $color = "", $licenseplatenumber = "", $vin = "") {
        $this->model_year = $model_year;
        $this->make = $make;
        $this->model = $model;
        $this->color = $color;
        $this->license_plate_number = $licenseplatenumber;
        $this->vin = $vin;
    }

    private function initializeDatabase() {
        $this->db = DatabaseConnection::getInstance();
    }

    private function isValidUser($user_id) {
        $statement = $this->db->prepare('SELECT `user_id` from `vehicles` WHERE `vehicle_id` = :vehicle_id');
        $statement->bindParam(':vehicle_id', $this->vehicle_id);
        $statement->setFetchMode(\PDO::FETCH_ASSOC);
        $statement->execute();
        $response = $statement->fetch();
        return $response['user_id'] === $user_id;
    }

    public static function getAll(): array {
        $db = DatabaseConnection::getInstance();
        $statement = $db->prepare('SELECT * FROM `vehicles`');
        $statement->setFetchMode(\PDO::FETCH_ASSOC);

        $statement->execute();
        return $statement->fetchAll();
    }

    public function set($field, $value) {
        if (property_exists($this,$field)) {
            $this->$field = $value;
        }
    }

    public function save($user_id) {
        if (!$this->isValidUser($user_id)) {
            return array(
                'success' => false
            );
        }
        if ($this->vehicle_id === null || $this->vehicle_id === '') {
            return $this->create($user_id);
        }

        return $this->update();
    }

    public function create($user_id) {
        $statement = $this->db->prepare('
          INSERT INTO `vehicles` 
          (`model_year`, `make`, `model`, `color`, `license_plate_number`, `vin`, `user_id`)
          VALUES
          (:model_year, :make, :model, :color, :license_plate_number, :vin, :user_id)
          ');

        $statement->bindParam(':model_year', $this->model_year);
        $statement->bindParam(':make', $this->make);
        $statement->bindParam(':model', $this->model);
        $statement->bindParam(':color', $this->color);
        $statement->bindParam(':license_plate_number', $this->license_plate_number);
        $statement->bindParam(':vin', $this->vin);
        $statement->bindValue(':user_id', $user_id);

        if ($statement->execute()) {
            return array(
                'success' => true,
                'id' => $this->db->lastInsertId()
            );
        }
    }

    public function update() {
        $statement = $this->db->prepare('
              UPDATE `vehicles` 
              SET `model_year` = :model_year,
              `make` = :make,
              `model` = :model,
              `color` = :color,
              `license_plate_number` = :license_plate_number,
              `vin` = :vin
              WHERE vehicle_id = :vehicle_id
          ');

        $statement->bindParam(':model_year', $this->model_year);
        $statement->bindParam(':make', $this->make);
        $statement->bindParam(':model', $this->model);
        $statement->bindParam(':color', $this->color);
        $statement->bindParam(':license_plate_number', $this->license_plate_number);
        $statement->bindParam(':vin', $this->vin);
        $statement->bindParam(':vehicle_id', $this->vehicle_id);

        return array(
            'success' => $statement->execute()
        );
    }

    public function delete($user_id) {
        if (!$this->isValidUser($user_id)) {
            return array(
                'success' => false
            );
        }
        $statement = $this->db->prepare('
            DELETE
            FROM `vehicles`
            WHERE vehicle_id = :vehicle_id
        ');

        $statement->bindParam(':vehicle_id', $this->vehicle_id);
        return array(
            'success' => $statement->execute()
        );
    }

    //---------------------------------

    /**
     * @return mixed
     */
    public function getModelYear()
    {
        return $this->model_year;
    }

    /**
     * @param mixed $model_year
     */
    public function setModelYear(string $model_year)
    {
        $this->model_year = $model_year;
    }

    /**
     * @return mixed
     */
    public function getMake()
    {
        return $this->make;
    }

    /**
     * @param mixed $make
     */
    public function setMake(string $make)
    {
        $this->make = $make;
    }

    /**
     * @return mixed
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param mixed $model
     */
    public function setModel(string $model)
    {
        $this->model = $model;
    }

    /**
     * @return mixed
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @param mixed $color
     */
    public function setColor(string $color)
    {
        $this->color = $color;
    }

    /**
     * @return mixed
     */
    public function getLicenseplatenumber()
    {
        return $this->license_plate_number;
    }

    /**
     * @param mixed $license_plate_number
     */
    public function setLicenseplatenumber(string $license_plate_number)
    {
        $this->license_plate_number = $license_plate_number;
    }

    /**
     * @return mixed
     */
    public function getVin()
    {
        return $this->vin;
    }

    /**
     * @param mixed $vin
     */
    public function setVin(string $vin)
    {
        $this->vin = $vin;
    }
}