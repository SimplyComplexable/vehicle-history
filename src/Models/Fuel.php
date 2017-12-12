<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 12/5/2017
 * Time: 8:08 PM
 */

namespace VehicleHistory\Models;

use VehicleHistory\Utilities\DatabaseConnection;

class Fuel
{
    private $fuel_id;
    private $date;      // date of fill up
    private $odometer;  // total mileage on vehicle at fuel fill up
    private $distance;  // trip odometer when fueled up (how many miles on this tank)
    private $volume;    // how much fuel purchased (gals in US)
    private $cost;      // how much the fuel cost
    private $mpg;       // miles per gallon
    private $vehicle_id;


    public function __construct(...$args) {
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
        $this->fuel_id = $id;
        $statement = $this->db->prepare('SELECT * FROM `fuel` WHERE `fuel_id` = :fuel_id');
        $statement->bindParam(':fuel_id', $this->fuel_id);
        $statement->setFetchMode(\PDO::FETCH_ASSOC);
        $statement->execute();

        $data = $statement->fetch();
        foreach($data as $key => $value) {
            $this->set($key, $value);
        }
    }

    private function constructWithValues($date, $odometer, $distance, $volume, $cost, $mpg) {
        $this->date = $date;
        $this->odometer = $odometer;
        $this->distance = $distance;
        $this->volume = $volume;
        $this->cost = $cost;
        $this->mpg = $mpg;
    }

    private function initializeDatabase() {
        $this->db = DatabaseConnection::getInstance();
    }

    public static function getAllForVehicle($vehicle_id): array {
        $db = DatabaseConnection::getInstance();
        $statement = $db->prepare('SELECT * FROM `fuel` WHERE `vehicle_id` = :vehicle_id');
        $statement->bindParam(':vehicle_id', $vehicle_id);
        $statement->setFetchMode(\PDO::FETCH_ASSOC);

        $statement->execute();
        return $statement->fetchAll();
    }

    public function set($field, $value) {
        if (property_exists($this,$field)) {
            $this->$field = $value;
        }
    }

    public function save() {
        if ($this->fuel_id === null || $this->fuel_id === '') {
            return $this->create();
        }
        return $this->update();
    }

    public function create() {
        $statement = $this->db->prepare('
          INSERT INTO `fuel` 
          (`date`, `odometer`, `distance`, `volume`, `cost`, `mpg`)
          VALUES
          (:date, :odometer, :distance, :volume, :cost, :mpg)
        ');

        $statement->bindParam(':date', $this->date);
        $statement->bindParam(':odometer', $this->odometer);
        $statement->bindParam(':distance', $this->distance);
        $statement->bindParam(':volume', $this->volume);
        $statement->bindParam(':cost', $this->cost);
        $statement->bindParam(':mpg', $this->mpg);

        if ($statement->execute()) {
            return array(
                'success' => true,
                'id' => $this->db->lastInsertId()
            );
        }
    }

    public function update() {
        $statement = $this->db->prepare('
              UPDATE `fuel` 
              SET `date` = :date,
              `odometer` = :odometer,
              `distance` = :distance,
              `volume` = :volume,
              `cost` = :cost,
              `mpg` = :mpg,
              `vehicle_id` = :vehicle_id
              WHERE fuel_id = :fuel_id
          ');

        $statement->bindParam(':date', $this->date);
        $statement->bindParam(':odometer', $this->odometer);
        $statement->bindParam(':distance', $this->distance);
        $statement->bindParam(':volume', $this->volume);
        $statement->bindParam(':cost', $this->cost);
        $statement->bindParam(':mpg', $this->mpg);
        $statement->bindParam(':vehicle_id', $this->vehicle_id);
        $statement->bindParam(':fuel_id', $this->fuel_id);

        return array(
            'success' => $statement->execute()
        );
    }

    public function delete() {
        $statement = $this->db->prepare('
            DELETE
            FROM `fuel`
            WHERE fuel_id = :fuel_id
        ');

        $statement->bindParam(':fuel_id', $this->fuel_id);
        return array(
            'success' => $statement->execute()
        );
    }

    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param mixed $date
     */
    public function setDate(string $date)
    {
        $this->date = $date;
    }

    /**
     * @return mixed
     */
    public function getOdometer()
    {
        return $this->odometer;
    }

    /**
     * @param mixed $odometer
     */
    public function setOdometer(string $odometer)
    {
        $this->odometer = $odometer;
    }

    /**
     * @return mixed
     */
    public function getDistance()
    {
        return $this->distance;
    }

    /**
     * @param mixed $distance
     */
    public function setDistance(string $distance)
    {
        $this->distance = $distance;
    }

    /**
     * @return mixed
     */
    public function getVolume()
    {
        return $this->volume;
    }

    /**
     * @param mixed $volume
     */
    public function setVolume(string $volume)
    {
        $this->volume = $volume;
    }

    /**
     * @return mixed
     */
    public function getCost()
    {
        return $this->cost;
    }

    /**
     * @param mixed $cost
     */
    public function setCost(string $cost)
    {
        $this->cost = $cost;
    }

    /**
     * @return mixed
     */
    public function getMpg()
    {
        return $this->mpg;
    }

    /**
     * @param mixed $mpg
     */
    public function setMpg(string $mpg)
    {
        $this->mpg = $mpg;
    }
}