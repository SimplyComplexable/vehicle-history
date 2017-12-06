<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 12/5/2017
 * Time: 8:22 PM
 */

namespace VehicleHistory\Models;

class Part
{
    private $part_name;
    private $part_number;
    private $price;
    private $manufacturer;  // who makes the part
    private $vendor;        // who the part was purchased from
    private $notes;         // any notes about the part


    public function __construct($part_name, $part_number, $price, $manufacturer, $vendor, $notes = "")
    {
        $this->part_name = $part_name;
        $this->part_number = $part_number;
        $this->price = $price;
        $this->manufacturer = $manufacturer;
        $this->vendor = $vendor;
        $this->notes = $notes;
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