<?php
// DATABASE
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASSWORD', 'root');
define('DB_NAME', 'mvc');

// PATHS
define('ROOT_APP_PATH', dirname(__FILE__, 2));
define('ROOT_CONTROLLER_PATH', ROOT_APP_PATH . '/controllers/');
define('ROOT_MODEL_PATH', ROOT_APP_PATH . '/models/');
define('ROOT_VIEW_PATH', ROOT_APP_PATH . '/views/');

// GENERAL
define('ROOT_URL', '/');
define('SITE_NAME', 'GalaxyMVC');

define('HEADER_PATH', ROOT_APP_PATH . '/views/inc/header.php');
define('FOOTER_PATH', ROOT_APP_PATH . '/views/inc/footer.php');
