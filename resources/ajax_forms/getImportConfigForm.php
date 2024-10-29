<?php
	if (isset($_POST['jsonData'])) {
		$jsonData = $_POST['jsonData'];
		$configuration = json_decode($jsonData, true);
		$orgMappingsNamed = explode(":::", $_POST['orgNamesMapped']);
		$orgMappingsImported = explode(":::", $_POST['orgNamesImported']);
	} elseif (isset($_POST['configID'])) {
		$configID = $_POST['configID'];
		$instance = new ImportConfig(new NamedArguments(array('primaryKey' => $configID)));
		$orgMappingInstance = new OrgNameMapping();
		$orgMappings=$orgMappingInstance->getOrgNameMappingByImportConfigID($configID);
		$configuration=json_decode($instance->configuration,true);
	}

	//get all alias types for output in drop-down menu
	$aliasTypeArray = array();
	$aliasTypeObj = new AliasType();
	$aliasTypeArray = $aliasTypeObj->allAsArray();
	$aliasOptions = "";
	foreach($aliasTypeArray as $aliasType) {
		$aliasOptions .= "<option value='" . $aliasType['aliasTypeID'] . "'>" . $aliasType['shortName'] . "</option>";
	}


	//get all note types for output in drop-down menu
	$noteTypeArray = array();
	$noteTypeObj = new NoteType();
	$noteTypeArray = $noteTypeObj->allAsArrayForDD();
	$noteOptions = "";
	foreach($noteTypeArray as $noteType) {
		$noteOptions .= "<option value='" . $noteType['noteTypeID'] . "'>" . $noteType['shortName'] . "</option>";
	}

	//get all organization roles for output in drop-down menu
	$organizationRoleArray = array();
	$organizationRoleObj = new OrganizationRole();
	$organizationRoleArray = $organizationRoleObj->getArray();
	$organizationOptions = "";
	foreach($organizationRoleArray as $organizationRoleID => $organizationRoleShortName) {
		$organizationOptions .= "<option value='" . $organizationRoleID . "'>" . $organizationRoleShortName . "</option>";
	}
