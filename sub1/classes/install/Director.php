<?php 
    include_once("../../install/autoloader.php");
    //if ($_SERVER['REQUEST_METHOD'] == 'POST'){
        //Take in a version number and then run the installer for that version, returning any necessary forms or alerts.
        $versionNumber = (isset($_POST['version'])) ? $_POST['version'] : "2024.04.05";
        if($versionNumber){
            $filter = install\controller\Installer::REQUIRED_FOR_UPGRADE;
            echo json_encode($filter);
            exit;


        }
        
        
        $output = $_POST['version'];
        echo json_encode($output);
    //}
?>