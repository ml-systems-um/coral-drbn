<?php 
    class potentialInvalidInstallation(){
        public function __construct(){ ?>
        <form class="pure-form pure-form-aligned">
            <div class="row">

            </div>
            <div class="row">
                &nbsp;
            </div>
            <div class="row">
                <input type="hidden" name="namespace_option_button" id="namespace_option_button" value="false" />
            </div>
            <div class="row">
                <input class="u-full-width" type="submit" value="" onclick="setHiddenInput()"/>
            </div>
            <div class="row">

            </div>
        </form>

        <?php }
    }

?>