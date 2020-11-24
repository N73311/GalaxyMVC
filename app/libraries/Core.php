<?php

/*
 * Main Application Core Class
 * Creates URL & loads the core controller
 * URL FORMAT - /[controller]/[method]/[params]
 */

class Core
{
    // If no other controllers are supplied, load the 'Pages' controller
    // By Default: the method to call is 'index' and there are no params
    protected $currentUrlContents = [];
    protected $currentController = 'Home';
    protected $currentMethod = 'index';
    protected $currentParams = [];

    public function __construct()
    {
        // Set the default current URL array
        $this->currentUrlContents = [$this->currentController, $this->currentMethod, []];

        // Set the variable url equal to the getCurrentUrl function
        $this->setCurrentUrlFromLocation();

        // Resolve the controller listed in the URL
        $this->setCurrentControllerFromUrl();

        // Resolve the method to call in the controller
        $this->setCurrentMethodFromUrl();

        // Resolve any parameters contained in the url
        $this->setCurrentParamsFromUrl();

        // Call the controller method
        call_user_func_array([$this->currentController, $this->currentMethod], [$this->currentParams]);
    }

    private function setCurrentUrlFromLocation()
    {
        if (isset($_GET['url'])) {
            // Strip the ending slash if exists
            $url = rtrim($_GET['url'], '/');

            // Sanitize the url - remove any chars a url should not have
            $url = filter_var($url, FILTER_SANITIZE_URL);

            // Break the location url into an array, break at the '/' char
            $url = explode('/', $url);

            $this->currentUrlContents = $url;
        }
    }

    private function setCurrentControllerFromUrl()
    {
        // Controller
        $controllerName = $this->currentUrlContents[0];
        $capitalizedControllerName = ucwords($controllerName);
        $controllerFileName = $capitalizedControllerName . '.php';
        $controllerFilePath = ROOT_CONTROLLER_PATH . $controllerFileName;
        // Look in the controllers for the controller specified in the url
        if (file_exists($controllerFilePath)) {
            // Set the controller as the currentController
            $this->currentController = $capitalizedControllerName;
        }

        // Require the controller file
        $currentControllerFilePath = ROOT_CONTROLLER_PATH . $this->currentController . '.php';
        require_once $currentControllerFilePath;

        // Instantiate the controller
        $this->currentController = new $this->currentController;
    }

    private function setCurrentMethodFromUrl()
    {
        // check if a method exists in the url
        if (isset($this->currentUrlContents[1])) {

            $methodName = $this->currentUrlContents[1];

            // Check to see if the method exists in the controller
            if (method_exists($this->currentController, $methodName)) {
                $this->currentMethod = $methodName;
            }
        }
    }

    private function setCurrentParamsFromUrl()
    {
        if (isset($this->currentUrlContents[2])) {
            $this->currentParams = $this->currentUrlContents[2];
        }
    }
}