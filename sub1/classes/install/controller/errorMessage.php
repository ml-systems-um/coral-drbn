<?php 
    namespace install\controller;
    class errorMessage {
         public static function displayErrorAndStop($errorMsg){
            $invalidErrorMessage = [
                "<b>" . _("An error has occurred:") . "</b><br />" . _("Invalid Error Message passed to Installer. Please report this to developers.")
            ];
            $validMessageArray = is_array($errorMsg);
            $errorMessageView = ($validMessageArray) ? $errorMsg : $invalidErrorMessage;
            new \install\view\errorMessage($errorMessageView);
            exit;
        }
    }


?>