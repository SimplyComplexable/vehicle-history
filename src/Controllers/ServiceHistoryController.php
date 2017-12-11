<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 12/5/2017
 * Time: 1:55 PM
 */

namespace VehicleHistory\Controllers;

use VehicleHistory\Models\Service;

class ServiceHistoryController extends Controller
{
    private $vehicle_id;
    protected $viewFileName = 'ServiceHistory';

    public function __construct($id) {
        parent::__construct();
        $this->vehicle_id = $id;
    }

    protected function beforeRender() {
        $this->setVars(array(
            'vehicle_id' => $this->vehicle_id
        ));
    }

    public function getAll() {
        return Service::getAllForVehicle($this->vehicle_id);
    }
}