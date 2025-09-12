<?php 
    namespace install\controller;
    class versionManager {
        private $installerVersion = "2025.04.06";
        private $currentlyInstalledVersion = "";
        private $changeToBeMade = FALSE;
        private $needToUpgrade = FALSE;
        private $needToInstall = FALSE;
        private $validModules = [
            "auth", "licensing", "management", 
            "organizations", "reports", "resources", 
            "usage",
        ];
        private $validVersionOrder = [
            "1.9.0", 
            "2.0.0", 
            "3.0.0", "3.0.1", 
            "2020.02", "2020.09", 
            "2024.04", "2024.10", 
            "2025.04", "2025.04.01", "2025.04.02", "2025.04.03", "2025.04.04", "2025.04.05", "2025.04.06",
            "2025.10",
        ];
        public function __construct(){
            $this->setCurrentlyInstalledVersion();
            $this->validateVersions();
            $this->setInstalledSettings();
        }

        public function checkForChange(){
            $this->needToInstall = ($this->currentlyInstalledVersion == FALSE);
            $latestVersionToInstall = array_slice($this->validVersionOrder, -1)[0];
            $versionsDoNotMatch = ($this->currentlyInstalledVersion !== $latestVersionToInstall);
            $this->needToUpgrade = (!$this->needToInstall && $versionsDoNotMatch);
            $this->changeToBeMade = ($this->needToInstall || $this->needToUpgrade);
            return $this->changeToBeMade;
        }

        public function getVersionsToLoad(){
            if($this->needToInstall){
                return $this->getInstallVersions();
            }
            if($this->needToUpgrade){
                return $this->getUpgradeVersions();
            }
        }

        private function getInstallVersions(){
            $versionInstallArray['action'] = Installer::VERSION_STRING_INSTALL;
            $versionInstallArray['versions'] = $this->validVersionOrder;
            return $versionInstallArray;
        }

        private function getUpgradeVersions(){
            $versionUpgradeArray['action'] = Installer::VERSION_STRING_UPGRADE;
            $currentVersionIndex = array_search($this->currentlyInstalledVersion, $this->validVersionOrder);
            $firstUpgradeIndex = $currentVersionIndex + 1;
            $versionUpgradeArray['versions'] = array_slice($this->validVersionOrder, $firstUpgradeIndex);
            return $versionUpgradeArray;
        }

        private function setCurrentlyInstalledVersion(){
            try {
                $version = \common\Config::getInstallationVersion();
            } catch (\Exception $e) {
                $version = false;
            }
            $this->currentlyInstalledVersion = $version;
        }

        private function setInstalledSettings(){
            $this->needToInstall = ($this->currentlyInstalledVersion == FALSE);
            $versionsDoNotMatch = ($this->currentlyInstalledVersion !== $this->installerVersion);
            $this->needToUpgrade = (!$this->needToInstall && $versionsDoNotMatch);
            $this->changeToBeMade = ($this->needToInstall || $this->needToUpgrade);
        }

        private function validateInstalledVersion(){
            $currentVersion = ($this->currentlyInstalledVersion);
            $notInVersionArray = (!in_array($currentVersion, $this->validVersionOrder));
            $errorMsg = [
                "<b>" . _("An error has occurred:") . "</b><br />" . _("Sorry but the installer has been incorrectly configured. Please contact the developer."),
                _("The version currently installed is not a recognised version."),
                _("The version currently installed is: ")."<strong>{$currentVersion}</strong>",
            ];
            $output = ($currentVersion && $notInVersionArray) ? $errorMsg : FALSE;
            return $output;
        }

        private function validateConfigurationsExist(){
            $configurationFilesExist = array_reduce($this->validModules, function($carry, $module){
                $moduleExists = file_exists("{$module}/admin/configuration.ini");
                return ($carry || $moduleExists);
            });
            return $configurationFilesExist;
        }

        private function validateInstallProcess(){
            $moduleConfigsExist = $this->validateConfigurationsExist();
            if($moduleConfigsExist){
                //TODO Load PreInstallationCheck.
            }
        }

        private function validateVersions(){
            $errorChecks = [
                $this->validateInstalledVersion(),
            ];

            foreach($errorChecks as $testFailed){
                if($testFailed){
                    errorMessage::displayErrorAndStop($testFailed);
                }
            }
        }
    }
?>