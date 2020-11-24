<?php

/*
 * Base Controller
 * Loads modles and views
 */

class Controller
{

    public function getModelDefinition($modelName)
    {
        $modelFilePath = ROOT_MODEL_PATH . $modelName . '.php';
        // Require the model file
        require_once $modelFilePath;
        // Instantiate the model and return it
        return new $modelName;
    }

    public function getViewDefinition($viewName, $prependWithControllerName = false, $data = [])
    {
        $viewFilePath = $prependWithControllerName
            ? ROOT_VIEW_PATH . get_class($this) . '/' . $viewName . '.php'
            : ROOT_VIEW_PATH . $viewName . '.php';

        file_exists($viewFilePath)
            ? $this->requireViewFiles($viewFilePath, $data)
            : die('View does no exist');
    }

    private function requireViewFiles($viewFilePath, $data)
    {
        require_once HEADER_PATH;
        require_once $viewFilePath;
        require_once FOOTER_PATH;
    }
}