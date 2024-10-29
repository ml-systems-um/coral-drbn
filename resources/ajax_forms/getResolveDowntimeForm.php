<?php
$downtimeID = is_numeric($_GET['downtimeID']) ? $_GET['downtimeID']:null;

if ($downtimeID) {
	$downtime = new Downtime(new NamedArguments(array('primaryKey' => $downtimeID)));

?>
<form id="resolveDowntimeForm">
	<input name="downtimeID" type="hidden" value="<?php echo $downtime->downtimeID;?>" />
	<table class="thickboxTable" style="width:98%;background-image:url('images/title.gif');background-repeat:no-repeat;">
		<tr>
			<td colspan="2">
				<h1><?php echo _('Resolve Downtime'); ?></h1>
			</td>
		</tr>
		<tr>
			<td>
				<h2><?php echo _('Downtime Resolution:'); ?></h2>
			</td>
			<td>
				<div>
					<div><label for="endDate"><i><?php echo _('Date'); ?></i></label></div>
					<input class="date-pick" type="text" name="endDate" id="endDate" aria-describedby="span_error_endDate" />
					<span id='span_error_endDate' class='error updateDowntimeError'></span>
				</div>
				<div style="clear:both;">
					<div><label for="endDate"><i><?php echo _('Time'); ?></i></label></div>
<?php
echo buildTimeForm("endTime");
?>
					<span id='span_error_endDate' class='error updateDowntimeError'></span>
				</div>
			</td>
		</tr>
		<tr>
			<td><label for="note"><?php echo _('Note:'); ?></label></td>
			<td>
				<textarea id="note" name="note"><?php echo $downtime->note;?></textarea>
			</td>
		</tr>
	</table>
	<table class='noBorderTable' style='width:125px;'>
		<tr>
			<td style='text-align:left'><input type='button' value='submit' name='submitUpdatedDowntime' id='submitUpdatedDowntime'></td>
			<td style='text-align:right'><input type='button' value='cancel' onclick="myCloseDialog()"></td>
		</tr>
	</table>
</form>
<?php
} else {
?>
		<div>
			<?php echo _('Unable to retrieve Downtime.'); ?>
		</div>
		<table class='noBorderTable' style='width:125px;'>
			<tr>
				<td style='text-align:right'><input type='button' value='cancel' onclick="myCloseDialog()"></td>
			</tr>
		</table>
<?php
}
