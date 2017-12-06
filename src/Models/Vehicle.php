<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 12/5/2017
 * Time: 1:39 PM
 */

namespace VehicleHistory\Models;

class Vehicle
{
    private $model_year;
    private $make;
    private $model;
    private $color;
    private $licenseplatenumber;
    private $vin;


    public function __construct($model_year, $make, $model, $color = "", $licenseplatenumber = "", $vin = "")
    {
        $this->model_year = $model_year;
        $this->make = $make;
        $this->model = $model;
        $this->color = $color;
        $this->licenseplatenumber = $licenseplatenumber;
        $this->vin = $vin;
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
        return $this->licenseplatenumber;
    }

    /**
     * @param mixed $licenseplatenumber
     */
    public function setLicenseplatenumber(string $licenseplatenumber)
    {
        $this->licenseplatenumber = $licenseplatenumber;
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