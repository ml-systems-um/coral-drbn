<?php 
namespace common;
class Pathing
{
    public $CoralRoot = "";
    public function __construct() {
        /* CORAL may be in a subdirectory, or it may be in root. The follow code is a janky way to account for that when setting up the autoloader. 
            A wonderful contribution opportunity for anyone who's reading this would be to try and fix things up. Not only do you need to ensure the 
            document root is accurate (though dynamic), you also want to account for the differences in slashes used for pathing. */
        $usesBackslash = (DIRECTORY_SEPARATOR == '\\');
        $autoloaderFolder = basename(dirname(__FILE__));
        $dirname_dir = (__DIR__);
        $docRoot = $_SERVER['DOCUMENT_ROOT'];
        if($usesBackslash){
            $autoloaderFolder = str_replace("\\", "/", $autoloaderFolder);
            $dirname_dir = str_replace("\\", "/", $dirname_dir);
            $docRoot = str_replace("\\", "/", $docRoot);
        }
        $quotedRegex = preg_quote($docRoot, "/");
        $regexPhrase = "/^{$quotedRegex}/";
        $subDirectories = preg_replace($regexPhrase, "", $dirname_dir);
        $subDirectoryRoot = str_replace($autoloaderFolder, "", $subDirectories);
        $newDocRoot = "{$docRoot}{$subDirectoryRoot}";
        $this->CoralRoot = $newDocRoot;
    }
}
?>