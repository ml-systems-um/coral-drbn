<?php
	if (isset($_GET['userGroupID'])) $userGroupID = $_GET['userGroupID']; else $userGroupID = '';
	$userGroup = new UserGroup(new NamedArguments(array('primaryKey' => $userGroupID)));

	//get all users for output in drop down
	$allUserArray = array();
	$userObj = new User();
	$allUserArray = $userObj->allAsArray();

	//get users already set up for this user group in case it's an edit
	$ugUserArray = $userGroup->getUsers();
	//$ugUserArray gets an array of User Objects. We just need loginIDs and the dropdown Display Name.
	$userList = [];
	foreach($ugUserArray as $user){
		$loginID = ($user->loginID) ?? FALSE;
		$displayName = ($user->getDDDisplayName()) ?? FALSE;
		if($loginID && $displayName){
			$userList[$loginID] = $displayName;
		}
	}
?>
<div id='div_userGroupForm'>
	<!-- Note that userGroupForm.js validation logic uses row/input/button classes -->
	<form id='userGroupForm'>
		<input type='hidden' name='editUserGroupID' id='editUserGroupID' value='<?php echo $userGroupID; ?>'>

		<h2 class='headerText'><?php if ($userGroupID){ echo _("Edit User Group"); } else { echo _("Add User Group"); } ?></h2>

		<p class='error' id='span_errors'></p>
		<h3 class="wide"><?php echo _("User Group");?></h3>
		
		<div class="form-grid">
			<label for='groupName'><b><?php echo _("Group Name:");?></b></label>
			<input type='text' maxlength="200" id='groupName' name='groupName' value = '<?php echo $userGroup->groupName; ?>' class='changeInput' aria-describedby="span_error_groupName" required/>
			<p id='span_error_groupName' class='error'></p>

			<label for='emailAddress'><b><?php echo _("Email Addresses:");?></b></label>
			<input type='text' maxlength="200" id='emailAddress' name='emailAddress' value = '<?php echo $userGroup->emailAddress; ?>' class='changeInput' aria-describedby="email-hint" />
			<p class="form-text indent" id="email-hint"><?php echo _("(use comma and a space between each email address)")." "._("(limited to 200 characters)"); ?></p>


			<fieldset class="subgrid">
			<legend id="add-user-heading"><?php echo _("Add User");?></legend>
			<div class='newUserTR form-group'>
				<select id="loginIDSelect" class='changeSelect loginID' aria-labelledby="add-user-heading">
					<option value='' disabled selected></option>
					<?php
					foreach ($allUserArray as $ugUser){
						$loginID = ($ugUser['loginID']) ?? FALSE;
						$alreadyInGroup = ($userList[$loginID]) ?? FALSE;
						$disabled = ($alreadyInGroup) ? "disabled" : "";
						if($loginID){
							$userObj = new User(new NamedArguments(array('primaryKey' => $loginID)));
							$displayName = ($userObj->getDDDisplayName) ?? '';
							echo "<option value='{$loginID}' {$disabled}>{$displayName}</option>";
						}
					}
					?>
				</select>
				<span class="actions">
					<button class='addUser add-button btn link' title='<?php echo _("add user");?>' type='button'><?php echo _("Add");?></button>
				</span>
			</div>
			<p class='error' id='div_errorUser'></p>
			</fieldset>
		</div>
		<script type="text/javascript">
			if (typeof noUserString == 'undefined') {let noUserString = '';}
			noUserString = "<?php printf(_('No users assigned to %s group'), $userGroup->groupName); ?>";
		</script>
		<h3 class="wide"><?php echo _("Assigned Users");?></h3>
		<ul id="userList" class='unstyled userTable wide form-grid'>
		<?php

				$usersExist = (count($ugUserArray) > 0);
				if($usersExist){
					foreach ($userList as $loginID => $displayName){ ?>
						<li class='newUser subgrid'>
							<input type="hidden" name="assignedUsers[]" value="<?php echo $loginID ?>" />
							<input type="text" name="assignedUserNames" aria-labelledby="assigned-user-heading" value="<?php echo $displayName; ?>" readonly />
							<button type="button" class="btn start remove">
								<img src='images/cross.gif' alt="<?php printf(_("remove %s from %s group"), $displayName, $userGroup->groupName);?>" title="<?php printf(_("remove %s from %s group"), $displayName, $userGroup->groupName);?>" />
							</button>
						</li>
					<?php
					}
				}
			?>
				<li class='newUser subgrid' id="newUserSkeleton" hidden>
					<input type="hidden" name="assignedUsers[]" id="newUserID" value="" />
					<input type="text" name="assignedUserNames"  aria-labelledby="assigned-user-heading" value="" readonly />
					<button type="button" class="btn start remove">
						<img name="removeImage" src='images/cross.gif' alt="<?php printf(_('remove NEWUSER from %s group'), $userGroup->groupName); ?>" title="<?php printf(_('remove NEWUSER from %s group'), $userGroup->groupName); ?>" />
					</button>
				</li>
			</ul>

			<p class="actions">
				<input type='submit' value='<?php echo _("update group");?>' name='submitUserGroupForm' id ='submitUserGroupForm' class='submit-button primary'>
				<input type='button' value='<?php echo _("cancel");?>' onclick="myCloseDialog()" class='cancel-button secondary'>
			</p>

	</form>
</div>

<script type="text/javascript" src="js/forms/userGroupForm.js?random=<?php echo rand(); ?>"></script>

