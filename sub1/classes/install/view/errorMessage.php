<?php 
    namespace install\view;
    class errorMessage extends installerTemplate {
        public function __construct($messageArray){
            $this->sectionTitle = _("Installation Error");
            $this->messages = $messageArray;
            $this->buildPage();
        }
    }
?>