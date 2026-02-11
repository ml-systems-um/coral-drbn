<?php 

    // Include file of language codes
    $lang_name = new common\LangCodes();

    // Verify the language of the browser
    $GLOBALS['http_lang'] = NULL;

    if(isset($_COOKIE["lang"])) {
        $GLOBALS['http_lang'] = $_COOKIE["lang"];
    } else {
        $codeL = str_replace("-","_",substr($_SERVER["HTTP_ACCEPT_LANGUAGE"],0,5));
        $GLOBALS['http_lang'] = $lang_name->getLanguage($codeL);

        if($GLOBALS['http_lang'] == "") {
            $GLOBALS['http_lang'] = "en_US";
        }
    }

    putenv("LC_ALL={$GLOBALS['http_lang']}");
    setlocale(LC_ALL, "{$GLOBALS['http_lang']}.utf8");
    bindtextdomain("messages", MODULE_DIR."locale");
    textdomain("messages");

?>