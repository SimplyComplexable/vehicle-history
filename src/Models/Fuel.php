<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 12/5/2017
 * Time: 8:08 PM
 */

namespace VehicleHistory\Models;

class Fuel
{
    private $date;      // date of fill up
    private $odometer;  // total mileage on vehicle at fuel fill up
    private $distance;  // trip odometer when fueled up (how many miles on this tank)
    private $volume;    // how much fuel purchased (gals in US)
    private $cost;      // how much the fuel cost
    private $mpg;       // miles per gallon


    public function __construct($date, $odometer, $distance, $volume, $cost, $mpg = "")
    {
        $this->date = $date;
        $this->odometer = $odometer;
        $this->distance = $distance;
        $this->volume = $volume;
        $this->cost = $cost;
        $this->mpg = $mpg;
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