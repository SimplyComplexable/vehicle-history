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

    public function getAll($user_id) {
        return Vehicle::getAll($user_id);
    }

    public function addVehicle($user_id, array $data) {
        $vehicle = new Vehicle();
        foreach ($data as $key => $value) {
            $vehicle->set($key, $value);
        }

        return $vehicle->save($user_id);
    }

    public function updateVehicle($user_id, int $id, array $updates) {
        $vehicle = new Vehicle($id);
        foreach($updates as $key => $value) {
            $vehicle->set($key, $value);
        }
        return $vehicle->save($user_id);
    }

    public function deleteVehicle($user_id, int $id) {
        $vehicle = new Vehicle($id);
        return $vehicle->delete($user_id);
    }

    //override Component function which gets called before the page is rendered
    protected function beforeRender() {
        $userData = $this->getToken();
        //set view variables with this function
        //key will be the variables name
        $this->setVars(array(
            'vehicles' => Vehicle::getAll($userData['id']),
            'token' => $userData['token']
        ));
    }
}