<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 12/5/2017
 * Time: 8:14 PM
 */

namespace VehicleHistory\Models;

class Service
{
    private $date;      // date of service
    private $service;   // short service description
    private $odometer;  // total mileage on vehicle at time of service
    private $cost;      // total cost of service
    private $location;  // place where service was performed


    public function __construct($date, $service, $odometer, $cost, $location = "")
    {
        $this->date = $date;
        $this->service = $service;
        $this->odometer = $odometer;
        $this->cost = $cost;
        $this->location = $location;
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