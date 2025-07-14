<?php 
/**
 * Simple autoloader, so we don't need Composer just for this.
 */
class Autoloader
{
    public static function register()
    {
        spl_autoload_register(function ($class) {
            //var_dump($class);
            //echo '<br>';
            $file = "classes\\".str_replace('\\', DIRECTORY_SEPARATOR, $class).'.php';
            //var_dump($file);
            //echo '<hr>';
            if (file_exists($file)) {
                require $file;
                return true;
            }
            return false;
        });
    }
}
Autoloader::register();
?>