<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 12/5/2017
 * Time: 1:54 PM
 */

namespace VehicleHistory\Controllers;

use VehicleHistory\Models\Part;
use VehicleHistory\Models\Vehicle;

class PartsController extends Controller
{
    private $vehicle_id;
    protected $viewFileName = 'Parts';


    public function __construct($id) {
        parent::__construct();
        $this->vehicle_id = $id;
    }

    public function addPart(array $data) {
        $part = new Part();
        foreach ($data as $key => $value) {
            $part->set($key, $value);
        }

        return $part->save();
    }

    public function updatePart(int $id, array $updates) {
        $part = new Part($id);
        foreach($updates as $key => $value) {
            $part->set($key, $value);
        }
        return $part->save();
    }

    public function deletePart(int $id) {
        $part = new Part($id);
        return $part->delete();
    }

    protected function beforeRender() {
        $token = $this->getToken();

        $vehicle = new Vehicle($this->vehicle_id);
        $this->setVars(array(
            'vehicle_id' => $this->vehicle_id,
            'vehicle_title' => $vehicle->getModelYear() . ' ' . $vehicle->getMake() . ' ' . $vehicle->getModel()
        ));
    }

    public function getAll() {
        return Part::getAllForVehicle($this->vehicle_id);
    }
}