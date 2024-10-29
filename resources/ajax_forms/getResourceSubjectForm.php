<?php
	$resourceID = $_GET['resourceID'];
	$generalSubject = new GeneralSubject();
	$generalSubjectArray = $generalSubject->allAsArray();
?>
		<div id='div_updateForm'>
		<div class='formTitle'><h2 class='headerText'><?php echo _("Add General / Detail Subject Link");?></h2></div>

	<?php
		if (count($generalSubjectArray) > 0){
			?>
			<table class='linedDataTable table-border table-striped'>
				<thead>
				<tr>
				<th scope="col"><?php echo _("General Subject Name");?></th>
				<th scope="col"><?php echo _("Detail Subject Name");?></th>
				<th scope="col"><?php echo _("Actions");?></th>
				</tr>
				</thead>
				<tbody>
				<?php

				foreach($generalSubjectArray as $ug) {
					$generalSubject = new GeneralSubject(new NamedArguments(array('primaryKey' => $ug['generalSubjectID'])));

					echo "<tr>";
					echo "<th scope='row'>" . $generalSubject->shortName . "</th>";
					echo "<td></td>";
					echo "<td class='actions'><a href='javascript:void(0);' class='resourcesSubjectLink' resourceID='" . $resourceID . " 'generalSubjectID='" . $ug['generalSubjectID'] . " 'detailSubjectID='" . -1 . "'><input class='add-button' type='button' title='"._("add")."' value='"._("Add")."'/></a></td>";

					foreach ($generalSubject->getDetailedSubjects() as $detailedSubjects){
						echo "<tr>";
						echo "<td></td>";
						echo "<td>";
						echo $detailedSubjects->shortName . "</td>";
						echo "<td class='actions'><a href='javascript:void(0);' class='resourcesSubjectLink' resourceID='" . $resourceID . " 'generalSubjectID='" . $ug['generalSubjectID'] . " 'detailSubjectID='" . $detailedSubjects->detailedSubjectID . "'><input class='add-button' type='button' title='"._("add")."' value='"._("Add")."'/></a></td>";
						echo "</tr>";
					}
					echo "</tr>";
				}

				?>
			</tbody>	
			</table>
			<?php

		}else{
			echo "<p>". _("(none found)")."</p>";
		}
		?>

		<p><input type='button' value='<?php echo _("cancel");?>' onclick="myCloseDialog(); return false;" class='cancel-button'></p>
		</div>

		<script type="text/javascript" src="js/forms/resourceSubject.js?random=<?php echo rand(); ?>"></script>
		<!-- <script type="text/javascript">document.getElementById("div_updateForm").className="modalScroll";</script> -->