?>
<div id='importConfigColumns'>
	<div id='importConfigColumnsLeft'>
		<div id='ic-left-column'>
			<fieldset>
				<legend>
					<?php echo _("General Resource Fields");?>
				</legend>
				<p><label class="ic-label"><?php echo _("Resource Title");?><input id="resource_titleCol" class="ic-column" value="<?php echo $configuration["title"]?>" /></label></p>
				<p><label class="ic-label"><?php echo _("Description");?><input id="resource_descCol" class="ic-column" value="<?php echo $configuration["description"]?>" /></label></p>
				<p><label class="ic-label"><?php echo _("Resource URL");?><input id='resource_urlCol' class="ic-column" value="<?php echo $configuration["url"]?>" /></label></p>
				<p><label class="ic-label"><?php echo _("Alternate URL");?><input id='resource_altUrlCol' class="ic-column" value="<?php echo $configuration["altUrl"]?>" /></label></p>
				<p><label class="ic-label"><?php echo _("Resource Format");?><input id="resource_format" class="ic-column" value="<?php echo $configuration["resourceFormat"]?>" /></label></p>
				<p><label class="ic-label"><?php echo _("Resource Type");?><input id="resource_type" class="ic-column" value="<?php echo $configuration["resourceType"]?>" /></label></p>
				<p><label class="ic-label"><?php echo _("Acquisition Type");?><input id="acquisition_type" class="ic-column" value="<?php echo $configuration["acquisitionType"]?>" /></label></p>
			</fieldset>
			<fieldset><legend><?php echo _("Alias Sets");?></legend><div id='resource_alias'>
				<?php
					if(count($configuration["alias"]) > 0) {
						foreach($configuration["alias"] as $alias) {
							echo "<div class='alias-record'><p><label class='ic-label'>" . _("Alias") . "<input class='ic-column' value='".$alias["column"]."' /></label></p>";
							echo "<p><label class='ic-label'>" . _('Alias Type') . "<select class='ic-dropdown'>";
							foreach($aliasTypeArray as $aliasType) {
								echo "<option value='" . $aliasType['aliasTypeID'] . "'";
								if($alias['aliasType'] == $aliasType['aliasTypeID']) {
									echo " selected";
								}
								echo ">" . $aliasType['shortName'] . "</option>";
							}
							echo "</select></label></p>";
							echo "<p><label class='ic-label'>" . _("If delimited, delimited by") . "<input class='ic-delimiter' value='" . $alias["delimiter"]. "' /></label></p></div>";
						}
					}
					else {
						echo "<div class='alias-record'><p><label class='ic-label'>" . _("Alias") . "<input class='ic-column' value='' /></label></p>";
						echo "<p><label class='ic-label'>" . _('Alias Type') . "<select class='ic-dropdown'>";
						foreach($aliasTypeArray as $aliasType) {
							echo "<option value='" . $aliasType['aliasTypeID'] . "'>" . $aliasType['shortName'] . "</option>";
						}
						echo "</select></label></p>";
						echo "<p><label class='ic-label'>" . _("If delimited, delimited by") . "<input class='ic-delimiter' value='' /></label></p></div>";
					}
				?>
			</div><p><a id='add_alias' href='#'><?php echo _("+ Add another alias set");?></a></p></fieldset>

			<fieldset><legend><?php echo _("Resource Parents");?></legend><div id='resource_parent'>
				<?php
					if(count($configuration["parent"]) > 0) {
						foreach($configuration["parent"] as $parent) {
							echo "<p><label class='ic-label'>" . _("Parent Resource") . "<input class='ic-column' value='" . $parent . "' /></label></p>";
						}
					}
					else {
						echo "<p><label class='ic-label'>" . _("Parent Resource") . "<input class='ic-column' value='' /></label></p>";
					}
				?>
			</div><p><a id='add_parent' href='#'><?php echo _("+ Add another parent resource")?></a></p></fieldset>


				<fieldset>
					<legend>
						<?php echo _("ISBN/ISSN Sets");?>
					</legend>
					<div id='resource_isbnOrIssn'>
					<?php
						if(count($configuration["isbnOrIssn"]) > 0) {
							foreach($configuration["isbnOrIssn"] as $isbnOrIssn) {
								echo "<div class='isbnOrIssn-record'><p><label class='ic-label'>" . _("ISBN or ISSN") . "<input class='ic-column' value='" . $isbnOrIssn['column'] . "' /></label></p>";
								echo "<p><label class='ic-label'>" . _("If delimited, delimited by") . "<input class='ic-delimiter' value='" . $isbnOrIssn["delimiter"]. "' /></label></p>";
								echo "<p><label class='ic-dedupe'><input class='ic-dedupe' type='checkbox'" . (($isbnOrIssn['dedupe'])?' checked':'') . " />" . _("Dedupe on this column") . "</label></p></div>";
							}
						}
						else {
							echo "<div class='isbnOrIssn-record'><p><label class='ic-label'>" . _("ISBN or ISSN") . "<input class='ic-column' value='' /></label></p>";
							echo "<p><label class='ic-label'>" . _("If delimited, delimited by") . "<input class='ic-delimiter' value='' /></label></p>";
							echo "<p><label class='ic-dedupe'><input class='ic-dedupe' type='checkbox' />" . _("Dedupe on this column") . "</label></p></div>";
						}
					?>
				</div><p><a id='add_isbnorissn' href='#'><?php echo _("+ Add another ISBN or ISSN set");?></a></p>
			</fieldset>



				<fieldset>
					<legend>
						<?php echo _("Subject Sets");?>
					</legend>
					<div id='resource_subject'>
					<?php
						if(count($configuration["subject"]) > 0) {
							foreach($configuration["subject"] as $subject) {
								echo "<div class='subject-record'><p><label class='ic-label'>" . _("Subject") . "<input class='ic-column' value='" . $subject['column'] . "' /></label></p>";
								echo "<p><label class='ic-label'>" . _("If delimited, delimited by") . "<input class='ic-delimiter' value='" . $subject['delimiter'] . "' /></label></p></div>";
							}
						}
						else {
							echo "<div class='subject-record'><p><label class='ic-label'>" . _("Subject") . "<input class='ic-column' value='' /></label></p>";
							echo "<p><label class='ic-label'>" . _("If delimited, delimited by") . "<input class='ic-delimiter' value='' /></label></p></div>";
						}
					?>
				</div><p><a id='add_subject' href='#'><?php echo _("+ Add another subject set");?></a></p>
			</fieldset>



				<fieldset>
					<legend>
						<?php echo _("Note Sets");?>
					</legend><div id='resource_note'>
					<?php
						if(count($configuration["note"]) > 0) {
							foreach($configuration["note"] as $note) {
								echo "<div class='note-record'><p><label class='ic-label'>" . _("Note") . "<input class='ic-column' value='" . $note['column'] . "' /></label></p>";
								echo "<p><label class='ic-label'>" . _('Note Type') . "<select class='ic-dropdown'>";
								foreach($noteTypeArray as $noteType) {
									echo "<option value='" . $noteType['noteTypeID'] . "'";
									if($note['noteType'] == $noteType['noteTypeID']) {
										echo " selected";
									}
									echo ">" . $noteType['shortName'] . "</option>";
								}
								echo "</select></label></p>";
								echo "<p><label class='ic-label'>" . _('If delimited, delimited by') . "<input class='ic-delimiter' value='" . $note['delimiter'] . "' /></label></p></div>";
							}
						}
						else {
							echo "<div class='note-record'><p><label class='ic-label'>" . _("Note") . "<input class='ic-column' value='' /></label></p>";
							echo "<p><label class='ic-label'>" . _('Note Type') . "<select class='ic-dropdown'>";
							foreach($noteTypeArray as $noteType) {
								echo "<option value='" . $noteType['noteTypeID'] . "'>" . $noteType['shortName'] . "</option>";
							}
							echo "</select></label></p>";
							echo "<p><label class='ic-label'>" . _('If delimited, delimited by') . "<input class='ic-delimiter' value='' /></label></p></div>";
						}
					?>
				</div><p><a id='add_note' href='#'><?php echo _("+ Add another note set");?></a></p></fieldset>


		</div>
	</div>
	<div id='importConfigColumnsRight'>
		<div id='ic-right-column'>

				<fieldset>
					<legend><?php echo _("Organization Sets");?></legend><div id='resource_organization'>
					<?php
						if(count($configuration["organization"]) > 0) {
							foreach($configuration["organization"] as $organization) {
								echo "<div class='organization-record'><p><label class='ic-label'>" . _("Organization") . "<input class='ic-column' value='".$organization['column']."' /></label></p><p><label class='ic-label'>" . _('Organization Role') . "<select class='ic-dropdown'>";
								foreach($organizationRoleArray as $organizationRoleID => $organizationRoleShortName) {
									echo "<option value='" . $organizationRoleID . "'";
									if($organization["organizationRole"] == $organizationRoleID) {
										echo " selected";
									}
									echo ">" . $organizationRoleShortName . "</option>";
								}
								echo "</select></label></p></div>";
							}
						}
						else {
							echo "<div class='organization-record'><p><label class='ic-label'>" . _("Organization") . "<input class='ic-column' value='' /></label></p><p><label class='ic-label'>" . _('Organization Role') . "<select class='ic-dropdown'>";
							foreach($organizationRoleArray as $organizationRoleID => $organizationRoleShortName) {
								echo "<option value='" . $organizationRoleID . "'>" . $organizationRoleShortName . "</option>";
							}
							echo "</select></label></p></div>";
						}
					?>
				</div><p><a id='add_organization' href='#'><?php echo _("+ Add another organization set");?></a></p></fieldset>



			<fieldset>
			<legend><?php echo _("Organization Name Mapping");?></legend><div id='resource_organization'>
			<p><?php echo _("Use these mappings to normalize different variations of an organization’s name to a single value. For example, you could have a publisher with three variations of their name across your import file: PublisherHouse, PublisherH, and PH. You could create a mapping for each one and normalize them all to PublisherHouse, to prevent duplicate organizations from being created. Each column that is added to an Organization set above is checked against the complete list of mappings that you create. ") . "<a id='regexLink' href='https://en.wikipedia.org/wiki/Perl_Compatible_Regular_Expressions' target='_blank'>" . _("PCRE regular expressions") . "</a>" . _(" are supported for these mappings.");?></p>
			<div id='importConfigOrgMapping'>
				<table id='org_mapping_table' >
					<tr>
						<th id="org_mapping_name"><?php echo _("Organization Name");?></th>
						<th id="org_mapping_to"><?php echo _("Will Be Mapped To");?></th>
						<th></th>
						<th></th>
					</tr>
					<?php
						if (isset($orgMappingsNamed) && $orgMappingsImported) {
							for ($i = 0; $i < count($orgMappingsNamed); $i++) {
								if ( $orgMappingsNamed[$i] && $orgMappingsImported[$i]) {
									echo "<tr><td><input class='ic-org-imported' value='" . $orgMappingsImported[$i] . "' aria-labelledby='org_mapping_name' /></td>";
									echo "<td><input class='ic-org-mapped' value='" . $orgMappingsNamed[$i] . "'  aria-labelledby='org_mapping_to' /></td>";
									echo "<td><img class='remove' src='images/cross.gif' /></td></tr>";
								}
							}
						} elseif (isset($orgMappings) ? count($orgMappings)>0 : '') {
							foreach($orgMappings as $orgMapping) {
								echo "<tr><td><input class='ic-org-imported' value='" . $orgMapping->orgNameImported . "'  aria-labelledby='org_mapping_name' /></td>";
								echo "<td><input class='ic-org-mapped' value='" . $orgMapping->orgNameMapped . "'  aria-labelledby='org_mapping_to' /></td>";
								echo "<td><img class='remove' src='images/cross.gif' /></td></tr>";
							}
						}
						else {
							echo "<tr><td><input class='ic-org-imported' /></td><td><input class='ic-org-mapped' /></td><td><img class='remove' src='images/cross.gif' /></td></tr>";
						}
					?>
				</table>
				<a id='add_mapping' href='#'><?php echo _("+ Add another mapping")?></a>
			</div>
		</fieldset>
        <fieldset>
        <legend><?php echo _("Acquisitions"); ?></legend><div id="resource_acquisitions">
        <p><label class="ic-label"><?php echo _("Fund Code");?><input id="fundCode" class="ic-column" value="<?php echo $configuration["fundCode"]?>" /></label></p>
        <p><label class="ic-label"><?php echo _("Cost");?><input id="cost" class="ic-column" value="<?php echo $configuration["cost"]?>" /></label></p>
        <p><label class="ic-label" for="orderType"><?php echo _("Order Type");?></label><select id="orderType" name="orderType">
        <?php
        $orderTypeObj = new OrderType();
        foreach ($orderTypeObj->allAsArray() as $orderType) {
            echo ('<option value="' . $orderType['orderTypeID'] . '"');
	    if ($configuration['orderTypeID'] == $orderType['orderTypeID']) {
		echo (' selected="selected"');
	    }
	    echo ('>' . $orderType['shortName'] . '</option>');
        }
        ?>
        </select>
        <p><label class="ic-label" for="currency"><?php echo _("Currency");?></label><select id="currency" name="currency">
        <?php
        $currencyObj = new Currency();
        foreach ($currencyObj->allAsArray() as $currency) {
            echo ('<option value="' . $currency['currencyCode'] . '"');
	    if ($configuration['currencyCode'] == $currency['currencyCode']) {
		echo (' selected="selected"');
	    }
	    echo ('>' . $currency['shortName'] . ' (' . $currency['currencyCode'] . ')</option>');
        }
        ?>
        </select>

        </fieldset>

	<fieldset>
        <legend><?php echo _("Workflows"); ?></legend>
	<div id="resource_workflows">
	<p><input type="checkbox" name="sendemails" id="sendemails"<?php if ($configuration['sendemails']) echo ' checked="checked"'; ?>> <label for="sendemails"><?php echo _("Send emails when starting workflows?"); ?></label></p>
	</div>
	</fieldset>

		</div>
	</div>
	<div style='clear: both;'></div>
