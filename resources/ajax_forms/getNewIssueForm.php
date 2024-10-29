<?php
$util = new utility();

$resourceID = $_GET["resourceID"];
$resourceAcquisitionID = $_GET["resourceAcquisitionID"];

$resourceAcquisition = new ResourceAcquisition(new NamedArguments(array('primaryKey' => $resourceAcquisitionID)));
$resource = new Resource(new NamedArguments(array('primaryKey' => $resourceID)));

$organizationArray = $resource->getOrganizationArray();
$organizationData = $organizationArray[0];

$config = new Configuration();
//the issues feature currently support org or resource contacts, but not both
$moduleFilter = ($config->settings->organizationsModule == 'Y') ? 'organizations':'resources';
$contactsArray = $resourceAcquisition->getUnarchivedContacts($moduleFilter);
if ($organizationData['organizationID']) {
	$organizationResourcesArray = $resource->getSiblingResourcesArray($organizationData['organizationID']);
?>

<form id='newIssueForm'>
	<input type="hidden" id="sourceOrganizationID" name="sourceOrganizationID" value="<?php echo $organizationData['organizationID'];?>" />
	<input type="hidden" name="sourceResourceID" value="<?php echo $resourceID;?>" />
	<input type="hidden" name="sourceResourceAcquisitionID" value="<?php echo $resourceAcquisitionID;?>" />
	<table class="thickboxTable" style="width:98%;background-image:url('images/title.gif');background-repeat:no-repeat;">
		<tr>
			<td colspan="2">
				<h1><?php echo _("Report New Problem");?></h1>
				<span class='error'><?php echo _("* required fields");?></span>
			</td>
		</tr>
		<tr>
			<td><label><?php echo _("Organization:");?>&nbsp;&nbsp;<span class='bigDarkRedText'>*</span></label></td>
			<td>
				<p><?php echo $organizationData['organization']; ?></p>
				<span id='span_error_organizationId' class='error'></span>
			</td>
		</tr>
		<tr>
			<td><label for="contactIDs"><?php echo _("Contact:");?>&nbsp;&nbsp;<span class='bigDarkRedText'>*</span></label></td>
			<td>
				<select multiple style="min-height: 60px;" type='text' id='contactIDs' name='contactIDs[]' aria-describedby='span_error_contactName'>
<?php

	foreach ($contactsArray as $contact) {
		if (!empty($contact['name'])) {
			echo "		<option value=\"{$contact['contactID']}\">{$contact['name']}</option>";
		} else {
			echo "		<option value=\"{$contact['contactID']}\">{$contact['emailAddress']}</option>";
		}
	}

?>
				</select>
				<span id='span_error_contactName' class='error'></span>
			</td>
		</tr>
<?php
if ($config->settings->organizationsModule == 'Y') {
?>
		<tr>
			<td></td>
			<td>
				<input type="hidden" name="orgModuleUrl" id="orgModuleUrl" value="<?php echo $util->getCoralUrl();?>organizations/" />
				<a id="getCreateContactForm" href="#"><?php echo _("add contact");?></a>
				<div id="inlineContact"></div>
                <script>
				    $("#getCreateContactForm").on("click",function(e) {
				        e.preventDefault();
				        $(this).fadeOut(250, function() {
				            getInlineContactForm();
				        });
				    });
				</script>
			</td>
		</tr>
<?php
}
?>
		<tr>
			<td><label for="ccCreator"><?php echo _("CC myself:");?></label></td>
			<td>
				<input type='checkbox' id='ccCreator' name='ccCreator' class='changeInput' aria-describedby='span_error_ccCreator' />
				<span id='span_error_ccCreator' class='error error'></span>
			</td>
		</tr>
		<tr>
			<td><label for="inputEmail"><?php echo _("CC:");?></label></td>
			<td>
				<input type="text" id="inputEmail" name="inputEmail" aria-describedby="span_error_contactIDs" />
				<input type="button" id="addEmail" name="addEmail" value="<?php echo _('Add');?>" />
				<p>
					<?php echo _("Current CCs:");?> <span id="currentEmails"></span>
				</p>
				<input type="hidden" id='ccEmails' name='ccEmails' value='' class='changeInput' />
				<span id='span_error_contactIDs' class='error'></span>
			</td>
		</tr>
		<tr>
			<td><label for="subjectText"><?php echo _("Subject:");?>&nbsp;&nbsp;<span class='bigDarkRedText'>*</span></label></td>
			<td>
				<input type='text' id='subjectText' name='issue[subjectText]' value='' class='changeInput' aria-describedby='span_error_subjectText' />
				<span id='span_error_subjectText' class='error error'></span>
			</td>
		</tr>
		<tr>
			<td><label for="bodyText"><?php echo _("Body:");?>&nbsp;&nbsp;<span class='bigDarkRedText'>*</span></label></td>
			<td>
				<textarea id='bodyText' name='issue[bodyText]' value='' aria-describedby="span_error_bodyText"></textarea>
				<span id='span_error_bodyText' class='error error'></span>
			</td>
		</tr>
		<tr>
			<td><label><?php echo _("Applies to:");?>&nbsp;&nbsp;<span class='bigDarkRedText'>*</span></label></td>
			<td>

				<div>
					<input type="checkbox" class="issueResources entityArray" name="resourceIDs[]" value="<?php echo $resourceID;?>" checked /> <label for="thisResources"><?php echo _("Applies only to");?> <?php echo $resource->titleText ?></label>
				</div>
				<div>
					<input type="checkbox" class="issueResources entityArray" name="organizationID" id="organizationID" value="<?php echo $organizationData['organizationID'];?>" /> <label for="allResources"><?php echo _("Applies to all");?> <?php echo $organizationData['organization']; ?> resources</label>
				</div>
				<div>
					<!-- TODO: i18n placeholders -->
					<input type="checkbox" class="issueResources" id="otherResources" /><label for="otherResources"> <?php echo _("Applies to selected");?> <?php echo $organizationData['organization'] ?> <?php echo _("resources");?></label>
				</div>
				<select multiple id="resourceIDs" name="resourceIDs[]" aria-label="<?php echo _('Select resources') ?>" aria-describedby="span_error_entities">
<?php
	if (!empty($organizationResourcesArray)) {
		foreach ($organizationResourcesArray as $resource) {
			echo "		<option class=\"entityArray\" value=\"{$resource['resourceID']}\">{$resource['titleText']}</option>";
		}
	}
?>
				</select>
				<span id='span_error_entities' class='error'></span>
			</td>
		</tr>
	</table>

	<p><label> <?php echo _("Send me a reminder every");?>
		<select name="issue[reminderInterval]">
			<?php for ($i = 1; $i <= 31; $i++) echo "<option".(($i==7) ? ' selected':'').">{$i}</option>"; ?>
		</select> <?php echo _("day(s)");?></label>
	</p>

	<table class='noBorderTable' style='width:125px;'>
		<tr>
			<td style='text-align:left'><input type='submit' value='<?php echo _("submit");?>' name='submitNewIssue' id='submitNewIssue' class='submit-button'></td>
			<td style='text-align:right'><input type='button' value='<?php echo _("cancel");?>' onclick="myCloseDialog()" class='cancel-button'></td>
		</tr>
	</table>

</form>

<?php
} else {
	echo '<p>' . _("Opening an issue requires a resource to be associated with an organization.") . '</p>';
	echo '<input type="button" value="' . _("cancel") . '" onclick="myCloseDialog();">';
}
?>


