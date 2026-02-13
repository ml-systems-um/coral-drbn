<?php

		$resourceSubject = new ResourceSubject();

		$resourceID = $_GET['resourceID'];
		$generalSubjectID = $_GET['generalSubjectID'];
		$detailSubjectID = $_GET['detailSubjectID'];

$resource = new Resource(new NamedArguments(array('primaryKey' => $resourceID)));
$brandNewSubject = ($resource->getSubjectCountByGeneral($generalSubjectID) == 0);
$generalSubject = new GeneralSubject(new NamedArguments(array('primaryKey' => $generalSubjectID)));
$addingRecommended = ($detailSubjectID == 4);

//If it's not a brand new Subject and we're not adding recommended, leave the resource recommended in the A-Z list.
if(!$brandNewSubject && !$addingRecommended){$addingRecommended = TRUE;}
$callType = ($brandNewSubject) ? 'Insert' : 'Update';
$postData = [
	'CallType' => $callType,
	'Librarian' => $loginID,
	'SubjectName' => $generalSubject->shortName,
	'Recommended' => $addingRecommended,
	'DBID' => $resource->AZDBID,
];
		if (!isset($detailSubjectID)) {
			$detailSubjectID = -1;
		}

		if (!isset($generalSubjectID)) {
			$generalSubjectID = -1;
		}

		$generalDetailSubjectLink = new GeneralDetailSubjectLink();
		$generalDetailSubjectLinkID = $generalDetailSubjectLink->getGeneralDetailID($generalSubjectID, $detailSubjectID);

		$resourceSubject->resourceID = $resourceID;
		$resourceSubject->generalDetailSubjectLinkID = $generalDetailSubjectLinkID;

		// Check to see if the subject has already been associated with the resouce.  If not then save.
		if ($resourceSubject->duplicateCheck($resourceID, $generalDetailSubjectLinkID) == 0) {
			try {
				//Before updating CORAL, pass a CURL call to the A-Z list with the removal Instructions.
				$url = 'https://devg.lib.umd.umich.edu/scripts/coral/subjectsAPI.php';
				$ch = curl_init();
				// Set cURL options
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				// Execute cURL session
				$response = curl_exec($ch);
				$resourceSubject->save();
			} catch (Exception $e) {
				echo "<span class='error'>";
				echo $e->getMessage();
				echo "</span>";
			}
		}

?>
