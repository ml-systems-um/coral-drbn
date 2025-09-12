<?php 
    use install\controller as CORAL;
    include_once("../common/Autoloader.php");
    if ($_SERVER['REQUEST_METHOD'] == 'POST'){
        echo 'Post<br>';
        //Take in a version number and then run the installer for that version, returning any necessary forms or alerts.
        
        $versionNumber = (isset($_POST['version'])) ? $_POST['version'] : FALSE;
        if(!$versionNumber){
            //Error for trying to start the installer without a version number.
            return false;
        }
        
        $filter = CORAL\Installer::REQUIRED_FOR_UPGRADE;
        $installer = new CORAL\Installer($versionNumber);
        var_dump($installer); 
        try {
            echo 'Hi<br>';
            $installer = new CORAL\Installer($versionNumber);
            var_dump($installer);
        }
        catch (Exception $e) {
            var_dump($e);
            switch ($e->getCode()) {
                case CORAL\Installer::ERR_CANNOT_READ_PROVIDER_SCRIPT:
                    $yield = new stdClass();
                    $yield->messages = [ _("While trying to load module scripts an error occurred."), _("Please check that PHP has execute (probably 644) permission on your install folders.") ];
                    yield_test_results_and_exit($yield, [], 0);
                    break;
            }
        }
        echo '<pre>';
        print_r($installer);
        echo '</pre>';
        $requirements = $installer->getRequiredProviders($filter);
        echo json_encode($requirements);
    } else {echo 'No Post';}
?>