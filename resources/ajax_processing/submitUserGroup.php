<?php
		//Check to see if a userGroupID was passed along.
		$userGroupID = ($_POST['userGroupID']) ?? FALSE;
		$userGroup = ($userGroupID) ? 
			new UserGroup(new NamedArguments(array('primaryKey' => $userGroupID))) : 
			new UserGroup();

		//Overwrite or set the groupName and emailAddress values.
		$userGroup->groupName = ($_POST['groupName']) ?? "";
		$userGroup->emailAddress = ($_POST['emailAddress']) ?? "";

		try {
			//Try to save the user group.
			$userGroup->save();
			//Get the usergroup ID.
			$userGroupID = $userGroup->primaryKey;

			//From the loginIDs provided, we only need to add or remove users from the group. Existing users don't need to be touched, so we won't bother. First, get the list of loginIDs from the form.
			$usersArray = ($_POST['usersList']) ?? [];
			//Confirm that it's an array. Just in case.
			$usersArray = (is_array($usersArray)) ? $usersArray : [];
			//Now get the users currently listed in the group.
			$existingUsers = $userGroup->getUserLoginIDs();
			//Now compare the two arrays from each side to get the removed and new users.
			$removedUsers = array_diff($existingUsers, $usersArray);
			$newUsers = array_diff($usersArray, $existingUsers);
			
			//First, remove the users that aren't in the provided usersArray.
			foreach($removedUsers as $user){
				$userGroup->removeUser($user);
			}

			//Now try to add the new users.
			foreach ($newUsers as $user){
				if ($user){
					$userGroupLink = new UserGroupLink();
					$userGroupLink->loginID = $user;
					$userGroupLink->userGroupID = $userGroupID;

					try {
						$userGroupLink->save();
					} catch (Exception $e) {
						echo "<span class='error'>";
						echo $e->getMessage();
						echo "</span>";
					}
				}
			}



		} catch (Exception $e) {
			echo "<span class='error'>";
			echo $e->getMessage();
			echo "</span>";
		}

?>
