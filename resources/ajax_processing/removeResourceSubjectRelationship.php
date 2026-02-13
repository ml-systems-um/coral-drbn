<?php
		$generalDetailSubjectID = $_GET['generalDetailSubjectID'];
		$resourceID = $_GET['resourceID'];
		$generalDetailRow = new GeneralDetailSubjectLink(new NamedArguments(array('primaryKey' => $generalDetailSubjectID)));
		$generalSubjectID = $generalDetailRow->generalSubjectID;
		$generalSubject = new GeneralSubject(new NamedArguments(array('primaryKey' => $generalSubjectID)));
		$resource = new Resource(new NamedArguments(array('primaryKey' => $resourceID)));
		$removeSubjectCompletely = ($resource->getSubjectCountByGeneral($generalSubjectID) == 1);
		$resourceSubject = new ResourceSubject(new NamedArguments(array('primaryKey' => $resourceID)));

		$callType = ($removeSubjectCompletely) ? 'Delete' : 'Update';
		$postData = [
			'CallType' => $callType,
			'Librarian' => $loginID,
			'SubjectName' => $generalSubject->shortName,
			'DBID' => $resource->AZDBID,
		];
		if($callType == 'update'){
			//Are we removing the Preferred? Set the Recommended Value based on what we're deleting.
			$removePreferred = ($generalDetailRow->detailedSubjectID == 4);
			if($removePreferred){
				$postData['Recommended'] = FALSE;
			}
		}
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
		try {

			$resourceSubject->removeResourceSubject($resourceID, $generalDetailSubjectID);
			echo "<span class='success'>";
			echo _("Subject successfully removed.");
			echo "</span>";
		} catch (Exception $e) {
			echo "<span class='error'>";
			echo $e->getMessage();
			echo "</span>";
		}

?>
