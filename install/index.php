<?php
	include_once("classes/common/autoloader.php");
	$versionManager = new install\controller\versionManager();
	$changeExists = ($versionManager->checkForChange());
	if($changeExists){
		//Get a list of versions to load, including (potentially) install.
		$versionsToLoad = $versionManager->getVersionsToLoad();
		//Pass the versions to Load to the Javascript to take over. The rest of the direction code should be controlled by the Javascript file..
		?>
            <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
            	<html xmlns="http://www.w3.org/1999/xhtml">
					<head>
						<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
						<title>CORAL - Centralized Online Resources Acquisitions and Licensing</title>
						<link rel="SHORTCUT ICON" href="images/favicon.ico" />
						<link rel="stylesheet" href="css/install.css">
						<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/skeleton/2.0.4/skeleton.min.css">
						<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css"></script>
						<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
						<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
						<script type="text/javascript" src="js/installDirector.js"></script>
						<script type="text/javascript">
							const versionsToLoad = <?php echo json_encode($versionsToLoad); ?>;
						</script>
					</head>
					<body id="pageBody" class="container"></body>
				</html>

		<?php
		exit; //Prevents the rest of /index.php loading.
	}

function run_loop($version)
{
	$_SESSION["run_loop_version"] = $version;
	require_once "installer.php";
	switch ($version) {
		case Installer::VERSION_STRING_INSTALL:
			$requirement_filter = Installer::REQUIRED_FOR_INSTALL;
			break;
		case Installer::VERSION_STRING_MODIFY:
			$requirement_filter = Installer::REQUIRED_FOR_MODIFY;
			break;
		default:
			$requirement_filter = Installer::REQUIRED_FOR_UPGRADE;
			break;
	}
	try {
		$installer = new Installer($version);
	}
	catch (Exception $e) {
		switch ($e->getCode()) {
			case Installer::ERR_CANNOT_READ_PROVIDER_SCRIPT:
				$yield = new stdClass();
				$yield->messages = [ _("While trying to load module scripts an error occurred."), _("Please check that PHP has execute (probably 644) permission on your install folders.") ];
				yield_test_results_and_exit($yield, [], 0);
				break;
		}
	}
	$requirements = $installer->getRequiredProviders($requirement_filter);
	foreach ($requirements as $i => $requirement) {
		$testResult = $installer->runTestForResult($requirement);

		if (isset($testResult->skipped))
		{
			switch ($testResult->cause)
			{
				case Installer::CAUSE_ALREADY_EXISTED:
					continue 2; // break switch & continue foreach
					break;
				case Installer::CAUSE_DEPENDENCY_NOT_FOUND:
					$testResult->yield = new stdClass();
					$testResult->yield->messages = [ sprintf(_("Dependency for '%s' not found: %s"), $installer->getTitleFromUid($requirement), $testResult->missing_dependency) ];
					yield_test_results_and_exit($testResult->yield, $installer->getSuccessfullyCompletedTestTitles(), $installer->getApproxiamateCompletion());
					break;
			}
		}
		else if (!$testResult->success)
		{
			$installer_messages = $installer->getMessages();
			$test_messages = isset($testResult->yield->messages) ? $testResult->yield->messages : [];
			$testResult->yield->messages = array_merge($installer_messages, $test_messages);
			yield_test_results_and_exit($testResult->yield, $installer->getSuccessfullyCompletedTestTitles(), $installer->getApproxiamateCompletion());
		}
		else
		{
			if (isset($testResult->completionMessages))
				$completionMessages[ $requirement ] = $testResult->completionMessages;
		}
	}

	$installer->declareInstallationComplete();
	$completed_tests = $installer->getSuccessfullyCompletedTestTitles();
	while ($failingPostInstallationTest = $installer->postInstallationTest())
		yield_test_results_and_exit($failingPostInstallationTest->yield, $completed_tests, 97/100);

	// Success!
	$return = new stdClass();
	$return->show_completion = true;
	$return->completion_title = _("Congratulations");
	$return->redirection_message = _("Redirecting Home: ");
	switch ($version) {
		case Installer::VERSION_STRING_INSTALL:
			$return->completion_message = _("Installation has been successfully completed.");
			break;
		case Installer::VERSION_STRING_MODIFY:
			$return->completion_message = _("Installation modification has been successfully completed.");
			break;
		default:
			$return->completion_message = _("Upgrade has been successfully completed.");
			break;
	}
	session_unset();
	yield_test_results_and_exit($return, $completed_tests, 100/100);
}

function do_install()
{
	require_once "test_if_installed.php";
	if (!continue_installing())
	{
		session_unset();
		$return = new stdClass();
		$return->redirect_home = true;
		yield_test_results_and_exit($return, [], 1);
	}
	require_once "installer.php";
	run_loop(Installer::VERSION_STRING_INSTALL);
}

function do_upgrade($version)
{
	global $INSTALLATION_VERSIONS;
	$current_version_index = array_search($version, $INSTALLATION_VERSIONS);
	run_loop($INSTALLATION_VERSIONS[++$current_version_index]);
}

/*
$CURRENT_VERSION = is_installed();
if ($CURRENT_VERSION !== $INSTALLATION_VERSION || !empty($_SESSION["run_loop_version"]))
{
	make_sure_template_is_drawn();
	require_once "test_results_yielder.php";
	if (!empty($_SESSION["run_loop_version"]))
	{
		run_loop($_SESSION["run_loop_version"]);
		exit();
	}
	elseif (!$CURRENT_VERSION)
	{
		do_install();
		exit();
	}
	else
	{
		// Do upgrade
		do_upgrade($CURRENT_VERSION);
		exit();
	}
}
*/

// TODO: Handle these variations
// TAKEN FROM test_if_installed.php -> needs to be handled in do_upgrade()

// elseif (version_compare(INSTALLATION_VERSION, $old_version) > 0)
// {
// 	// This installer installs a newer version
// 	$instruction = _("This installer installs a newer version of CORAL than the one currently installed. This is <b>highly discouraged</b> and will probably result in the loss of data. Instead you should try to upgrade.");
// 	$option_buttons = $allowed_options(["take_me_home", "try_upgrade", "install_anyway"]);
// }
// else if (version_compare(INSTALLATION_VERSION, $old_version) === 0)
// {
// 	// Already installed and current version
// 	$instruction = _("You already have the current version installed. Are you looking for the home page?");
// 	$option_buttons = $allowed_options(["take_me_home"]);
// }
// else if (version_compare(INSTALLATION_VERSION, $old_version) < 0)
// {
// 	// Apparently the already installed version is newer than this installer
// 	$yield->messages[] = _("<b>Warning:</b> A problem exists in your CORAL installation.");
// 	$yield->messages[] = _("<b>Warning:</b> The CORAL version already installed is newer than this software version. You should notify your administrator or the developer.");
// 	$instruction = _("The installed version of CORAL is newer than the newest version this installer can install.");
// 	$option_buttons = $allowed_options(["take_me_home"]);
// }
