<?php

/*
**************************************************************************************************************************
** CORAL Universal Directory
**
** Copyright (c) 2010 University of Notre Dame
**
** This file is part of CORAL.
**
** CORAL is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
**
** CORAL is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
**
** You should have received a copy of the GNU General Public License along with CORAL.  If not, see <http://www.gnu.org/licenses/>.
**
**************************************************************************************************************************
*/

// Increase memory due to large sized reports
ini_set('max_execution_time', 1000);
ini_set("default_socket_timeout", 1000);
ini_set('memory_limit', '256M');

define('CLASSES_DIR', BASE_DIR . "../classes/");
define('ADMIN_DIR', BASE_DIR . 'admin/');
define('INTERFACES_DIR', ADMIN_DIR . 'interfaces/');
// Automatically load undefined classes from subdirectories of |CLASSES_DIR|.
class Autoloader{
    public static function register(){
        spl_autoload_register(function ($class){
            $file = str_replace('\\', DIRECTORY_SEPARATOR, $class).'.php';
            $fileLocation = CLASSES_DIR.$file;
            if(file_exists($fileLocation)){
                require $fileLocation;
                return true;
            } else {return false;}
        });
    }
}
Autoloader::register();

// Include file of language codes
$langCodes = new common\LangCodes();

// Verify the language of the browser
$language = $langCodes->setGlobalLanguage();
$GLOBALS['http_lang'] = $language;
putenv("LC_ALL={$language}");
setlocale(LC_ALL, "{$language}.utf8");
bindtextdomain("messages", BASE_DIR."locale");
textdomain("messages");

//** Functions for backwards (and forward) compatability in PHP **//

// Add lcfirst() for PHP < 5.3.0
if (false === function_exists('lcfirst')) {
	function lcfirst($string) {
		return strtolower(substr($string, 0, 1)) . substr($string, 1);
	}
}

//fix default timezone for PHP > 5.3
if(function_exists("date_default_timezone_set") and function_exists("date_default_timezone_get")){
	@date_default_timezone_set(@date_default_timezone_get());
}

function return_date_format() {
    $config = new Configuration();
    $config_date_format = $config->settings->date_format;
    if (isset($config_date_format) && $config_date_format != '') {
        $date_format = $config_date_format;
    } else {
        $date_format = "%m/%d/%Y";
    }
    return $date_format;
}

function return_datepicker_date_format() {
    $config = new Configuration();
    $config_date_format = $config->settings->datepicker_date_format;
    if (isset($config_date_format) && $config_date_format != '') {
        $date_format = $config_date_format;
    } else {
        $date_format = "mm/dd/yyyy";
    }
    return $date_format;
}

function return_sql_locale() {
    $config = new Configuration();
    $config_number_locale = $config->settings->number_locale;
    if (isset($config_number_locale) && $config_number_locale != '') {
        $number_locale = "'$config_number_locale'";
    } else {
        // If not set, setting to null will use MySQL/MariaDB default
        $number_locale = "NULL";
    }
    return $number_locale;
}

function return_number_locale() {
    $config = new Configuration();
    $config_number_locale = $config->settings->number_locale;
    if (isset($config_number_locale) && $config_number_locale != '') {
        $number_locale = $config_number_locale;
    } else {
        $number_locale = 'en_US';
    }
    return $number_locale;
}

function return_number_decimals() {
    $config = new Configuration();
    $config_number_decimals = $config->settings->number_decimals;
    if (isset($config_number_decimals) && $config_number_decimals != '') {
        $number_decimals = $config_number_decimals;
    } else {
        $number_decimals = 2;
    }
    return $number_decimals;
}

function format_date($mysqlDate) {

	//see http://php.net/manual/en/function.date.php for options

	//upper case Y = four digit year
	//lower case y = two digit year
	//make sure digit years matches for both directory.php and common.js

	//SUGGESTED: "m/d/Y" or "d-m-Y"
    return strftime(return_date_format(), strtotime($mysqlDate));

}

function create_php_date_format_from_js_format($input_string) {

  // Note the js format strings are specific to the datepicker plugin
  $js_formats = array('/yyyy/','/yy/','/mmmm/','/mmm/','/mm/','/dd/');
  // php format strings https://www.php.net/manual/en/function.date.php
  $php_formats = array('Y','y','F','M','m','d');

  return preg_replace($js_formats, $php_formats, $input_string);
}

function create_date_from_js_format($input) {
  /*
   * see https://andy-carter.com/blog/php-date-formats for overview of different php date formatters
   * Coral utilizes strftime() and strtotime(), but strtotime expects dates to be formatted in US English
   *
   * Thus, while the above format_date() function works for mysql dates (which are stored as YYYY/MM/DD HH:MM:SS,
   * it does not work for formatting form input dates, which are formatted via the datepiccker_date_format in common/configuration.ini
   *
   * E.g. a UK date of 26/11/2019 will return an error when using strtotime
   *
   * This function turns an input date into php Date object, which can then be utilized by strftime()
   *
   * To do so, it must convert datepicker format strings (e.g. 'dd' & 'mm') into php date format strings (e.g. 'd', 'm')
   * using the create_php_date_format_from_js_format() function above
   */

  $datepicker_format = return_datepicker_date_format();
  $php_format = create_php_date_format_from_js_format($datepicker_format);
  return date_create_from_format($php_format, $input);
}

function getTarget() {
    $config = new Configuration();
    return ($config->settings->open_new_windows == "Y") ? "target='_blank'" : "";
}

function debug($value) {
    echo '<pre>'.print_r($value, true).'</pre>';
}

function uploadErrorMessage($code) {
    switch ($code) {
        case UPLOAD_ERR_INI_SIZE:
            $message = _("The uploaded file exceeds the upload_max_filesize directive in php.ini");
            break;
        case UPLOAD_ERR_FORM_SIZE:
            $message = _("The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form");
            break;
        case UPLOAD_ERR_PARTIAL:
            $message = _("The uploaded file was only partially uploaded");
            break;
        case UPLOAD_ERR_NO_FILE:
            $message = _("No file was uploaded");
            break;
        case UPLOAD_ERR_NO_TMP_DIR:
            $message = _("Missing a temporary folder");
            break;
        case UPLOAD_ERR_CANT_WRITE:
            $message = _("Failed to write file to disk");
            break;
        case UPLOAD_ERR_EXTENSION:
            $message = _("File upload stopped by extension");
            break;
        default:
            $message = _("Unknown upload error");
            break;
    }
    return $message;
}
?>