</div>
<script type='text/javascript'>
	$(".remove").click(function () {
	    $(this).parent().parent().fadeTo(400, 0, function () {
			$(this).remove();
	    });
	});
   $('#add_alias').click(function (e) {
   		e.preventDefault();
   		$('#resource_alias').append(
   			"<div class='alias-record'><p><label class='ic-label'><?php echo _('Alias');?><input class='ic-column' value='' /></label></p><p><label class='ic-label'><?php echo _('Alias Type');?><select class='ic-dropdown'><?php echo $aliasOptions?></select></label></p><p><label class='ic-label'><?php echo _('If delimited, delimited by');?><input class='ic-delimiter' value='' /></label></p></div>"
   		);
   });
   $('#add_parent').click(function (e) {
   		e.preventDefault();
   		$('#resource_parent').append (
   			"<p><label class='ic-label'><?php echo _('Parent Resource');?><input class='ic-column' value='' /></label></p>"
   		);
   });
   $('#add_isbnorissn').click(function (e) {
   		e.preventDefault();
   		$('#resource_isbnOrIssn').append (
   			"<div class='isbnOrIssn-record'><p><label class='ic-label'><?php echo _('ISBN or ISSN');?><input class='ic-column' value='' /></label></p><p><label class='ic-label'><?php echo _('If delimited, delimited by');?><input class='ic-delimiter' value='' /></label></p><p><label class='ic-dedupe'><input class='ic-dedupe' type='checkbox' /><?php echo _('Dedupe on this column');?></label></p></div>"
   		);
   });
   $('#add_subject').click(function (e) {
   		e.preventDefault();
   		$('#resource_subject').append(
   			"<div class='subject-record'><p><label class='ic-label'><?php echo _('Subject');?><input class='ic-column' value='' /></label></p><p><label class='ic-label'><?php echo _('If delimited, delimited by');?><input class='ic-delimiter' value='' /></label></p></div>"
   		);
   });
   $('#add_note').click(function (e) {
   		e.preventDefault();
   		$('#resource_note').append (
			"<div class='note-record'><p><label class='ic-label'><?php echo _('Note');?><input class='ic-column' value='' /></label></p><p><label class='ic-label'><?php echo _('Note Type');?><select class='ic-dropdown'><?php echo $noteOptions?></select></label></p><p><label class='ic-label'><?php echo _('If delimited, delimited by');?><input class='ic-delimiter' value='' /></label></p></div>"
   		);
   });
   $('#add_organization').click(function (e) {
   		e.preventDefault();
   		$('#resource_organization').append (
			"<div class='organization-record'><p><label class='ic-label'><?php echo _('Organization');?><input class='ic-column' value='' /></label></p><p><label class='ic-label'><?php echo _('Organization Role');?><select class='ic-dropdown'><?php echo $organizationOptions?></select></label></p></div>"
   		);
   });
   $('#add_mapping').click(function (e) {
   		e.preventDefault();
   		$('#org_mapping_table').append (
   			"<tr><td><input class='ic-org-imported' aria-labelledby='org_mapping_name' /></td><td><input class='ic-org-mapped' aria-labelledby='org_mapping_to' /></td><td><img class='remove' src='images/cross.gif' /></td></tr>"
   		);
   });
</script>
