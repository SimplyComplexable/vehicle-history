<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 12/5/2017
 * Time: 1:55 PM
 */

namespace VehicleHistory\Controllers;

use VehicleHistory\Models\Service;
use VehicleHistory\Models\Vehicle;

class ServiceHistoryController extends Controller
{
    private $vehicle_id;
    protected $viewFileName = 'ServiceHistory';

    public function __construct($id) {
        parent::__construct();
        $this->vehicle_id = $id;
    }

    public function getAll() {
        return Service::getAllForVehicle($this->vehicle_id);
    }

    public function addService(array $data) {
        $vehicle = new Service();
        foreach ($data as $key => $value) {
            $vehicle->set($key, $value);
        }

        return $vehicle->save();
    }

    public function updateService(int $id, array $updates) {
        $service = new Service($id);
        foreach($updates as $key => $value) {
            $service->set($key, $value);
        }
        return $service->save();
    }

    public function deleteService(int $id) {
        $service = new Service($id);
        return $service->delete();
    }

    protected function beforeRender() {
        $token = $this->getToken();

        $service = new Vehicle($this->vehicle_id);
        $this->setVars(array(
            'vehicle_id' => $this->vehicle_id,
            'vehicle_title' => $service->getModelYear() . ' ' . $service->getMake() . ' ' . $service->getModel()
        ));
    }
}