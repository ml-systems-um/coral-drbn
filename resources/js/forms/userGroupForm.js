/*
**************************************************************************************************************************
** CORAL Resources Module v. 1.0
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


$(document).ready(function(){
	 $("#submitUserGroupForm").click(function () {
		submitUserGroup();
	 });

	$(".addUser").on('click', function () {
		var loginID = $('.newUserTR .loginID').val();
		if ((loginID == '') || (loginID == null)){
			$('#div_errorUser').html(_("Error - User is required"));
			return false;
		}else{
			$('#div_errorUser').html('');
			let newRow = $('#newUserSkeleton').clone();
			newRow.removeAttr('id');
			newRow.removeAttr('hidden');
			//Get the selected user option.
			let userOption = $('.changeSelect.loginID option:selected');
			if (!userOption.length) {
				return false;
			}

			let userName = userOption.text();
			let userID = userOption.val();
			
			//Set the values in the cloned, template row.
			$('[name="assignedUsers[]"]', newRow).val(userID);
			$('[name="assignedUserNames"]', newRow).val(userName);
			//Update the alt and text to user the user name. First get the button, then revise the text.
			let deleteImage = $('[name="removeImage"]', newRow);
			let altText = deleteImage.prop('alt');
			let titleText = deleteImage.prop('title');
			deleteImage.prop('alt', altText.replace('NEWUSER', userName));
			deleteImage.prop('title', titleText.replace('NEWUSER', userName));

			$(".remove", newRow).on('click', function() {
				removeUser($(this));
			});
			newRow.appendTo('.userTable');

			//Disable the option in the user dropdown and reset the choice.
			$("#loginIDSelect").children(`[value='${userID}']`).prop('disabled', true);
			//Reset to the top choice.
			$("#loginIDSelect").val('').change();
			updateNoUsers();
			return false;
		}
	});

	$(".remove").on('click', function() {

		removeUser($(this));
	});

	updateNoUsers();
 });

function updateNoUsers(){
	//How many users are listed?
	let userList = $('ul#userList > :not(#newUserSkeleton)');
	if(userList.length == 0){
		//There aren't users.
		$('<li id="noUsers" class="wide"><em>'+noUserString+'</em></li>').appendTo("ul#userList");
	} else {
		//Try to remove No Users if it exists.
		$('#noUsers').remove();
	}
}

 function removeUser(btn) {
		btn.closest('.newUser').fadeTo(400, 0, function () {
			//Best we can do right now is assume the child with type='hidden' is the loginID.
			let loginID = $(this).children("[type='hidden']").val();
			//Reenable the option in the Select Dropdown.
			$("#loginIDSelect").children(`[value='${loginID}']`).removeAttr('disabled');
			//Remove the row from the user list and check to see if there are remaining users.
			this.remove();
			updateNoUsers();
		});
		return false;
 }

 function validateUserGroup(){
	let validated = validateRequired('groupName',"<br />"+_("Group name must be entered to continue.")+"<br />");
	return validated;
}

function submitUserGroup(){
	userList = [];
	let userLoginIDs = $('.newUser:not(#newUserSkeleton)>[name="assignedUsers[]"]');
	userLoginIDs.each(function(){
		let login = $(this).val();
		userList.push(login);
	});

	if (validateUserGroup() === true) {
		$('#submitUserGroupForm').attr("disabled", "disabled");
		  $.ajax({
			 type:       "POST",
			 url:        "ajax_processing.php?action=submitUserGroup",
			 cache:      false,
			 data:      { 
							userGroupID: $("#editUserGroupID").val(), 
							groupName: $("#groupName").val(), 
							emailAddress: $("#emailAddress").val(), 
							usersList: userList  
						},
			 success:    function(html) {
				if (html){
					$("#span_errors").html(html);
					$("#submitUserGroupForm").removeAttr("disabled");
				}else{
					myDialogPOST();
					window.parent.updateWorkflowTable();
					return false;
				}
			 }
		 });
	}
}

//kill all binds done by jquery live
function kill(){
	$('.addUser').die('click');
	$('.remove').die('click');
}
