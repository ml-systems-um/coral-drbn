<?php
/**
 * @author: Cesar Hernandez
 * @author: Tim Streasick (revised for the common\LangCodes version)
 * getLanguage: This method return the name of the language according to the language code
 */
namespace common;
class LangCodes{
    private $languageCodes = [
        'de_DE' => [
            'code' => 'de_DE',
            'name' => 'Deutsch',
        ],
        'en_GB' => [
            'code' => 'en_GB',
            'name' => 'English (GB)',
        ],
        'en_US' => [
            'code' => 'en_US',
            'name' => 'English (US)',
        ],
        'es_ES' => [
            'code' => 'es_ES',
            'name' => 'Español',
        ],
        'fr_FR' => [
            'code' => 'fr_FR',
            'name' => 'Français',
        ],
        'tr_TR' => [
            'code' => 'tr_TR',
            'name' => 'Türkçe',
        ],
        'zh_CN' => [
            'code' => 'zh_CN',
            'name' => '中文 (简体)',
        ],
        'zh_TW' => [
            'code' => 'zh_TW',
            'name' => '中文 (正體)',
        ],
    ];

    public function getLanguage($code){
        $validLanguageCode = (array_key_exists($code, $this->languageCodes));
        $output = ($validLanguageCode) ? $this->languageCodes[$code]['code'] : null;
        return $output;
    }
    public function getNameLang($code_lang){
        $validLanguageCode = (array_key_exists($code_lang, $this->languageCodes));
        $output = ($validLanguageCode) ? $this->languageCodes[$code]['name'] : null;
        return $output;
    }

    public function getBrowserLanguage() {
        return str_replace('-', '_', substr($_SERVER["HTTP_ACCEPT_LANGUAGE"],0,5));
    }

    public function getLanguageSelector() {
        $route='locale';
        $languages[]="en_US"; // add default language
        if (is_dir($route)) {
            if ($dh = opendir($route)) {
                while (($file = readdir($dh)) !== false) {
                    if (is_dir("$route/$file") && $file!="." && $file!=".." && $this->getLanguage($file) != null){
                        $languages[]=$file;
                    }
                }
                closedir($dh);
            }
        }else {
            echo "<br>"._("Invalid translation route!");
        }
        // Sort ordered list
        sort($languages);

        // Get language of navigator and set the selected Language.
        $browserLanguage = $this->getBrowserLanguage();
        $selectedLanguage = ($_COOKIE['lang']) ?? $browserLanguage; 

        echo '<select name="lang" id="lang" class="dropDownLang" aria-label="'._('Language').'">';
        foreach($languages as $languageValue){
            $languageName = $this->getNameLang($languageValue);
            $loopLanguage = (isset($_COOKIE["lang"])) ? $languageValue : substr($languageValue,0,5);
            $selected = ($selectedLanguage == $loopLanguage) ? "selected" : "";
            echo "<option value='{$languageValue}' {$selected}>{$languageName}</option>";
        }
        echo '</select>';
    }
}
?>
