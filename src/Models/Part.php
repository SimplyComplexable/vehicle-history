<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 12/5/2017
 * Time: 8:22 PM
 */

namespace VehicleHistory\Models;

use VehicleHistory\Utilities\DatabaseConnection;

class Part
{
    private $part_id;
    private $part_name;
    private $date;
    private $price;
    private $manufacturer;  // who makes the part
    private $vendor;        // who the part was purchased from
    private $notes;         // any notes about the part
    private $vehicle_id;
    private $db;

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
        $this->part_id = $id;
        $statement = $this->db->prepare('SELECT * FROM `part` WHERE `part_id` = :part_id');
        $statement->bindParam(':part_id', $this->part_id);
        $statement->setFetchMode(\PDO::FETCH_ASSOC);
        $statement->execute();

        $data = $statement->fetch();
        foreach($data as $key => $value) {
            $this->set($key, $value);
        }
    }

    private function constructWithValues($part_id, $part_name, $price, $manufacturer, $vendor, $notes, $vehicle_id)
    {
        $this->part_id = $part_id;
        $this->part_name = $part_name;
        $this->price = $price;
        $this->manufacturer = $manufacturer;
        $this->vendor = $vendor;
        $this->notes = $notes;
        $this->vehicle_id = $vehicle_id;
    }

    private function initializeDatabase() {
        $this->db = DatabaseConnection::getInstance();
    }

    private function isValidUser($user_id) {
        $statement = $this->db->prepare('
          SELECT `vehicles`.`user_id` 
          FROM `part` 
          INNER JOIN `vehicles`
          ON `vehicles`.`vehicle_id` = `part`.`vehicle_id`
          WHERE `part_id` = :part_id');
        $statement->bindParam(':part_id', $this->part_id);
        $statement->setFetchMode(\PDO::FETCH_ASSOC);
        $statement->execute();
        $response = $statement->fetch();
        return $response['user_id'] === $user_id;
    }


    public static function getAllForVehicle($vehicle_id): array {
        $db = DatabaseConnection::getInstance();
        $statement = $db->prepare('SELECT * FROM `part` WHERE `vehicle_id` = :vehicle_id');
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

    public function save($user_id) {
        if ($this->part_id === null || $this->part_id === '') {
            return $this->create($user_id);
        }
        if (!$this->isValidUser($user_id)) {
            return array(
                'success' => false
            );
        }
        return $this->update();
    }

    public function create() {
        $statement = $this->db->prepare('
          INSERT INTO `part` 
          (`part_name`, `price`, `date`, `manufacturer`, `vendor`, `notes`, `vehicle_id`)
          VALUES
          (:part_name, :price, :date, :manufacturer, :vendor, :notes, :vehicle_id)
        ');
        $statement->bindParam(':part_name', $this->part_name);
        $statement->bindParam(':date', $this->date);
        $statement->bindParam(':price', $this->price);
        $statement->bindParam(':manufacturer', $this->manufacturer);
        $statement->bindParam(':vendor', $this->vendor);
        $statement->bindParam(':notes', $this->notes);
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
              UPDATE `part` 
              SET 
              `part_name` = :part_name,
              `date` = :date,
              `price` = :price,
              `manufacturer` = :manufacturer,
              `vendor` = :vendor,
              `notes` = :notes,
              `vehicle_id` = :vehicle_id
              WHERE `part_id` = :part_id
          ');

        $statement->bindParam(':part_name', $this->part_name);
        $statement->bindParam(':date', $this->date);
        $statement->bindParam(':price', $this->price);
        $statement->bindParam(':manufacturer', $this->manufacturer);
        $statement->bindParam(':vendor', $this->vendor);
        $statement->bindParam(':notes', $this->notes);
        $statement->bindParam(':vehicle_id', $this->vehicle_id);
        $statement->bindParam(':part_id', $this->part_id);

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
            FROM `part`
            WHERE part_id = :part_id
        ');

        $statement->bindParam(':part_id', $this->part_id);
        return array(
            'success' => $statement->execute()
        );
    }


    /**
     * @return mixed
     */
    public function getPartName()
    {
        return $this->part_name;
    }

    /**
     * @param mixed $part_name
     */
    public function setPartName(string $part_name)
    {
        $this->part_name = $part_name;
    }

    /**
     * @return mixed
     */
    public function getPartNumber()
    {
        return $this->part_number;
    }

    /**
     * @param mixed $part_number
     */
    public function setPartNumber(string $part_number)
    {
        $this->part_number = $part_number;
    }

    /**
     * @return mixed
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param mixed $price
     */
    public function setPrice(string $price)
    {
        $this->price = $price;
    }

    /**
     * @return mixed
     */
    public function getManufacturer()
    {
        return $this->manufacturer;
    }

    /**
     * @param mixed $manufacturer
     */
    public function setManufacturer(string $manufacturer)
    {
        $this->manufacturer = $manufacturer;
    }

    /**
     * @return mixed
     */
    public function getVendor()
    {
        return $this->vendor;
    }

    /**
     * @param mixed $vendor
     */
    public function setVendor(string $vendor)
    {
        $this->vendor = $vendor;
    }

    /**
     * @return mixed
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * @param mixed $notes
     */
    public function setNotes(string $notes)
    {
        $this->notes = $notes;
    }
}