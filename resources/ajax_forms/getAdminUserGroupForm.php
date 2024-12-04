<?php
	if (isset($_GET['userGroupID'])) $userGroupID = $_GET['userGroupID']; else $userGroupID = '';
	$userGroup = new UserGroup(new NamedArguments(array('primaryKey' => $userGroupID)));

	//get all users for output in drop down
	$allUserArray = array();
	$userObj = new User();
	$allUserArray = $userObj->allAsArray();

	//get users already set up for this user group in case it's an edit
	$ugUserArray = $userGroup->getUsers();
?>
		<div id='div_userGroupForm'>
		<form id='userGroupForm'>
		<input type='hidden' name='editUserGroupID' id='editUserGroupID' value='<?php echo $userGroupID; ?>'>

		<div class='formTitle' style='width:280px; margin-bottom:5px;position:relative;'><span class='headerText'><?php if ($userGroupID){ echo _("Edit User Group"); } else { echo _("Add User Group"); } ?></span></div>

		<p class='error' id='span_errors'></p>
		<!-- TODO: eliminate nested tables -->
		<!-- Note that userGroupForm.js validation logic uses table/row classes -->
		<table class='noBorder' style='width:100%;'>
		<tr style='vertical-align:top;'>
		<td style='vertical-align:top;position:relative;'>


			<span class='surroundBoxTitle'><b><?php echo _("User Group");?></b></span>

			<table class='surroundBox'>
			<tr>
			<td>

				<table class='noBorder'>
				<tr>
				<td><label for='groupName'><b><?php echo _("Group Name:");?></b></label></td>
				<td>
				<input type='text' id='groupName' name='groupName' value = '<?php echo $userGroup->groupName; ?>' class='changeInput' aria-describedby="span_error_groupName" /><p id='span_error_groupName' class='error'></p>
				</td>
				</tr>

				<tr>
				<td><label for='emailAddress' style="white-space: nowrap;"><b><?php echo _("Email Addresses:");?></b></label></td>
				<td>
				<input type='text' id='emailAddress' name='emailAddress' value = '<?php echo $userGroup->emailAddress; ?>' style='width:210px' class='changeInput' />
				</td>
				</tr>
                <tr><td colspan="2"><?php echo _("(use comma and a space between each email address)"); ?></td></tr>
				</table>
			</td>
			</tr>
			</table>

			<div style='height:10px;'>&nbsp;</div>

			</td>
			</tr>
			<tr>
			<td>

			<span class='surroundBoxTitle'>&nbsp;&nbsp;<label for='loginID'><b><?php echo _("Assigned Users");?></b></label>&nbsp;&nbsp;</span>

			<table class='surroundBox'>
			<tr>
			<td>

				<table class='noBorder smallPadding newUserTable' style='width:205px; margin:15px 35px 0px 35px;'>

				<tr class='newUserTR'>
				<td>
				<select class='changeSelect loginID' style='width:145px;'>
				<option value=''></option>
				<?php

				foreach ($allUserArray as $ugUser){
					$userObj = new User(new NamedArguments(array('primaryKey' => $ugUser['loginID'])));
					$ddDisplayName = $userObj->getDDDisplayName;
					echo "<option value='" . $ugUser['loginID'] . "'>" . $ddDisplayName . "</option>\n";
				}
				?>
				</select>
				</td>

				<td class="actions">
				<a href="javascript:void(0)"><input class='addUser add-button' title='<?php echo _("add user");?>' type='button' value='<?php echo _("Add");?>'/></a>
				</td>
				</tr>
				</table>
				<p class='error' id='div_errorUser'></p>

				<table class='noBorder smallPadding userTable' style='width:205px; margin:0px 35px 0px 35px;'>
				<tr>
				<td colspan='2'>
					<hr style='width:200px;' />
				</td>
				</tr>

				<?php
				if (is_array($ugUserArray) && count($ugUserArray) > 0) {

					foreach ($ugUserArray as $ugUser){
					?>
						<tr class='newUser'>
						<td>
						<select class='changeSelect loginID' style='width:145px;'>
						<option value=''></option>
						<?php
						foreach ($allUserArray as $userGroupUser){

							$userObj = new User(new NamedArguments(array('primaryKey' => $userGroupUser['loginID'])));
							$ddDisplayName = $userObj->getDDDisplayName;

							if ($ugUser->loginID == $userGroupUser['loginID']){
								echo "<option value='" . $userGroupUser['loginID'] . "' selected>" . $ddDisplayName . "</option>\n";
							}else{
								echo "<option value='" . $userGroupUser['loginID'] . "'>" . $ddDisplayName . "</option>\n";
							}
						}
						?>
						</select>
						</td>

						<td class="actions">
							<a href="javascript:void(0)"><img src='images/cross.gif' alt="<?php echo _("remove user from group");?>" title="<?php echo _("remove user from group");?>" class='remove' /></a>
						</td>
						</tr>
					<?php
					}
				}

				?>

				</table>



			</td>
			</tr>
			</table>

		</td>
		</tr>
		</table>


		<hr style='width:283px;margin-top:15px; margin-bottom:10px;' />

		<table class='noBorderTable' style='width:125px;'>
			<tr>
				<td style='text-align:left'><input type='button' value='<?php echo _("submit");?>' name='submitUserGroupForm' id ='submitUserGroupForm' class='submit-button primary'></td>
				<td style='text-align:right'><input type='button' value='<?php echo _("cancel");?>' onclick="myCloseDialog()" class='cancel-button secondary'></td>
			</tr>
		</table>

		</form>
		</div>

		<script type="text/javascript" src="js/forms/userGroupForm.js?random=<?php echo rand(); ?>"></script>

