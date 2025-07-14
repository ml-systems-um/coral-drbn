<?php 
    namespace install\view;
    class preInstallationCheck extends installerTemplate {
        public function __construct(){
            $this->sectionTitle = _("CORAL Pre-Installation Check");
            $this->messages = [
                _("We cannot tell whether or not CORAL is installed. Either it is not installed or it was installed using another installer."),
                _("If CORAL is already installed you should <b>NOT</b> try to install."),
            ];
            $this->buildPage();
        }

        protected function buildMainBody(){ ?>
            <form class="pure-form pure-form-aligned">
                <p class="instructions">
                    <?php echo _("Please choose one of the options below:"); ?>
                </p>
                <input type="hidden" name='installation_root_option_button' id='installation_root_option_button' value=false />
                <input  class="u-full-width" 
                        type='submit' 
                        value='<?php echo _("Install CORAL"); ?>' 
                        onclick="$('#installation_root_option_button').val('install_anyway');return false;"; 
                />
                <input  class="u-full-width" 
                        type='submit' 
                        value='<?php echo _("CORAL Already Installed (Upgrade/Repair)"); ?>' 
                        onclick="$('#installation_root_option_button').val('already_installed');return false;"; 
                />
            </form>
        <?php }
    }


?>