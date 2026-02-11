<?php 
    class Autoloader{
        public static function register(){
            spl_autoload_register(function ($class){
                //$classString = "resources\controllers\ResourceForm";
                $debugClass = ($classString) ?? FALSE;
                $debug = ($debugClass == $class);
                $classPath = CORAL_DIR . "\\classes\\{$class}";
                $file = str_replace('\\', DIRECTORY_SEPARATOR, $classPath).'.php';
                if($debug){
                    var_dump($file);
                    echo '<pre>';
                    var_dump(file_exists($file));
                    echo '</prE>';
                }
                if(file_exists($file)){
                    require_once($file);
                    return true;
                } else {return false;}
            });
        }
    }
    Autoloader::register();
?>