/*
**************************************************************************************************************************
** CORAL Resources Module v. 1.2
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

    baseTitle = document.title;

    updateUserTable();
    $("#side li:first-child a").attr('aria-current', 'page');

    $("#side li a").click(function () {
        $("#side li a").removeAttr('aria-current');
        $(this).attr('aria-current', 'page');
        updateTable($(this).attr("id"));
        updateDocTitle($(this).text());
    });

    $("#UserAdminLink").click(function () {
        updateUserTable();
    });

    $("#AlertAdminLink").click(function () {
        updateAlertTable();
    });


    $("#WorkflowAdminLink").click(function () {
        updateWorkflowTable();
    });

    $("#SubjectsAdminLink").click(function () {
        updateSubjectsTable();
    });


    $("#CurrencyLink").click(function () {
        updateCurrencyTable();
    });

    $("#FundLink").click(function () {
        updateFundTable();
    });

    $("#ImportConfigLink").click(function () {
        updateImportConfigTable();
    });

    $("#EbscoKbConfigLink").click(function () {
        updateEbscoKbConfigTable();
    });

    $("#ebscoKbConfig").on('submit', function(e){
        e.preventDefault();
        submitEbscoKbData();
    });

    $('.removeData').on('click', function () {
        deleteData($(this).attr("cn"), $(this).attr("id"));
    });

});

function updateDocTitle(newTitle) {
	document.title = newTitle + ' - ' + baseTitle;
}

function updateTable(className){

    $.ajax({
        type:       "GET",
        url:        "ajax_htmldata.php",
        cache:      false,
        data:       "action=getAdminDisplay&className=" + className,
        success:    function(html) {
            $('#div_AdminContent').html(html);
        }
    });

    //make sure error is empty
    $('#div_error').html("");


}


function updateCurrencyTable(){

    $.ajax({
        type:       "GET",
        url:        "ajax_htmldata.php",
        cache:      false,
        data:       "action=getAdminCurrencyDisplay",
        success:    function(html) {
            $('#div_AdminContent').html(html);
        }
    });

    //make sure error is empty
    $('#div_error').html("");

}

function updateFundTable(){

    $.ajax({
        type:       "GET",
        url:        "ajax_htmldata.php",
        cache:      false,
        data:       "action=getAdminFundDisplay",
        success:    function(html) {
            $('#div_AdminContent').html(html);
        }
    });

    //make sure error is empty
    $('#div_error').html("");

}

function updateImportConfigTable(){

    $.ajax({
        type:       "GET",
        url:        "ajax_htmldata.php",
        cache:      false,
        data:       "action=getAdminImportConfigDisplay",
        success:    function(html) {
            $('#div_AdminContent').html(html);
        }
    });

    //make sure error is empty
    $('#div_error').html("");

}


function updateUserTable(){

    $.ajax({
        type:       "GET",
        url:        "ajax_htmldata.php",
        cache:      false,
        data:       "action=getAdminUserDisplay",
        success:    function(html) {
            $('#div_AdminContent').html(html);
        }
    });

    //make sure error is empty
    $('#div_error').html("");

}


function updateAlertTable(){

    $.ajax({
        type:       "GET",
        url:        "ajax_htmldata.php",
        cache:      false,
        data:       "action=getAdminAlertDisplay",
        success:    function(html) {
            $('#div_AdminContent').html(html);
        }
    });

    //make sure error is empty
    $('#div_error').html("");

}


function updateWorkflowTable(){

    $.ajax({
        type:       "GET",
        url:        "ajax_htmldata.php",
        cache:      false,
        data:       "action=getAdminWorkflowDisplay",
        success:    function(html) {
            $('#div_AdminContent').html(html);
        }
    });

    //make sure error is empty
    $('#div_error').html("");

}

function updateSubjectsTable(){

    $.ajax({
        type:       "GET",
        url:        "ajax_htmldata.php",
        cache:      false,
        data:       "action=getAdminSubjectDisplay",
        success:    function(html) {
            $('#div_AdminContent').html(html);
        }
    });

    //make sure error is empty
    $('#div_error').html("");

}

function updateEbscoKbConfigTable(){

  $.ajax({
    type:       "GET",
    url:        "ajax_htmldata.php",
    cache:      false,
    data:       "action=getAdminEbscoKbConfigDisplay",
    success:    function(html) {
      $('#div_AdminContent').html(html);
    }
  });

  //make sure error is empty
  $('#div_error').html("");

}

function submitData(){
    if (validateAdminForms() === true) {
        //The stats addition requires special checking.
        let statsCheck = $('#stats:checkbox:checked');
        let statsValue = (statsCheck.length > 0);
        $.ajax({
            type:       "POST",
            url:        "ajax_processing.php?action=updateData",
            cache:      false,
            data:       { className: $("#editClassName").val(), updateID: $("#editUpdateID").val(), shortName: $('#updateVal').val(), stats: statsValue },
            success:    function(html) {
                updateTable($("#editClassName").val());
        	    myDialogPOST();
	        }
        });
    }
}

// Validate admin forms
function validateAdminForms() {
    if ($("#updateVal").val() == ''){
        $("#span_errors").html('Error - Please enter a value');
        $("#updateVal").focus();
        return false;
    }else{
        return true;
    }
}
function submitUserData(){
    $.ajax({
        type:       "POST",
        url:        "ajax_processing.php?action=updateUserData",
        cache:      false,
        data:       { orgloginID: $('#editLoginID').val(), loginID: $('#loginID').val(), firstName: $('#firstName').val(), lastName: $('#lastName').val(), emailAddress: $('#emailAddress').val(), privilegeID: $('#privilegeID').val(), accountTabIndicator: getCheckboxValue('accountTab') },
        success:    function(html) {
            updateUserTable();
            myDialogPOST();
        }
    });

}


function submitCurrencyData(){
    if(validateCurrency() === true){
        $.ajax({
            type:       "POST",
            url:        "ajax_processing.php?action=updateCurrency",
            cache:      false,
            data:       { editCurrencyCode: $('#editCurrencyCode').val(), currencyCode: $('#currencyCode').val(), shortName: $('#shortName').val() },
            success:    function(html) {
                updateCurrencyTable();
                myDialogPOST();
            }
        });
    }
}

// Validate currency form
function validateCurrency() {
    if ($("#currencyCode").val() == ''){
        $("#span_errors").html('Error - Please enter the currency code');
        $("#currencyCode").focus();
        return false;
    }else if($("#shortName").val() == ''){
        $("#span_errors").html('Error - Please enter a short name for the currency');
        $("#shortName").focus();
        return false;
    }else{
        return true;
    }
}

// Validate fund form
function submitFundData(){
	var isArchived = $('#archivedUpdate').attr('checked');
    if(validateFund() === true){
        $.ajax({
            type:       "POST",
            url:        "ajax_processing.php?action=updateFund",
            cache:      false,
            data:       { fundID: $('#fundID').val(), fundCode: $('#fundCode').val(), shortName: $('#shortName').val(), archived: isArchived },
            success:    function(html) {
                updateFundTable();
                myDialogPOST();
            }
        });
    }
}

// Validate fund form
function validateFund() {
    if ($("#fundCode").val() == ''){
        $("#span_errors").html('Error - Please enter the fund code');
        $("#fundCode").focus();
        return false;
    }else if($("#shortName").val() == ''){
        $("#span_errors").html('Error - Please enter a short name for the fund');
        $("#shortName").focus();
        return false;
    }else{
        return true;
    }
}

function submitImportConfigData() {
    if(validateImportConfig() === true)
    {
        //assemble configuration data as json
        var jsonData = {};
        jsonData.title = $('#resource_titleCol').val();
        jsonData.description = $('#resource_descCol').val();
        jsonData.alias = [];
        $('div.alias-record').each(function() {
            var aliasObject={}
            aliasObject.column=$(this).find('input.ic-column').val();
            aliasObject.aliasType=$(this).find('select').val();
            aliasObject.delimiter=$(this).find('input.ic-delimiter').val();
            jsonData.alias.push(aliasObject);
        });
        jsonData.url = $('#resource_urlCol').val();
        jsonData.altUrl = $("#resource_altUrlCol").val();
        jsonData.parent = [];
        $('div#resource_parent').find('input').each(function() {
            jsonData.parent.push($(this).val());
        });
        jsonData.isbnOrIssn = [];
        $('div.isbnOrIssn-record').each(function() {
            var isbnOrIssnObj = {};
            isbnOrIssnObj.column = $(this).find('input.ic-column').val();
            isbnOrIssnObj.delimiter = $(this).find('input.ic-delimiter').val();
            isbnOrIssnObj.dedupe = $(this).find('input.ic-dedupe').attr('checked');
            jsonData.isbnOrIssn.push(isbnOrIssnObj);
        });
        jsonData.resourceFormat = $("#resource_format").val();
        jsonData.resourceType = $("#resource_type").val();
        jsonData.acquisitionType = $("#acquisition_type").val();
        jsonData.fundCode = $("#fundCode").val();
        jsonData.cost = $("#cost").val();
	jsonData.currencyCode = $("#currency").val();
        jsonData.orderTypeID = $("#orderType").val();
        jsonData.sendemails = $("#sendemails").attr('checked');
        jsonData.subject = [];
        $('div.subject-record').each(function() {
            var subjectObject = {};
            subjectObject.column = $(this).find('input.ic-column').val();
            subjectObject.delimiter = $(this).find('input.ic-delimiter').val();
            jsonData.subject.push(subjectObject);
        });
        jsonData.note = [];
        $('div.note-record').each(function() {
            var noteObject={};
            noteObject.column=$(this).find('input.ic-column').val();
            noteObject.noteType=$(this).find('select').val();
            noteObject.delimiter=$(this).find('input.ic-delimiter').val();
            jsonData.note.push(noteObject);
        });
        jsonData.organization = [];
        $('div.organization-record').each(function() {
            var organizationObject={}
            organizationObject.column=$(this).find('input').val();
            organizationObject.organizationRole=$(this).find('select').val();
            jsonData.organization.push(organizationObject);
        });
        var configuration = JSON.stringify(jsonData);
        var orgNameImported = '';
        $('.ic-org-imported').each(function() {
            orgNameImported += $(this).val() + ":::";
        });

        var orgNameMapped = '';
        $('.ic-org-mapped').each(function() {
            orgNameMapped += $(this).val() + ":::";
        });
        $.ajax({
            type:       "POST",
            url:        "ajax_processing.php?action=updateImportConfig",
            cache:      false,
            data:       { importConfigID: $('#importConfigID').val(), shortName: $('#shortName').val(), configuration: configuration, orgNameImported: orgNameImported, orgNameMapped: orgNameMapped},
            success:    function(html) {
                updateImportConfigTable();
                myDialogPOST();
            }
        });
    }
}

function validateImportConfig() {
    if($("#shortName").val() == ''){
        $("#span_errors").html('Error - Please enter a short name for the fund');
        $("#shortName").focus();
        return false;
    }else{
        return true;
    }

}

function submitAdminAlertEmail() {
    $.ajax({
        type:       "POST",
        url:        "ajax_processing.php?action=updateAdminAlertEmail",
        cache:      false,
        data:       { alertEmailAddressID: $('#editAlertEmailAddressID').val(), emailAddress: $('#emailAddress').val() },
        success:    function(html) {
            updateAlertTable();
            myDialogPOST();
        }
    });

}


function submitAdminAlertDays(){

    numberOfDays = $('#daysInAdvanceNumber').val();

    if (parseInt(numberOfDays) != numberOfDays-0){
        $('#div_form_error').html(_("Number of days must be a number"));
        return false;
    }else if ((numberOfDays < 1) || (numberOfDays > 365)){
        $('#div_form_error').html(_("Number of days should be between 1 and 365"));
        return false;
    }else{
        $('#div_form_error').html("&nbsp;");
        $.ajax({
            type:       "POST",
            url:        "ajax_processing.php?action=updateAdminAlertDays",
            cache:      false,
            data:       { alertDaysInAdvanceID: $('#editAlertDaysInAdvanceID').val(), daysInAdvanceNumber: $('#daysInAdvanceNumber').val() },
            success:    function(html) {
                updateAlertTable();
                myDialogPost();
            }
        });
    }
}

// Validate fund form
function submitEbscoKbData(){
    var form = $('#ebscoKbConfig');
    $('#ebscoKbConfigError').html();
    $.ajax({
      type:       "POST",
      url:        "ajax_processing.php?action=updateEbscoKbConfig",
      cache:      false,
      data:       form.serialize(),
      success:    function(e) {
        updateEbscoKbConfigTable();
        setTimeout(function(){
          window.location.reload();
        },500);
      },
      error:      function(xhr) {
        $('#ebscoKbConfigError').html(xhr.responseText);
      }
    });
}


function deleteData(className, deleteID){

    if (confirm(_("Do you really want to delete this data?")) == true) {

        $.ajax({
            type:       "GET",
            url:        "ajax_processing.php",
            cache:      false,
            data:       "action=deleteInstance&class=" + className + "&id=" + deleteID,
            success:    function(html) {

                if (html){
                    showError(html);

                    // close the div in 3 secs
                    setTimeout("emptyError();",3000);
                }else{
                    updateTable(className);
                }

            }
        });

    }
}

function deleteGeneralSubject(className, deleteID){

    if (confirm(_("Do you really want to remove this data?")) == true) {

        $.ajax({
            type:       "GET",
            url:        "ajax_processing.php",
            cache:      false,
            data:       "action=deleteGeneralSubject&class=" + className + "&id=" + deleteID,
            success:    function(html) {

                if (html){
                    showError(html);

                    // close the div in 3 secs
                    setTimeout("emptyError();",3000);
                }else{
                    updateSubjectsTable();
                }

            }
        });

    }
}


function deleteDetailedSubject(className, deleteID){

    if (confirm(_("Do you really want to remove this data?")) == true) {

        $.ajax({
            type:       "GET",
            url:        "ajax_processing.php",
            cache:      false,
            data:       "action=deleteDetailedSubject&class=" + className + "&id=" + deleteID,
            success:    function(html) {

                if (html){
                    showError(html);

                    // close the div in 3 secs
                    setTimeout("emptyError();",3000);
                }else{
                    updateSubjectsTable();
                }

            }
        });

    }
}


function deleteGeneralDetailSubject(className, deleteID){

    if (confirm(_("Do you really want to remove this data?")) == true) {

        $.ajax({
            type:       "GET",
            url:        "ajax_processing.php",
            cache:      false,
            data:       "action=deleteInstance&class=" + className + "&id=" + deleteID,
            success:    function(html) {

                if (html){
                    showError(html);

                    // close the div in 3 secs
                    setTimeout("emptyError();",3000);
                }else{
                    updateSubjectsTable();
                }

            }
        });

    }
}


function deleteUser(deleteId){

    if (confirm(_("Do you really want to delete this user?")) == true) {

        $('#span_User_response').html('<img src = "images/circle.gif">&nbsp;&nbsp;Processing...');
        $.ajax({
            type:       "GET",
            url:        "ajax_processing.php",
            cache:      false,
            data:       "action=deleteInstance&class=User&id=" + deleteId,
            success:    function(html) {
                if (html){
                    showError(html);

                    // close the div in 3 secs
                    setTimeout("emptyError();",3000);
                }else{
                    updateUserTable();
                }
            }
        });

    }
}


function deleteAlert(className, deleteID){

    if (confirm(_("Do you really want to remove this alert setting?")) == true) {

        $.ajax({
            type:       "GET",
            url:        "ajax_processing.php",
            cache:      false,
            data:       "action=deleteInstance&class=" + className + "&id=" + deleteID,
            success:    function(html) {

                if (html){
                    showError(html);

                    // close the div in 3 secs
                    setTimeout("emptyError();",3000);
                }else{
                    updateAlertTable();
                }

            }
        });

    }
}

function duplicateWorkflow(sourceID){

    $.ajax({
        type:       "get",
        url:        "ajax_processing.php",
        cache:      false,
        data:       "action=duplicateWorkflow&id=" + sourceID,
        success:    function(html) {

            if (html){
                showError(html);

                // close the div in 3 secs
                setTimeout("emptyError();",3000);
            }else{
                updateWorkflowTable();
            }

        }
    });

}

function deleteWorkflow(className, deleteID){

    if (confirm(_("Do you really want to remove this data?")) == true) {

        $.ajax({
            type:       "GET",
            url:        "ajax_processing.php",
            cache:      false,
            data:       "action=deleteInstance&class=" + className + "&id=" + deleteID,
            success:    function(html) {

                if (html){
                    showError(html);

                    // close the div in 3 secs
                    setTimeout("emptyError();",3000);
                }else{
                    updateWorkflowTable();
                }

            }
        });

    }
}


function deleteCurrency(className, deleteID){

    if (confirm(_("Do you really want to delete this currency?")) == true) {

        $.ajax({
            type:       "GET",
            url:        "ajax_processing.php",
            cache:      false,
            data:       "action=deleteInstance&class=" + className + "&id=" + deleteID,
            success:    function(html) {

                if (html){
                    showError(html);

                    // close the div in 3 secs
                    setTimeout("emptyError();",3000);
                }else{
                    updateCurrencyTable();
                }

            }
        });

    }
}

function deleteFund(className, deleteID){
    if (confirm("Do you really want to delete this fund?") == true) {

        $.ajax({
            type:       "GET",
            url:        "ajax_processing.php",
            cache:      false,
            data:       "action=deleteInstance&class=" + className + "&id=" + deleteID,
            success:    function(html) {

                if (html){
					alert(html);
                }else{
                    updateFundTable();
                }

            }
        });
    }
}

function deleteImportConfig(className, deleteID){
    if (confirm("Do you really want to delete this import configuration?") == true) {

        $.ajax({
            type:       "GET",
            url:        "ajax_processing.php",
            cache:      false,
            data:       "action=deleteImportConfig&importConfigID=" + deleteID,
            success:    function(html) {

                if (html){
                    alert(html);
                    updateImportConfigTable();
                }else{
                    updateImportConfigTable();
                }

            }
        });
    }
}

function archiveFund(isChecked, fundID, fundCode, shortName) {
	var conformMsg;
	var warningMsgArchive = "The archived funds will not be used for new resources. Do you  want to continue?";
	var warningMsgRestore = "Do you want to restore this fund?";

	if(isChecked == true)	{
		conformMsg = warningMsgArchive;	}
	else{
		conformMsg = warningMsgRestore;}

	if (confirm(conformMsg) == true) {
	         $.ajax({
			            type:       "POST",
			            url:        "ajax_processing.php?action=updateFund",
			            cache:      false,
			            data:       { fundID: fundID, fundCode: fundCode, shortName: shortName, archived: isChecked },
			            success:    function(html) {
			                updateFundTable();
			                myDialogPOST();
			            }
        });
    }
    else
    {
		 updateFundTable();
	}
}

function showError(html){

    $('#div_error').fadeTo(0, 5000, function () {
        $('#div_error').html(html);

    });
}


function emptyError(){

    $('#div_error').fadeTo(500, 0, function () {
        $('#div_error').html("");
    });

}
