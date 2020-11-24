<?php

// Config
require_once 'config/config.php';

// Autoload Core Libraries
// This method will call for every file in the libraries folder that have
// class names that match the file names
// For this autoloader to work, the filenames MUST match the class name defined in each file
spl_autoload_register(function ($className) {
    $fileToRequire = 'libraries/' . $className . '.php';
    require_once $fileToRequire;
});



