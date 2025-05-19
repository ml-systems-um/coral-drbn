<?php
/**
 * @author: Cesar Hernandez
 * getLanguage: This method return the name of the language according to the language code
 */
namespace common;
class LangCodes {
    private $langCodeArray = [
            'de_DE'=>'de_DE',
            'en_GB'=>'en_GB',
            'en_US'=>'en_US',
            'es_ES'=> 'es_ES',
            'fr_FR'=>'fr_FR',
            'tr_TR'=>'tr_TR',
            'zh_CN'=>'zh_CN',
            'zh_TW'=>'zh_TW'
    ];

    private $langNameArray = [
            'de_DE'=>'Deutsch',
            'en_GB'=>'English (GB)',
            'en_US'=>'English (US)',
            'es_ES'=> 'Español',
            'fr_FR'=>'Français',
            'tr_TR'=>'Türkçe',
            'zh_CN'=>'中文 (简体)',
            'zh_TW'=>'中文 (正體)'
    ];

    private $defaultLangCode = 'en_US';

    //Check to see if the language Code exists; if it does, return it. Otherwise return NULL.
    public function getLanguage($code){
        $codeValue = ($this->langCodeArray[$code]) ?? NULL;
        return $codeValue;
    }

    public function getBrowserLanguage() {
        return str_replace('-', '_', substr($_SERVER["HTTP_ACCEPT_LANGUAGE"],0,5));
    }

    //Check to see if the language code has a name value; if it does, return it. Otherwise return NULL.
    private function getNameLang($code){
        $nameValue = ($this->langNameArray[$code]) ?? NULL;
        return $nameValue;
    }

    //Create and return a list of language codes that exist.
    private function getLanguageArray(){
        $route = "locale"; //Route to the language files.
        $outputLanguages = []; //Empty array to hold all the language codes.
        $outputLanguages[] = $this->defaultLangCode; //Always add a default language.
        $linkValues = [".", ".."];

        $validDirectory = is_dir($route);
        if($validDirectory){
            if($dh = opendir($route)){
                //Loop through the directory and read file directories.
                while (($file = readdir($dh)) !== false) {
                    $directoryExists = (is_dir("{$route}/{$file}"));
                    $fileIsNotALink = (!in_array($file, $linkValues));
                    $fileIsALanguage = ($this->getLanguage($file) !== NULL);

                    if ($directoryExists && $fileIsNotALink && $fileIsALanguage){
                        $outputLanguages[] = $file;
                    }
                }
            }
        } else {echo "<br>"._("Invalid translation route!");}
        return $outputLanguages;
    }

    public function getLanguageSelector() {
        // Get language of navigator
        $browserLang = $this->getBrowserLanguage();

        //Get a list of Language codes that have file directories.
        $langCodeList = $this->getLanguageArray();

        // Show an ordered list
        sort($langCodeList);

        //Get the language cookie, or set it to false if it doesn't exist.
        $langCookie = ($_COOKIE['lang']) ?? FALSE;
        
        echo "<select name='lang' id='lang' class='dropDownLang' aria-label='"._('Language')."'>";
            foreach($langCodeLost as $langCode){
                //Is this the selected language?
                if($langCookie){ //We can compare it to the cookie.
                    $selected = ($langCode == $langCookie) ? "selected" : "";
                } else { //We need to compare a substring to the browser Language.
                    $selected = (substr($langCode,0,5) == $browserLang) ? "selected" : "";
                }
                $languageName = $this->getNameLang($langCode);
                echo "<option value='{$langCode}' {$selected}>{$languageName}</option>";
            }
        echo "</select>";
    }

    public function setGlobalLanguage(){
        //Reset the output.
        $output = NULL;
        $langCookie = ($_COOKIE['lang']) ?? FALSE;
        $browserCode = $this->getBrowserLanguage();
        $emptyBrowser = ($browserCode == "");
        $browserReturn = ($emptyBrowser) ? $this->defaultLanguage : $browserCode;

        $output = ($langCookie) ? $langCookie : $browserReturn;
        return $output;
    }
}
?>