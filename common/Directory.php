<?php 
    //Define the CORAL_DIR. MODULE_DIR comes out of the directory file in the module.
    define('CORAL_DIR', MODULE_DIR.'..');

    // Increase memory due to large sized reports
    ini_set('max_execution_time', 1000);
    ini_set("default_socket_timeout", 1000);
    ini_set('memory_limit', '256M');

    require_once('Autoloader.php');
    require_once('Common_functions.php');
    require_once('Languages.php');
?>