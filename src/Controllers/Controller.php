<?php

namespace VehicleHistory\Controllers;

use VehicleHistory\Models\Token;

class Controller {
// name of view file, not including extension (assumed to be .php)
    protected $viewFileName;
//view variables
    protected $variables = array();

    function __construct($viewFileName = null) {
        if ($viewFileName) {
            $this->viewFileName = $viewFileName;
        } else if ($this->viewFileName === null) {
            $this->viewFileName = $this->getViewFileName();
        }
    }

    /**
     * Get the assumed name of the view file based on normal conventions
     *
     * @return string Name of file parsed from the class name
     */
    private function getViewFileName() {
        return str_replace('Controller', '', get_class($this));
    }

    /**
     * Get the full path of a class, for dynamic loading, assuming normal conventions
     *
     * @param string $classType (e.g. Controller, Service, Model, etc.)
     * @param string $className
     * @return string full path to the class
     */
    protected function getClassPath($classType, $className) {
        return str_replace('Controllers', ucwords($classType) . 's', __DIR__) . DIRECTORY_SEPARATOR
            . ucwords($className) . ucwords($classType) . '.php';
    }

    /**
     * Overridable function called before view is rendered
     * Most Controller work should happen here
     */
    protected function beforeRender() {
    }

    /**
     * Overridable function called after view is rended
     * Can be used for cleanup
     */
    protected function afterRender() {
    }

    /**
     * push variables onto the $variables array which will be loaded into the view
     *
     * @param array $variablesArray array('variable_name' => variable_value)
     */
    protected function setVars($variablesArray) {
        foreach ($variablesArray as $name => $value) {
            $this->variables[$name] = $value;
        }
    }

    protected function getToken() {
        // !!! HACK DOES NOT WORK AT THE TOP OF THE FILE !!!
        require(__DIR__.'/../../config.php');
        if (!array_key_exists('token', $_GET) || !Token::getIDFromToken($_GET['token'])) {
            header('Location: https://icarus.cs.weber.edu'.$baseURI.'/login');
            return false;
        }

        return array(
            'token' => $_GET['token'],
            'id' => Token::getIDFromToken($_GET['token'])
        );
    }

    /**
     * Get the full path to the view file, assuming normal conventions
     *
     * @return string full path to the view file
     */
    public function getViewPath() {
        return str_replace('Controllers', 'View' . 's', __DIR__) . DIRECTORY_SEPARATOR .
            $this->viewFileName . '.php';
    }

    /**
     * Render view in the $viewFileName into HTML
     *
     * @return string Rendered HTML
     */
    public function renderView() {
        foreach ($this->variables as $name => $value) {
            $$name = $value;
        }
        $viewFile = $this->getViewPath();
        ob_start();
        include($viewFile);
        $html = ob_get_contents();
        ob_end_clean();
        return $html;
    }

    /**
     * Call lifecycle hooks and render html
     *
     * @return string rendered HTML
     */
    public function httpResponse() {
        $this->beforeRender();
        $html = $this->renderView();
        $this->afterRender();
        return $html;
    }
}