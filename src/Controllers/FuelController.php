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

    public function getAll($user_id) {
        return Fuel::getAllForVehicle($this->vehicle_id);
    }

    public function addFuel($user_id, array $data) {
        $fuel = new Fuel();
        foreach ($data as $key => $value) {
            $fuel->set($key, $value);
        }

        return $fuel->save();
    }

    public function updateFuel($user_id, int $id, array $updates) {
        $fuel = new Fuel($id);
        foreach($updates as $key => $value) {
            $fuel->set($key, $value);
        }
        return $fuel->save();
    }

    public function deleteFuel($user_id, int $id) {
        $fuel = new Fuel($id);
        return $fuel->delete();
    }

    protected function beforeRender() {
        $token = $this->getToken();

        $vehicle = new Vehicle($this->vehicle_id);
        $this->setVars(array(
            'vehicle_id' => $this->vehicle_id,
            'vehicle_title' => $vehicle->getModelYear() . ' ' . $vehicle->getMake() . ' ' . $vehicle->getModel()
        ));
    }
}