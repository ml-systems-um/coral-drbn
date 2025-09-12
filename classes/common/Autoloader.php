<?php 
/**
 * Simple autoloader, so we don't need Composer just for this.
 */
namespace common;
class Autoloader
{
    public static function register()
    {
        spl_autoload_register(function ($class) {
            include_once('Pathing.php');
            $pathing = new Pathing;
            $docRoot = $pathing->CoralRoot;
            $filePath = "{$docRoot}{$class}.php";
            $fileExists = file_exists($filePath);
            if($fileExists){require $filePath;}
            return $fileExists;
        });
    }
}
Autoloader::register();
?>