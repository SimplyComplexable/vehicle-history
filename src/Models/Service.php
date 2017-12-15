<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 12/5/2017
 * Time: 8:14 PM
 */

namespace VehicleHistory\Models;

use VehicleHistory\Utilities\DatabaseConnection;

class Service
{
    private $service_id;
    private $date;      // date of service
    private $service;   // short service description
    private $odometer;  // total mileage on vehicle at time of service
    private $cost;      // total cost of service
    private $location;  // place where service was performed
    private $vehicle_id;
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
        $this->service_id = $id;
        $statement = $this->db->prepare('SELECT * FROM `service` WHERE `service_id` = :service_id');
        $statement->bindParam(':service_id', $this->service_id);
        $statement->setFetchMode(\PDO::FETCH_ASSOC);
        $statement->execute();

        $data = $statement->fetch();
        foreach($data as $key => $value) {
            $this->set($key, $value);
        }
    }

    private function constructWithValues($date, $service, $odometer, $cost, $location, $vin, $vehicle_id) {
        $this->date = $date;
        $this->service = $service;
        $this->odometer = $odometer;
        $this->cost = $cost;
        $this->location = $location;
        $this->vehicle_id = $vehicle_id;
    }

    private function initializeDatabase() {
        $this->db = DatabaseConnection::getInstance();
    }

    private function isValidUser($user_id) {
        $statement = $this->db->prepare('SELECT `user_id` from `service` WHERE `service_id` = :service_id');
        $statement->bindParam(':vehicle_id', $this->vehicle_id);
        $statement->setFetchMode(\PDO::FETCH_ASSOC);
        $statement->execute();
        $response = $statement->fetch();
        return $response['user_id'] === $user_id;
    }

    public static function getAllForVehicle($vehicle_id): array {
        $db = DatabaseConnection::getInstance();
        $statement = $db->prepare('SELECT * FROM `service` WHERE `service_id` = :service_id');
        $statement->bindParam(':service_id', service_idœ);
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
        if ($this->service_id === null || $this->service_id === '') {
            return $this->create($user_id);
        }
        return $this->update();
    }

    public function create() {
        $statement = $this->db->prepare('
          INSERT INTO `service` 
          (`date`, `service`, `odometer`, `cost`, `location`, `vehicle_id`)
          VALUES
          (:date, :service, :odometer, :cost, :location, :vehicle_id)
          ');

        $statement->bindParam(':date', $this->date);
        $statement->bindParam(':service', $this->service);
        $statement->bindParam(':odometer', $this->odometer);
        $statement->bindParam(':cost', $this->cost);
        $statement->bindParam(':location', $this->location);
        $statement->bindParam(':vehicle_id', $this->vehicle_id);

        if ($statement->execute()) {
            return array(
                'success' => true,
                'id' => $this->db->lastInsertId()
            );
        }
    }

    public function update() {
        $statement = $this->db->prepare('
              UPDATE `service` 
              SET `date` = :date,
              `service` = :service,
              `odometer` = :odometer,
              `cost` = :cost,
              `location` = :location,
              `vehicle_id` = :vehicle_id
              WHERE service_id = :service_id
          ');

        $statement->bindParam(':date', $this->date);
        $statement->bindParam(':service', $this->service);
        $statement->bindParam(':odometer', $this->odometer);
        $statement->bindParam(':cost', $this->cost);
        $statement->bindParam(':location', $this->location);
        $statement->bindParam(':vehicle_id', $this->vehicle_id);
        $statement->bindParam(':service_id', $this->service_id);

        $response = $statement->execute();

        return array(
            'success' => $response //$statement->execute()
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
            FROM `service`
            WHERE service_id = :service_id
        ');

        $statement->bindParam(':service_id', $this->service_id);
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
    public function getService()
    {
        return $this->service;
    }

    /**
     * @param mixed $service_item
     */
    public function setService(string $service)
    {
        $this->service = $service;
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
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param mixed $location
     */
    public function setLocation(string $location)
    {
        $this->location = $location;
    }
}