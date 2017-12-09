<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 12/5/2017
 * Time: 1:55 PM
 */

namespace VehicleHistory\Controllers;

use VehicleHistory\Models\Vehicle;

class VehicleController extends Controller
{
    protected $viewFileName = 'Vehicles';

    public function addVehicle($data) {
        $vehicle = new Vehicle();
        foreach ($data as $key => $value) {
            $vehicle->set($key, $value);
        }
        $vehicle->save();
    }

    public function updateVehicle($id, $updates) {
        $vehicle = new Vehicle($id);
        foreach($updates as $key => $value) {
            $vehicle->set($key, $value);
        }
        $vehicle->save();
    }

    public function deleteVehicle($id) {
        $vehicle = new Vehicle($id);
        $vehicle->delete();
    }

    //override Component function which gets called before the page is rendered
    protected function beforeRender() {
        //set view variables with this function
        //key will be the variables name
        $this->setVars(array(
            'vehicles' => Vehicle::getAll()
        ));
    }
}