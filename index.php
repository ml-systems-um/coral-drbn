<?php
	session_start();
	// "install/index.php" will check if CORAL is installed and version is current
	require_once("install/index.php");

	// Include file of language codes
	include_once 'LangCodes.php';
	$lang_name = new LangCodes();

	// Verify the language of the browser
	global $http_lang;
	if(isset($_COOKIE["lang"])){
		$http_lang = $_COOKIE["lang"];
	}else{
		$codeL = $lang_name->getBrowserLanguage();
		$http_lang = $lang_name->getLanguage($codeL);
		if($http_lang == "")
		  $http_lang = "en_US";
	}
	putenv("LC_ALL=$http_lang");
	setlocale(LC_ALL, $http_lang.".utf8");
	bindtextdomain("messages", dirname(__FILE__) . "/locale");
	textdomain("messages");
?>

<!DOCTYPE html>
<html lang="<?php echo str_replace('_', '-', $http_lang); ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="CACHE-CONTROL" CONTENT="public">

	<title><?php echo _("eRM - eResource Management"); ?></title>
	
	<link rel="icon" href="<?php echo $coralPath; ?>images/favicon.ico">
  <link rel="icon" href="<?php echo $coralPath; ?>images/favicon.svg" type="image/svg+xml">
  <link rel="stylesheet" href="<?php echo $coralPath; ?>css/style.css" type="text/css" media="screen" />
  <link rel="stylesheet" href="css/indexstyle.css" type="text/css" media="screen" />
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
	<script src="js/plugins/Gettext.js"></script>
	<script src="js/common.js"></script>

	<?php
		// Add translation for the JavaScript files
		global $http_lang;
		$str = substr($_SERVER["HTTP_ACCEPT_LANGUAGE"],0,5);
		$default_l = $lang_name->getLanguage($str);
		if($default_l==null || empty($default_l)){$default_l=$str;}
		if(isset($_COOKIE["lang"])){
			if($_COOKIE["lang"]==$http_lang && $_COOKIE["lang"] != "en_US"){
				echo "<link rel='gettext' type='application/x-po' href='./locale/".$http_lang."/LC_MESSAGES/messages.po' />";
			}
		}else if($default_l==$http_lang && $default_l != "en_US"){
				echo "<link rel='gettext' type='application/x-po' href='./locale/".$http_lang."/LC_MESSAGES/messages.po' />";
		}
	?>
</head>
<body class="home">

	<header>
		<a href="#main-content" class="skip"><?php echo _('Skip to main content'); ?></a>
    <h1 class="title-main fw-normal"><?php echo _("<b>eRM</b> &bullet; eResource Management");?></h1>
    <p>
			<label for="lang" class="language-select"><?php echo _("Change language:");?></label>
      <?php $lang_name->getLanguageSelector(); ?>
		</p>
	</header>
<main id="main-content">
	<article>
	<ul class="icons">
		<?php
		$mainPageIcon = "";
		$modules = [ "resources" => _("Resources"), "licensing" => _("Licensing"), "organizations" => _("Organizations"), "usage" => _("Usage Statistics"), "management" => _("Management") ];

		foreach ($modules as $key => $value)
		{
			$module = "";
			try
			{
				$mod_conf = Config::getSettingsFor($key);
				if (isset($mod_conf["enabled"]) && $mod_conf["enabled"] == "Y")
				{
					$module = "<a href='{$key}/'><img src='images/icon-{$key}.png' alt='' /><span>{$value}</span></a>";
				}
			}
			catch (Exception $e)
			{
				if ($e->getCode() != Config::ERR_VARIABLES_MISSING)
				{
					throw $e;
				}
			}

			if (empty($module))
			{
				$module = "<img src='images/icon-{$key}-off.png'><span>{$value}</span>";
			}
			$mainPageIcon .= "<li id='{$key}' class='main-page-icons'>$module</li>";
		}
		echo $mainPageIcon;
		?>
	</ul>

	</article>
	</main>

	<?php
	include('templates/footer.php');
	?>
</body>
</html>
