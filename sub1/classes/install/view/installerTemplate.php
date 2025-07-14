<?php 
    namespace install\view;
    class installerTemplate {
        public $sectionTitle = "";
        public $messages = [];

        public function __construct(){
            $this->buildPage();
        }
        
        public function buildPage(){ ?>
            <div class="row main" style="opacity: 1; padding-right: 30px; padding-left: 0px;">
                <div class="two columns">&nbsp;</div>
                <div class="eight columns">
                    <h1 class="content-head is-center heading">
                        CORAL Installer
                    </h1>
                    <div class="installation_stuff">
                        <h2 id="section-title" class="section-title">
                            <?php echo $this->sectionTitle; ?>
                        </h2>
                        <div class="messages">
                            <?php foreach($this->messages as $msg){
                                echo "<div class='message'>{$msg}</div>";
                            } ?>
                        </div>
                        <div class="mainbody">
                            <?php $this->buildMainBody(); ?>
                        </div>
                    </div>
                    <div class="redirection">
                        <div class="row">
                            <div class="three columns">
                                <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                                    <circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none"/>
                                    <path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
                                </svg>
                            </div>
                            <div class="nine columns">
                                <div class="completion_title"></div>
                                <p class="completion_message"></p>
                                <p>
                                    <span class="redirection_message"></span><span class="countdown"></span>
                                </p>
                                <ul class="completed_test_holder">
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="two columns">&nbsp;</div>
            </div>
            <div class="percentageComplete"></div>
        <?php }

        protected function buildMainBody(){}
    }
?>