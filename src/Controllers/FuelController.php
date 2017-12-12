<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 12/5/2017
 * Time: 1:54 PM
 */

namespace VehicleHistory\Controllers;

use VehicleHistory\Models\Fuel;
use VehicleHistory\Models\Vehicle;

class FuelController extends Controller
{
    private $vehicle_id;
    protected $viewFileName = 'FuelLog';

    public function __construct($id) {
        parent::__construct();
        $this->vehicle_id = $id;
    }

    public function addFuel(array $data) {
        $fuel = new Fuel();
        foreach ($data as $key => $value) {
            $fuel->set($key, $value);
        }

        return $fuel->save();
    }

    public function updateFuel(int $id, array $updates) {
        $fuel = new Fuel($id);
        foreach($updates as $key => $value) {
            $fuel->set($key, $value);
        }
        return $fuel->save();
    }

    public function deleteFuel(int $id) {
        $fuel = new Fuel($id);
        return $fuel->delete();
    }

    protected function beforeRender() {
        $vehicle = new Vehicle($this->vehicle_id);
        $this->setVars(array(
            'vehicle_id' => $this->vehicle_id,
            'vehicle_title' => $vehicle->getModelYear() . ' ' . $vehicle->getMake() . ' ' . $vehicle->getModel()
        ));
    }

    public function getAll() {
        return Fuel::getAllForVehicle($this->vehicle_id);
    }
}