<?php 
/**
 * Simple autoloader, so we don't need Composer just for this.
 */
class Autoloader
{
    public static function register()
    {
        spl_autoload_register(function ($class) {
            $usesBackslash = (DIRECTORY_SEPARATOR == '\\');
            $dirname_dir = str_replace("\\", DIRECTORY_SEPARATOR, (__DIR__));
            $root = $_SERVER['DOCUMENT_ROOT'];
            var_dump($dirname_dir);
            echo '<br>';
            var_dump($root);
            $newRoot = str_replace("\\", DIRECTORY_SEPARATOR, $root);
            echo '<br>';
            var_dump($newRoot);
            echo '<br>';
            var_dump(DIRECTORY_SEPARATOR);
            echo '<br>';
            $newnewRoot = preg_replace('/^' . preg_quote($newRoot, '/') . '/', '', $dirname_dir);
            var_dump($newnewRoot);
            exit;


            echo Autoloader::$testValue;
            var_dump($class);
            echo '<br>';
            $file = $_SERVER['DOCUMENT_ROOT']."\\classes\\".str_replace('\\', DIRECTORY_SEPARATOR, $class).'.php';
            var_dump($file);
            echo '<br>';
            var_dump(file_exists($file));
            echo '<hr>';
            if (file_exists($file)) {
                echo 'Good to go<br>';
                require $file;
                return true;
            }
            return false;
        });
    }

    private function correctSlashes($filePath){
        return str_replace("\\", DIRECTORY_SEPARATOR, $filePath);
    }
}
Autoloader::register();
?>