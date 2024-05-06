<?php


/*
**************************************************************************************************************************
** CORAL Authentication Module v. 1.0
**
** Copyright (c) 2011 University of Notre Dame
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


session_start();

include_once 'directory.php';

if (isset($_SESSION['loginID'])){
	$loginID=$_SESSION['loginID'];
}

$user = new User(new NamedArguments(array('primaryKey' => $loginID)));

function configSet($config_file, $section, $key, $value) {
	$config_data = parse_ini_file($config_file, true);
	$config_data[$section][$key] = $value;
	$new_content = '';
	foreach ($config_data as $section => $section_content) {
		$section_content = array_map(function($value, $key) {
			return "$key=$value";
		}, array_values($section_content), array_keys($section_content));
		$section_content = implode("\n", $section_content);
		$new_content .= "[$section]\n$section_content\n\n";
	}
	file_put_contents($config_file, $new_content);
}

if (($user->isAdmin) && ($user->getOpenSession())){

	switch ($_GET['action']) {
		case 'submitUser':
			$util = new Utility();

			//if this is an existing user
			if ((isset($_POST['editLoginID'])) && ($_POST['editLoginID'] != '')){
				$sUser = new User(new NamedArguments(array('primaryKey' => $_POST['editLoginID'])));
			}else{
				//set up new user
				$sUser = new User();
				$sUser->loginID = $_POST['loginID'];
			}

			//only update it if it was sent
			if (isset($_POST['password']) && ($_POST['password'] != '')){
				$prefix = $util->randomString(45);
				$sUser->password 		= $util->hashString('sha512', $prefix . $_POST['password']);
				$sUser->passwordPrefix	= $prefix;
			}

			if ($_POST['adminInd'] == "1" || $_POST['adminInd'] == "Y"){
				$sUser->adminInd 		= "Y";
			}else{
				$sUser->adminInd 		= "N";
			}

			try {
				$sUser->save();
			} catch (Exception $e) {
				echo "<span class='error'>";
				echo $e->getMessage();
				echo "</span>";
			}

			break;

		case 'deleteUser':
			$loginID = $_GET['loginID'];
			$dUser = new User(new NamedArguments(array('primaryKey' => $loginID)));

			try {
				$dUser->delete();
				echo "<span class='success'>";
				echo _("User successfully deleted.");
				echo "</span>";
			} catch (Exception $e) {
				echo "<span class='error'>";
				echo $e->getMessage();
				echo "</span>";
			}

			break;
		case 'updateMenu':
			$updateStatus = $_GET['menuStatus'];
			$fileName = "../common/configuration.ini";
			$setting = ($updateStatus === "true") ? "Y" : "N";
			$commonConfig = "../common/configuration.ini";
			configSet($commonConfig, 'settings', 'displayFullMenu', $setting);
			break;
		case 'updateSettings':
			$commonConfig = "../common/configuration.ini";
			configSet($commonConfig, 'settings', 'displayFullMenu', 'Y');
			break;
		default:
				if (empty($action))
            return;
		   printf(_("Action %s not set up!"), $action);
		   break;

	}
}

?>
