<?php 
/**
 * @author Antoine Pouch <ant-1@pouch.name>
 * @package gestionlite
 */

//Directory prefix for the Apache host ('/' for no prefix, and prefix must end with a /)
$matches = array();
preg_match('|(/gestionlite[^/]*/)|', $_SERVER['REQUEST_URI'], $matches);
define('APACHE_DIRECTORY_PREFIX', $matches[0]);

//Authentication user/pass
define('ADMIN_USERNAME', 'lagestion');
define('ADMIN_PASSWORD', 'cestnul');

//DB constants
define('DB_HOST', 'localhost');
define('DB_NAME', substr(APACHE_DIRECTORY_PREFIX, 1, -1));
define('DB_USER', 'gestionlite');
define('DB_PASS', 'lr9w3');

//Controller : default module and action
define('CONTROLLER_DEFAULT_MODULE', 'movement');
define('CONTROLLER_DEFAULT_ACTION', 'list');

//Controller : default module and action for mobile platforms
define('CONTROLLER_DEFAULT_MODULE_MOBILE', 'movement');
define('CONTROLLER_DEFAULT_ACTION_MOBILE', 'update');

//Default form mode for movement controller
define('CONTROLLER_MOVEMENT_DEFAUT_MODE', 'quick');

//The default view's layout
define('DEFAULT_LAYOUT', 'layout.php');

//How many movements per default to show in the "last for account" listing ?
define('LAST_MOVEMENTS_FOR_ACCOUNT_COUNT', 50);

//Graphs directory
define('GRAPHS_DIRECTORY', APACHE_DIRECTORY_PREFIX . 'graphs');
define('GRAPHS_DIRECTORY_ABSOLUTE', dirname(dirname(__FILE__)) . '/htdocs/graphs');

//Truetype fonts directory
define('TTF_DIR', dirname(dirname(__FILE__)) . '/truetype/');

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(dirname(__FILE__)) . '/lib');

?>
