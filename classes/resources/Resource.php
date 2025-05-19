<?php

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
namespace resources;
class Resource extends \common\DatabaseObject {
	/* Public Functions */

    public function asArray() {
		$rarray = array();
		foreach (array_keys($this->attributeNames) as $attributeName) {
			if ($this->$attributeName != null) {
				$rarray[$attributeName] = $this->$attributeName;
			}
		}

        $status = new Status(new NamedArguments(array('primaryKey' => $this->statusID)));
        $rarray['status'] = $status->shortName;

        if ($this->resourceTypeID) {
            $resourceType = new ResourceType(new NamedArguments(array('primaryKey' => $this->resourceTypeID)));
            $rarray['resourceType'] = $resourceType->shortName;
        }

        if ($this->resourceFormatID) {
            $resourceFormat = new ResourceFormat(new NamedArguments(array('primaryKey' => $this->resourceFormatID)));
            $rarray['resourceFormat'] = $resourceFormat->shortName;
        }

        if ($this->acquisitionTypeID) {
            $acquisitionType = new AcquisitionType(new NamedArguments(array('primaryKey' => $this->acquisitionTypeID)));
            $rarray['acquisitionType'] = $acquisitionType->shortName;
        }

		$identifiers = $this->getIsbnOrIssn();
		$rarray['isbnOrIssn'] = array();
		foreach ($identifiers as $identifier) {
				array_push($rarray['isbnOrIssn'], $identifier->isbnOrIssn);
		}

        $aliases = $this->getAliases();
        $rarray['aliases'] = array();
		foreach ($aliases as $alias) {
				array_push($rarray['aliases'], $alias->shortName);
		}

		return $rarray;


    }

	public function countResourceAcquisitions() {
        $query = "SELECT COUNT(*) AS count FROM ResourceAcquisition WHERE resourceID = " . $this->resourceID;
		$result = $this->db->processQuery($query, 'assoc');
        return ($result) ? $result['count'] : 0;
    }

	//returns array based on search for excel output (export.php)
	public function export($whereAdd, $orderBy) {

		$config = new Configuration();

		if ($config->settings->organizationsModule == 'Y') {
			$dbName = $config->settings->organizationsDatabaseName;

			$orgJoinAdd = "LEFT JOIN " . $dbName . ".Organization O ON O.organizationID = ROL.organizationID
							 LEFT JOIN " . $dbName . ".Alias OA ON OA.organizationID = ROL.organizationID";

			$orgSelectAdd = "GROUP_CONCAT(DISTINCT O.name ORDER BY O.name DESC SEPARATOR '; ') organizationNames";
		}else{
			$orgJoinAdd = "LEFT JOIN Organization O ON O.organizationID = ROL.organizationID";

			$orgSelectAdd = "GROUP_CONCAT(DISTINCT O.shortName ORDER BY O.shortName DESC SEPARATOR '; ') organizationNames";
		}


		$licSelectAdd = '';
		$licJoinAdd = '';
		if ($config->settings->licensingModule == 'Y') {
			$dbName = $config->settings->licensingDatabaseName;

			$licJoinAdd = " LEFT JOIN ResourceLicenseLink RLL ON RLL.resourceAcquisitionID = RA.resourceAcquisitionID
							LEFT JOIN " . $dbName . ".License L ON RLL.licenseID = L.licenseID
							LEFT JOIN ResourceLicenseStatus RLS ON RLS.resourceAcquisitionID = RA.resourceAcquisitionID
							LEFT JOIN LicenseStatus LS ON LS.licenseStatusID = RLS.licenseStatusID";

			$licSelectAdd = "GROUP_CONCAT(DISTINCT L.shortName ORDER BY L.shortName DESC SEPARATOR '; ') licenseNames,
							GROUP_CONCAT(DISTINCT LS.shortName, ': ', DATE_FORMAT(RLS.licenseStatusChangeDate, '" . return_date_format() . "') ORDER BY RLS.licenseStatusChangeDate DESC SEPARATOR '; ') licenseStatuses, ";

		}


		$status = new Status();
		//also add to not retrieve saved records
		$savedStatusID = intval($status->getIDFromName('saved'));
		$whereAdd[] = "R.statusID != " . $savedStatusID;

		if (is_array($whereAdd) && count($whereAdd) > 0) {
			$whereStatement = " WHERE " . implode(" AND ", $whereAdd);
		}else{
			$whereStatement = "";
		}


		//now actually execute query
		$query = "SELECT R.resourceID, R.titleText, AT.shortName acquisitionType, CONCAT_WS(' ', CU.firstName, CU.lastName) createName,
						R.createDate createDate, CONCAT_WS(' ', UU.firstName, UU.lastName) updateName,
						R.updateDate updateDate, S.shortName status,
						RT.shortName resourceType, RF.shortName resourceFormat, RA.orderNumber, RA.systemNumber, R.resourceURL, R.resourceAltURL,
						RA.subscriptionStartDate, RA.subscriptionEndDate, RA.subscriptionAlertEnabledInd, AUT.shortName authenticationType,
						AM.shortName accessMethod, SL.shortName storageLocation, UL.shortName userLimit, RA.authenticationUserName,
						RA.authenticationPassword, RA.coverageText, CT.shortName catalogingType, CS.shortName catalogingStatus, RA.recordSetIdentifier, RA.bibSourceURL,
						RA.numberRecordsAvailable, RA.numberRecordsLoaded, RA.hasOclcHoldings, GROUP_CONCAT(DISTINCT I.isbnOrIssn ORDER BY isbnOrIssnID SEPARATOR '; ') AS isbnOrIssn,
                        RPAY.year,
                        F.shortName as fundName, F.fundCode,
                        FORMAT(COALESCE(RPAY.priceTaxExcluded, 0) / 100, " . return_number_decimals() . " ," . return_sql_locale()  . ") as priceTaxExcluded,
                        FORMAT(COALESCE(RPAY.taxRate, 0) / 100, " . return_number_decimals() . " ," . return_sql_locale()  . ") as taxRate,
                        FORMAT(COALESCE(RPAY.priceTaxIncluded, 0) / 100, " . return_number_decimals() . " ," . return_sql_locale()  . ") as priceTaxIncluded,
                        FORMAT(COALESCE(RPAY.paymentAmount, 0) / 100, " . return_number_decimals() . " ," . return_sql_locale()  . ") as paymentAmount,
                        RPAY.currencyCode, CD.shortName as costDetails, OT.shortName as orderType, RPAY.costNote, RPAY.invoiceNum,
						" . $orgSelectAdd . ",
						" . $licSelectAdd . "
						GROUP_CONCAT(DISTINCT A.shortName ORDER BY A.shortName DESC SEPARATOR '; ') aliases,
						GROUP_CONCAT(DISTINCT PS.shortName ORDER BY PS.shortName DESC SEPARATOR '; ') purchasingSites,
						GROUP_CONCAT(DISTINCT AUS.shortName ORDER BY AUS.shortName DESC SEPARATOR '; ') authorizedSites,
						GROUP_CONCAT(DISTINCT ADS.shortName ORDER BY ADS.shortName DESC SEPARATOR '; ') administeringSites,
						GROUP_CONCAT(DISTINCT RP.titleText ORDER BY RP.titleText DESC SEPARATOR '; ') parentResources,
						GROUP_CONCAT(DISTINCT RC.titleText ORDER BY RC.titleText DESC SEPARATOR '; ') childResources
								FROM Resource R
                                    LEFT JOIN ResourceAcquisition RA ON RA.resourceID = R.resourceID
									LEFT JOIN ResourcePayment RPAY ON RA.resourceAcquisitionID = RPAY.resourceAcquisitionID
									LEFT JOIN Alias A ON R.resourceID = A.resourceID
									LEFT JOIN ResourceOrganizationLink ROL ON R.resourceID = ROL.resourceID
									" . $orgJoinAdd . "
									LEFT JOIN ResourceRelationship RRC ON RRC.relatedResourceID = R.resourceID
									LEFT JOIN ResourceRelationship RRP ON RRP.resourceID = R.resourceID
									LEFT JOIN Resource RC ON RC.resourceID = RRC.resourceID
									LEFT JOIN ResourceSubject RSUB ON R.resourceID = RSUB.resourceID
									LEFT JOIN Resource RP ON RP.resourceID = RRP.relatedResourceID
									LEFT JOIN GeneralDetailSubjectLink GDLINK ON RSUB.generalDetailSubjectLinkID = GDLINK.generalDetailSubjectLinkID
									LEFT JOIN ResourceFormat RF ON R.resourceFormatID = RF.resourceFormatID
									LEFT JOIN ResourceType RT ON R.resourceTypeID = RT.resourceTypeID
									LEFT JOIN AcquisitionType AT ON RA.acquisitionTypeID = AT.acquisitionTypeID
									LEFT JOIN ResourceStep RS ON RA.resourceAcquisitionID = RS.resourceAcquisitionID
									LEFT JOIN Fund F ON RPAY.fundID = F.fundID
									LEFT JOIN OrderType OT ON RPAY.orderTypeID = OT.orderTypeID
									LEFT JOIN CostDetails CD ON RPAY.costDetailsID = CD.costDetailsID
									LEFT JOIN Status S ON R.statusID = S.statusID
									LEFT JOIN ResourceNote RN ON R.resourceID = RN.entityID
									LEFT JOIN NoteType NT ON RN.noteTypeID = NT.noteTypeID
									LEFT JOIN User CU ON R.createLoginID = CU.loginID
									LEFT JOIN User UU ON R.updateLoginID = UU.loginID
									LEFT JOIN CatalogingStatus CS ON RA.CatalogingStatusID = CS.catalogingStatusID
									LEFT JOIN CatalogingType CT ON RA.catalogingTypeID = CT.catalogingTypeID
									LEFT JOIN ResourcePurchaseSiteLink RPSL ON RA.resourceAcquisitionID = RPSL.resourceAcquisitionID
									LEFT JOIN PurchaseSite PS ON RPSL.purchaseSiteID = PS.purchaseSiteID
									LEFT JOIN ResourceAuthorizedSiteLink RAUSL ON RA.resourceAcquisitionID = RAUSL.resourceAcquisitionID
									LEFT JOIN AuthorizedSite AUS ON RAUSL.authorizedSiteID = AUS.authorizedSiteID
									LEFT JOIN ResourceAdministeringSiteLink RADSL ON RA.resourceAcquisitionID = RADSL.resourceAcquisitionID
									LEFT JOIN AdministeringSite ADS ON RADSL.administeringSiteID = ADS.administeringSiteID
									LEFT JOIN AuthenticationType AUT ON AUT.authenticationTypeID = RA.authenticationTypeID
									LEFT JOIN AccessMethod AM ON AM.accessMethodID = RA.accessMethodID
									LEFT JOIN StorageLocation SL ON SL.storageLocationID = RA.storageLocationID
									LEFT JOIN UserLimit UL ON UL.userLimitID = RA.userLimitID
									LEFT JOIN IsbnOrIssn I ON I.resourceID = R.resourceID
									" . $licJoinAdd . "
								" . $whereStatement . "
                GROUP BY
                  RPAY.resourcePaymentID,
                  R.resourceID,
                  AT.shortName,
                  RA.orderNumber,
                  RA.systemNumber,
                  RA.subscriptionAlertEnabledInd,
                  AUT.shortName,
                  AM.shortName,
                  SL.shortName,
                  UL.shortName,
                  RA.authenticationUserName,
                  RA.authenticationPassword,
                  RA.coverageText,
                  CT.shortName,
                  CS.shortName,
                  RA.recordSetIdentifier,
                  RA.bibSourceURL,
                  RA.numberRecordsAvailable,
                  RA.numberRecordsLoaded,
                  RA.hasOclcHoldings
								ORDER BY " . $orderBy;
		$result = $this->db->processQuery(stripslashes($query), 'assoc');

		$searchArray = array();

		//need to do this since it could be that there's only one result and this is how the dbservice returns result
		if (isset($result['resourceID'])) { $result = [$result]; }
		foreach ($result as $row) {
			array_push($searchArray, $row);
		}

		return $searchArray;
	}

	//returns array of alias objects
	public function getAliases() {

		$query = "SELECT * FROM Alias WHERE resourceID = '" . $this->resourceID . "' order by shortName";

		$result = $this->db->processQuery($query, 'assoc');

		$objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['aliasID'])) { $result = [$result]; }
		foreach ($result as $row) {
			$object = new Alias(new NamedArguments(array('primaryKey' => $row['aliasID'])));
			array_push($objects, $object);
		}

		return $objects;
	}

	// TODO: i18n; remove other articles
	//used for A-Z on search (index)
	public function getAlphabeticalList() {
		$alphArray = array();
		$result = $this->db->query("SELECT DISTINCT UPPER(SUBSTR(TRIM(LEADING 'The ' FROM titleText),1,1)) letter, COUNT(SUBSTR(TRIM(LEADING 'The ' FROM titleText),1,1)) letter_count
								FROM Resource R
								GROUP BY UPPER(SUBSTR(TRIM(LEADING 'The ' FROM titleText),1,1))
								ORDER BY 1;");

		while ($row = $result->fetch_assoc()) {
			$alphArray[$row['letter']] = $row['letter_count'];
		}

		return $alphArray;
	}	

	//returns array of child resource objects
	public function getChildResources() {
		return $this->getRelatedResources('relatedResourceID');
	}

	//returns array of contact objects
	public function getCreatorsArray() {

		$creatorsArray = array();

		//get resource specific creators
		$query = "SELECT distinct loginID, firstName, lastName
			FROM Resource R, User U
			WHERE U.loginID = R.createLoginID
			ORDER BY lastName, firstName, loginID";

		$result = $this->db->processQuery($query, 'assoc');

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['loginID'])){ $result = [$result] ;}
		foreach ($result as $row) {
			array_push($creatorsArray, $row);
		}

		return $creatorsArray;
	}

	//returns array of subject objects
	public function getDetailedSubjects($resourceID, $generalSubjectID) {

		$query = "SELECT
				RSUB.resourceID,
				GDL.detailedSubjectID,
				DetailedSubject.shortName,
				GDL.generalSubjectID
			FROM
				ResourceSubject RSUB
				INNER JOIN GeneralDetailSubjectLink GDL ON (RSUB.GeneralDetailSubjectLinkID = GDL.GeneralDetailSubjectLinkID)
				INNER JOIN DetailedSubject ON (GDL.detailedSubjectID = DetailedSubject.detailedSubjectID)
			WHERE
				RSUB.resourceID = " . $resourceID . " AND GDL.generalSubjectID = " . $generalSubjectID . " ORDER BY DetailedSubject.shortName";

		//echo $query . "<br>";

		$result = $this->db->processQuery($query, 'assoc');

		$objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['detailedSubjectID'])) { $result = [$result]; }
		foreach ($result as $row) {
			$object = new DetailedSubject(new NamedArguments(array('primaryKey' => $row['detailedSubjectID'])));
			array_push($objects, $object);
		}

		return $objects;
	}

	//gets an array of distinct organizations set up for this resource (organizationID, organization)
	public function getDistinctOrganizationArray() {
		$config = new Configuration;

		//if the org module is installed get the org name from org database
		if ($config->settings->organizationsModule == 'Y') {
			$dbName = $config->settings->organizationsDatabaseName;

			$resourceOrgArray = array();

			$query = "SELECT DISTINCT organizationID FROM ResourceOrganizationLink WHERE resourceID = '" . $this->resourceID . "'";

			$result = $this->db->processQuery($query, 'assoc');

			$objects = array();

			//need to do this since it could be that there's only one request and this is how the dbservice returns result
			if (isset($result['organizationID'])) {
				$orgArray = array();

				//first, get the organization name
				$query = "SELECT name FROM " . $dbName . ".Organization WHERE organizationID = " . $result['organizationID'];

				if ($orgResult = $this->db->query($query)) {
					while ($orgRow = $orgResult->fetch_assoc()) {
						$orgArray['organization'] = $orgRow['name'];
						$orgArray['organizationID'] = $result['organizationID'];
					}
				}

				array_push($resourceOrgArray, $orgArray);
			}else{
				foreach ($result as $row) {

					$orgArray = array();

					//first, get the organization name
					$query = "SELECT DISTINCT name FROM " . $dbName . ".Organization WHERE organizationID = " . $row['organizationID'];

					if ($orgResult = $this->db->query($query)) {
						while ($orgRow = $orgResult->fetch_assoc()) {
							$orgArray['organization'] = $orgRow['name'];
							$orgArray['organizationID'] = $row['organizationID'];
						}
					}

					array_push($resourceOrgArray, $orgArray);

				}

			}






		//otherwise if the org module is not installed get the org name from this database
		}else{



			$resourceOrgArray = array();

			$query = "SELECT DISTINCT organizationID FROM ResourceOrganizationLink WHERE resourceID = '" . $this->resourceID . "'";

			$result = $this->db->processQuery($query, 'assoc');

			$objects = array();

			//need to do this since it could be that there's only one request and this is how the dbservice returns result
			if (isset($result['organizationID'])) {
				$orgArray = array();

				//first, get the organization name
				$query = "SELECT DISTINCT shortName FROM Organization WHERE organizationID = " . $result['organizationID'];

				if ($orgResult = $this->db->query($query)) {
					while ($orgRow = $orgResult->fetch_assoc()) {
						$orgArray['organization'] = $orgRow['shortName'];
						$orgArray['organizationID'] = $result['organizationID'];
					}
				}

				array_push($resourceOrgArray, $orgArray);
			}else{
				foreach ($result as $row) {

					$orgArray = array();

					//first, get the organization name
					$query = "SELECT DISTINCT shortName FROM Organization WHERE organizationID = " . $row['organizationID'];

					if ($orgResult = $this->db->query($query)) {
						while ($orgRow = $orgResult->fetch_assoc()) {
							$orgArray['organization'] = $orgRow['shortName'];
							$orgArray['organizationID'] = $row['organizationID'];
						}
					}

					array_push($resourceOrgArray, $orgArray);

				}

			}





		}


		return $resourceOrgArray;
	}	

	public function getExportableDowntimes($archivedOnly=false) {
		$result = $this->getDownTimeResults($archivedOnly);

		$objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['downtimeID'])) {
			return array($result);
		} else {
			return $result;
		}
	}

	public function getExportableIssues($archivedOnly=false){
		if ($this->db->config->settings->organizationsModule == 'Y' && $this->db->config->settings->organizationsDatabaseName) {
			$contactsDB = $this->db->config->settings->organizationsDatabaseName;
		} else {
			$contactsDB = $this->db->config->database->name;
		}

		$query = "SELECT i.*,(SELECT GROUP_CONCAT(CONCAT('=HYPERLINK(\"mailto:',sc.emailAddress,'\",\"',COALESCE(sc.name,sc.emailAddress),'\")') SEPARATOR ', ')
								FROM IssueContact sic
								LEFT JOIN `{$contactsDB}`.Contact sc ON sc.contactID=sic.contactID
								WHERE sic.issueID=i.issueID) AS `contacts`,
							 (SELECT GROUP_CONCAT(se.titleText SEPARATOR ', ')
								FROM IssueRelationship sir
								LEFT JOIN Resource se ON (se.resourceID=sir.entityID AND sir.entityTypeID=2)
								WHERE sir.issueID=i.issueID) AS `appliesto`,
							 (SELECT GROUP_CONCAT(sie.email SEPARATOR ', ')
								FROM IssueEmail sie
								WHERE sie.issueID=i.issueID) AS `CCs`
					FROM Issue i
					LEFT JOIN IssueRelationship ir ON ir.issueID=i.issueID
					WHERE ir.entityID='{$this->resourceID}' AND ir.entityTypeID=2";
		if ($archivedOnly) {
			$query .= " AND i.dateClosed IS NOT NULL";
		} else {
			$query .= " AND i.dateClosed IS NULL";
		}
		$query .= "	ORDER BY i.dateCreated DESC";

		$result = $this->db->processQuery($query, 'assoc');

		$objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['issueID'])) {
			return array($result);
		}else{
			return $result;
		}
	}

	//returns array of external login records
	public function getExternalLoginArray() {

		$config = new Configuration;
		$elArray = array();

		//get resource specific accounts first
		$query = "SELECT EL.*,  ELT.shortName externalLoginType
				FROM ExternalLogin EL, ExternalLoginType ELT
				WHERE EL.externalLoginTypeID = ELT.externalLoginTypeID
				AND resourceID = '" . $this->resourceID . "'
				ORDER BY ELT.shortName;";

		$result = $this->db->processQuery($query, 'assoc');

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['externalLoginID'])){ $result = [$result]; }
		foreach ($result as $row) {
			array_push($elArray, $row);
		}

		//if the org module is installed also get the external logins from org database
		if ($config->settings->organizationsModule == 'Y') {
			$dbName = $config->settings->organizationsDatabaseName;

			$query = "SELECT DISTINCT EL.*, ELT.shortName externalLoginType, O.name organizationName
						FROM " . $dbName . ".ExternalLogin EL, " . $dbName . ".ExternalLoginType ELT, " . $dbName . ".Organization O,
							Resource R, ResourceOrganizationLink ROL
						WHERE EL.externalLoginTypeID = ELT.externalLoginTypeID
						AND R.resourceID = ROL.resourceID
						AND ROL.organizationID = EL.organizationID
						AND O.organizationID = EL.organizationID
						AND R.resourceID = '" . $this->resourceID . "'
						ORDER BY ELT.shortName;";

			$result = $this->db->processQuery($query, 'assoc');

			//need to do this since it could be that there's only one request and this is how the dbservice returns result
			if (isset($result['externalLoginID'])){ $result = [$result]; }
			foreach ($result as $row) {
				array_push($elArray, $row);
			}

		}
		return $elArray;
	}

	//returns array of externalLogin objects
	public function getExternalLogins() {

		$query = "SELECT * FROM ExternalLogin
					WHERE resourceID = '" . $this->resourceID . "'";

		$result = $this->db->processQuery($query, 'assoc');

		$objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['externalLoginID'])){ $result = [$result]; }
		foreach ($result as $row) {
			$object = new ExternalLogin(new NamedArguments(array('primaryKey' => $row['externalLoginID'])));
			array_push($objects, $object);
		}

		return $objects;
	}

	//returns array of subject objects
	public function getGeneralDetailSubjectLinkID() {

		$query = "SELECT
					GDL.generalDetailSubjectLinkID
				FROM
					Resource R
					INNER JOIN ResourceSubject RSUB ON (R.resourceID = RSUB.resourceID)
					INNER JOIN GeneralDetailSubjectLink GDL ON (RSUB.generalDetailSubjectLinkID = GDL.generalDetailSubjectLinkID)
					LEFT OUTER JOIN GeneralSubject GS ON (GDL.generalSubjectID = GS.generalSubjectID)
					LEFT OUTER JOIN DetailedSubject DS ON (GDL.detailedSubjectID = DS.detailedSubjectID)
				WHERE
					R.resourceID = '" . $this->resourceID . "'
				ORDER BY
					GS.shortName,
					DS.shortName";


		$result = $this->db->processQuery($query, 'assoc');

		$objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['generalDetailSubjectLinkID'])) { $result = [$result]; }
		foreach ($result as $row) {
			$object = new GeneralDetailSubjectLink(new NamedArguments(array('primaryKey' => $row['generalDetailSubjectLinkID'])));
			array_push($objects, $object);
		}

		return $objects;
	}

	//returns array of the initial note object
	public function getInitialNote() {
		$noteType = new NoteType();

		$query = "SELECT * FROM ResourceNote RN
					WHERE entityID = '" . $this->resourceID . "'
					AND noteTypeID = '" . $noteType->getInitialNoteTypeID() . "'
					ORDER BY noteTypeID desc LIMIT 0,1";


		$result = $this->db->processQuery($query, 'assoc');

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['resourceNoteID'])) {
			return new ResourceNote(new NamedArguments(array('primaryKey' => $result['resourceNoteID'])));
		} else{
			return new ResourceNote();
		}
	}

	public function getIsbnOrIssn() {
		$query = "SELECT *
			FROM IsbnOrIssn
			WHERE resourceID = '" . $this->resourceID . "'
			ORDER BY 1";

		$result = $this->db->processQuery($query, 'assoc');

		$objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['isbnOrIssnID'])) { $result = [$result]; }
		foreach ($result as $row) {
			$object = new IsbnOrIssn(new NamedArguments(array('primaryKey' => $row['isbnOrIssnID'])));
			array_push($objects, $object);
		}

		return $objects;
	}

	//returns array of notes objects
	public function getNotes($tabName = NULL) {

		if ($tabName) {
			$query = "SELECT * FROM ResourceNote RN
						WHERE entityID = '" . $this->resourceID . "'
						AND UPPER(tabName) = UPPER('" . $tabName . "')
						ORDER BY updateDate desc";
		}else{
			$query = "SELECT RN.*
						FROM ResourceNote RN
						LEFT JOIN NoteType NT ON NT.noteTypeID = RN.noteTypeID
						WHERE entityID = '" . $this->resourceID . "'
						ORDER BY updateDate desc, NT.shortName";
		}

		$result = $this->db->processQuery($query, 'assoc');

		$objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['resourceNoteID'])) { $result = [$result]; }
		foreach ($result as $row) {
			$object = new ResourceNote(new NamedArguments(array('primaryKey' => $row['resourceNoteID'])));
			array_push($objects, $object);
		}

		return $objects;
	}

	//gets an array of organizations set up for this resource (organizationID, organization, organizationRole)
	public function getOrganizationArray() {
		$config = new Configuration;

		//if the org module is installed get the org name from org database
		if ($config->settings->organizationsModule == 'Y') {
			$dbName = $config->settings->organizationsDatabaseName;

			$resourceOrgArray = array();

			$query = "SELECT * FROM ResourceOrganizationLink WHERE resourceID = '" . $this->resourceID . "'";

			$result = $this->db->processQuery($query, 'assoc');

			$objects = array();

			//need to do this since it could be that there's only one request and this is how the dbservice returns result
			if (isset($result['organizationID'])) {
				$orgArray = array();

				//first, get the organization name
				$query = "SELECT name FROM " . $dbName . ".Organization WHERE organizationID = " . $result['organizationID'];

				if ($orgResult = $this->db->query($query)) {
					while ($orgRow = $orgResult->fetch_assoc()) {
						$orgArray['organization'] = $orgRow['name'];
						$orgArray['organizationID'] = $result['organizationID'];
					}
				}

				//then, get the role name
				$query = "SELECT * FROM " . $dbName . ".OrganizationRole WHERE organizationRoleID = " . $result['organizationRoleID'];

				if ($orgResult = $this->db->query($query)) {
					while ($orgRow = $orgResult->fetch_assoc()) {
						$orgArray['organizationRoleID'] = $orgRow['organizationRoleID'];
						$orgArray['organizationRole'] = $orgRow['shortName'];
					}
				}

				array_push($resourceOrgArray, $orgArray);
			}else{
				foreach ($result as $row) {

					$orgArray = array();

					//first, get the organization name
					$query = "SELECT name FROM " . $dbName . ".Organization WHERE organizationID = " . $row['organizationID'];

					if ($orgResult = $this->db->query($query)) {
						while ($orgRow = $orgResult->fetch_assoc()) {
							$orgArray['organization'] = $orgRow['name'];
							$orgArray['organizationID'] = $row['organizationID'];
						}
					}

					//then, get the role name
					$query = "SELECT * FROM " . $dbName . ".OrganizationRole WHERE organizationRoleID = " . $row['organizationRoleID'];


					if ($orgResult = $this->db->query($query)) {
						while ($orgRow = $orgResult->fetch_assoc()) {
							$orgArray['organizationRoleID'] = $orgRow['organizationRoleID'];
							$orgArray['organizationRole'] = $orgRow['shortName'];
						}
					}

					array_push($resourceOrgArray, $orgArray);

				}

			}






		//otherwise if the org module is not installed get the org name from this database
		}else{



			$resourceOrgArray = array();

			$query = "SELECT * FROM ResourceOrganizationLink WHERE resourceID = '" . $this->resourceID . "'";

			$result = $this->db->processQuery($query, 'assoc');

			$objects = array();

			//need to do this since it could be that there's only one request and this is how the dbservice returns result
			if (isset($result['organizationID'])) {
				$orgArray = array();

				//first, get the organization name
				$query = "SELECT shortName FROM Organization WHERE organizationID = " . $result['organizationID'];

				if ($orgResult = $this->db->query($query)) {
					while ($orgRow = $orgResult->fetch_assoc()) {
						$orgArray['organization'] = $orgRow['shortName'];
						$orgArray['organizationID'] = $result['organizationID'];
					}
				}

				//then, get the role name
				$query = "SELECT * FROM OrganizationRole WHERE organizationRoleID = " . $result['organizationRoleID'];

				if ($orgResult = $this->db->query($query)) {
					while ($orgRow = $orgResult->fetch_assoc()) {
						$orgArray['organizationRoleID'] = $orgRow['organizationRoleID'];
						$orgArray['organizationRole'] = $orgRow['shortName'];
					}
				}

				array_push($resourceOrgArray, $orgArray);
			}else{
				foreach ($result as $row) {

					$orgArray = array();

					//first, get the organization name
					$query = "SELECT shortName FROM Organization WHERE organizationID = " . $row['organizationID'];

					if ($orgResult = $this->db->query($query)) {
						while ($orgRow = $orgResult->fetch_assoc()) {
							$orgArray['organization'] = $orgRow['shortName'];
							$orgArray['organizationID'] = $row['organizationID'];
						}
					}

					//then, get the role name
					$query = "SELECT * FROM OrganizationRole WHERE organizationRoleID = " . $row['organizationRoleID'];


					if ($orgResult = $this->db->query($query)) {
						while ($orgRow = $orgResult->fetch_assoc()) {
							$orgArray['organizationRoleID'] = $orgRow['organizationRoleID'];
							$orgArray['organizationRole'] = $orgRow['shortName'];
						}
					}

					array_push($resourceOrgArray, $orgArray);

				}

			}





		}


		return $resourceOrgArray;
	}

	//search used index page drop down
	public function getOrganizationList() {
		$config = new Configuration;

		$orgArray = array();

		//if the org module is installed get the org names from org database
		if ($config->settings->organizationsModule == 'Y') {
			$dbName = $config->settings->organizationsDatabaseName;
			$query = "SELECT name, organizationID FROM " . $dbName . ".Organization ORDER BY 1;";

		//otherwise get the orgs from this database
		}else{
			$query = "SELECT shortName name, organizationID FROM Organization ORDER BY 1;";
		}


		$result = $this->db->processQuery($query, 'assoc');

		//need to do this since it could be that there's only one result and this is how the dbservice returns result
		if (isset($result['organizationID'])){ $result = [$result]; }
		foreach ($result as $row) {
			array_push($orgArray, $row);
		}

		return $orgArray;
	}

	//returns array of parent resource objects
	public function getParentResources() {
		return $this->getRelatedResources('resourceID');
	}

    public function getResourceAcquisitions() {
        $query = "SELECT * from ResourceAcquisition WHERE resourceID = " . $this->resourceID . " ORDER BY subscriptionStartDate DESC, subscriptionEndDate DESC";
		$result = $this->db->processQuery($query, 'assoc');
        $objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['resourceAcquisitionID'])) { $result = [$result]; }
		foreach ($result as $row) {
			$object = new ResourceAcquisition(new NamedArguments(array('primaryKey' => $row['resourceAcquisitionID'])));
			array_push($objects, $object);
		}
		return $objects;

    }

    //returns resource object by ebscoKbId
    public function getResourceByEbscoKbId($ebscoKbId) {

        $query = "SELECT *
			FROM Resource
			WHERE ebscoKbID = $ebscoKbId
			LIMIT 0,1";

        $result = $this->db->processQuery($query, 'assoc');

        if (isset($result['resourceID'])) {
        	return new Resource(new NamedArguments(['primaryKey' => $result['resourceID']]));
		} else {
        	return false;
		}
    }

	//returns resource objects by Isbn or ISSN
	public function getResourceByIsbnOrISSN($isbnOrISSN) {
		$query = "SELECT DISTINCT(resourceID)
			FROM IsbnOrIssn";

		$i = 0;

		if (!is_array($isbnOrISSN)) {
			if ($isbnOrISSN === null) return;
			$value = $isbnOrISSN;
			$isbnOrISSN = array($value);
		}

		foreach ($isbnOrISSN as $value) {
			$query .= ($i == 0) ? " WHERE " : " OR ";
			$query .= "isbnOrIssn = '" . $this->db->escapeString($value) . "'";
			$i++;
		}

		$query .=	" ORDER BY 1";

		$result = $this->db->processQuery($query, 'assoc');

		$objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['resourceID'])) { $result = [$result]; }
		foreach ($result as $row) {
			$object = new Resource(new NamedArguments(array('primaryKey' => $row['resourceID'])));
			array_push($objects, $object);
		}

		return $objects;
	}

	//returns resource objects by title
    public function getResourceByTitle($title) {

        $query = "SELECT *
			FROM Resource
			WHERE UPPER(titleText) = '" . str_replace("'", "''", strtoupper($title)) . "'
			ORDER BY 1";

        $result = $this->db->processQuery($query, 'assoc');

        $objects = array();

        //need to do this since it could be that there's only one request and this is how the dbservice returns result
        if (isset($result['resourceID'])) { $result = [$result]; }
        foreach ($result as $row) {
            $object = new Resource(new NamedArguments(array('primaryKey' => $row['resourceID'])));
            array_push($objects, $object);
        }

        return $objects;
    }

	public function getSiblingResourcesArray($organizationID) {

			$query = "SELECT DISTINCT r.resourceID, r.titleText FROM ResourceOrganizationLink rol
						LEFT JOIN Resource r ON r.resourceID=rol.resourceID
						WHERE rol.organizationID=".$organizationID." AND r.archiveDate IS NULL
						ORDER BY r.titleText";

			$result = $this->db->processQuery($query, 'assoc');

			if (isset($result["resourceID"])) {
				return array($result);
			}

			return $result;
	}

	//search used for the license autocomplete
	public function licenseAutocomplete($q) {
		$config = new Configuration;
		$licenseArray = array();

		//if the org module is installed get the org name from org database
		if ($config->settings->licensingModule == 'Y') {
			$dbName = $config->settings->licensingDatabaseName;

			$result = $this->db->query("SELECT shortName, licenseID
									FROM " . $dbName . ".License
									WHERE upper(shortName) like upper('%" . $q . "%')
									ORDER BY 1;");

		}

		while ($row = $result->fetch_assoc()) {
			$licenseArray[] = $row['shortName'] . "|" . $row['licenseID'];
		}



		return $licenseArray;
	}

	//search used for the organization autocomplete
	public function organizationAutocomplete($q) {
		$config = new Configuration;
		$organizationArray = array();

		//if the org module is installed get the org name from org database
		if ($config->settings->organizationsModule == 'Y') {
			$dbName = $config->settings->organizationsDatabaseName;

			$result = $this->db->query("SELECT CONCAT(A.name, ' (', O.name, ')') shortName, O.organizationID
									FROM " . $dbName . ".Alias A, " . $dbName . ".Organization O
									WHERE A.organizationID=O.organizationID
									AND upper(A.name) like upper('%" . $q . "%')
									UNION
									SELECT name shortName, organizationID
									FROM " . $dbName . ".Organization
									WHERE upper(name) like upper('%" . $q . "%')
									ORDER BY 1;");

		}else{

			$result = $this->db->query("SELECT organizationID, shortName
									FROM Organization O
									WHERE upper(O.shortName) like upper('%" . $q . "%')
									ORDER BY shortName;");

		}


		while ($row = $result->fetch_assoc()) {
			$organizationArray[] = $row['shortName'] . "|" . $row['organizationID'];
		}



		return $organizationArray;
	}

	public function removeAllIsbnOrIssn() {
		$query = "DELETE
			FROM IsbnOrIssn
			WHERE resourceID = '" . $this->resourceID . "'";

		$result = $this->db->processQuery($query);
	}

	//removes all resource subjects
	public function removeAllSubjects() {

		$query = "DELETE
			FROM ResourceSubject
			WHERE resourceID = '" . $this->resourceID . "'";

		$result = $this->db->processQuery($query);
	}

	//deletes all parent resources associated with this resource
	public function removeParentResources() {

		$query = "DELETE FROM ResourceRelationship WHERE resourceID = '" . $this->resourceID . "'";

		return $this->db->processQuery($query);
	}

	//removes this resource
	public function removeResource() {
		//delete data from child linked tables
		$this->removeResourceRelationships();
		$this->removeResourceOrganizations();
		$this->removeAllSubjects();
		$this->removeAllIsbnOrIssn();
        $this->removeResourceAcquisitions();
		$instance = new ExternalLogin();
		foreach ($this->getExternalLogins() as $instance) {
			$instance->delete();
		}

		$instance = new ResourceNote();
		foreach ($this->getNotes() as $instance) {
			$instance->delete();
		}

		$instance = new Alias();
		foreach ($this->getAliases() as $instance) {
			$instance->delete();
		}


		$this->delete();
	}

    // Removes all resource acquisitions from this resource
    public function removeResourceAcquisitions() {
        $instance = new ResourceAcquisition();
        foreach($this->getResourceAcquisitions() as $instance) {
            $instance->removeResourceAcquisition();
        }

    }

	//removes this resource and its children
	public function removeResourceAndChildren() {

		// for each children
		foreach ($this->getChildResources() as $instance) {
			$removeChild = true;
			$child = new Resource(new NamedArguments(array('primaryKey' => $instance->resourceID)));

			// get parents of this children
			$parents = $child->getParentResources();

			// If the child ressource belongs to another parent than the one we're removing
			foreach ($parents as $pinstance) {
				$parentResourceObj = new Resource(new NamedArguments(array('primaryKey' => $pinstance->relatedResourceID)));
				if ($parentResourceObj->resourceID != $this->resourceID) {
					// We do not delete this child.
					$removeChild = false;
				}
			}
			if ($removeChild == true) {
				$child->removeResource();
			}
		}
		// Finally, we remove the parent
		$this->removeResource();
	}

	//removes resource note records
	public function removeResourceNotes() {

		$query = "DELETE
			FROM ResourceNote
			WHERE entityID = '" . $this->resourceID . "'";

		$result = $this->db->processQuery($query);
	}

	//removes resource organizations
	public function removeResourceOrganizations() {

		$query = "DELETE
			FROM ResourceOrganizationLink
			WHERE resourceID = '" . $this->resourceID . "'";

		$result = $this->db->processQuery($query);
	}

	//removes resource hierarchy records
	public function removeResourceRelationships() {

		$query = "DELETE
			FROM ResourceRelationship
			WHERE resourceID = '" . $this->resourceID . "' OR relatedResourceID = '" . $this->resourceID . "'";

		$result = $this->db->processQuery($query);
	}

	//search used for the resource autocomplete
	public function resourceAutocomplete($q) {
		$resourceArray = array();
		$result = $this->db->query("SELECT titleText, resourceID
								FROM Resource
								WHERE upper(titleText) like upper('%" . $q . "%')
								ORDER BY 1;");

		while ($row = $result->fetch_assoc()) {
			$resourceArray[] = $row['titleText'] . "|" . $row['resourceID'];
		}

		return $resourceArray;
	}

	//returns array based on search
	public function search($whereAdd, $orderBy, $limit) {
		$query = $this->searchQuery($whereAdd, $orderBy, $limit, false);
		$result = $this->db->processQuery($query, 'assoc');

		$searchArray = array();

		//need to do this since it could be that there's only one result and this is how the dbservice returns result
		if (isset($result['resourceID'])) { $result = [$result]; }
		foreach ($result as $row) {
			$row = static::addIdsToResourcesRow($row);
			array_push($searchArray, $row);
		}
		return $searchArray;
	}

	public function searchCount($whereAdd) {
		$query = $this->searchQuery($whereAdd, '', '', true);
		$result = $this->db->processQuery($query, 'assoc');

		return $result['count'];
	}

	public function searchQuery($whereAdd, $orderBy = '', $limit = '', $count = false) {
		$config = new Configuration();
		$status = new Status();

		$joinTree = array(
			"A" => array(
                "stmt" => "LEFT JOIN Alias A ON R.resourceID = A.resourceID",
            ),
            "AT" => array(
                "stmt" => "LEFT JOIN AcquisitionType AT ON RA.acquisitionTypeID = AT.acquisitionTypeID",
                "requires" => "RA"
            ),
            "CU" => array(
                "stmt" => "LEFT JOIN User CU ON R.createLoginID = CU.loginID",
            ),
            "GDLINK" => array(
                "stmt" => "LEFT JOIN GeneralDetailSubjectLink GDLINK ON RSUB.generalDetailSubjectLinkID = GDLINK.generalDetailSubjectLinkID",
                "requires" => "RSUB"
            ),
            "I" => array(
                "stmt" => "LEFT JOIN IsbnOrIssn I ON R.resourceID = I.resourceID"
            ),
			"RA" => array(
				"stmt" => "LEFT JOIN ResourceAcquisition RA ON R.resourceID = RA.resourceID",
			),
            "RADSL" => array(
                "stmt" => "LEFT JOIN ResourceAdministeringSiteLink RADSL ON RA.resourceAcquisitionID = RADSL.resourceAcquisitionID",
                "requires" => "RA"
            ),
            "RAUSL" => array(
                "stmt" => "LEFT JOIN ResourceAuthorizedSiteLink RAUSL ON RA.resourceAcquisitionID = RAUSL.resourceAcquisitionID",
                "requires" => "RA"
            ),
            "RF" => array(
                "stmt" => "LEFT JOIN ResourceFormat RF ON R.resourceFormatID = RF.resourceFormatID",
            ),
            "RC" => array(
                "stmt" => "LEFT JOIN Resource RC ON RC.resourceID = RRC.resourceID",
                "requires" => "RRC"
            ),
            "RNA" => array(
                "stmt" => "LEFT JOIN ResourceNote RNA ON RA.resourceAcquisitionID = RNA.entityID",
                "requires" => "RA"
            ),
            "RNR" => array(
                "stmt" => "LEFT JOIN ResourceNote RNR ON R.resourceID = RNR.entityID",
            ),
            "RP" => array(
                "stmt" => "LEFT JOIN Resource RP ON RP.resourceID = RRP.relatedResourceID",
                "requires" => "RRP"
            ),
            "RPAY" => array(
                "stmt" => "LEFT JOIN ResourcePayment RPAY ON RA.resourceAcquisitionID = RPAY.resourceAcquisitionID",
				"requires" => "RA"
            ),
            "RPSL" => array(
                "stmt" => "LEFT JOIN ResourcePurchaseSiteLink RPSL ON RA.resourceAcquisitionID = RPSL.resourceAcquisitionID",
                "requires" => "RA"
            ),
			"ROL" => array(
                "stmt" => "LEFT JOIN ResourceOrganizationLink ROL ON R.resourceID = ROL.resourceID",
            ),
            "RRC" => array(
                "stmt" => "LEFT JOIN ResourceRelationship RRC ON RRC.relatedResourceID = R.resourceID",
            ),
			"RRP" => array(
				"stmt" => "LEFT JOIN ResourceRelationship RRP ON RRP.resourceID = R.resourceID",
			),
            "RS" => array(
                "stmt" => "LEFT JOIN ResourceStep RS ON RA.resourceAcquisitionID = RS.resourceAcquisitionID",
                "requires" => "RA"
            ),
			"RSUB" => array(
				"stmt" => "LEFT JOIN ResourceSubject RSUB ON R.resourceID = RSUB.resourceID",
			),
            "RT" => array(
                "stmt" => "LEFT JOIN ResourceType RT ON R.resourceTypeID = RT.resourceTypeID",
            ),
            "S" => array(
                "stmt" => "LEFT JOIN Status S ON R.statusID = S.statusID",
            ),
		);

		if ($config->settings->organizationsModule == 'Y') {
			$dbName = $config->settings->organizationsDatabaseName;
			$joinTree["O"] = array(
				"stmt" => "LEFT JOIN " . $dbName . ".Organization O ON O.organizationID = ROL.organizationID
							LEFT JOIN " . $dbName . ".Alias OA ON OA.organizationID = ROL.organizationID",
				"requires" => "ROL"
			);
		}else{
			$joinTree["O"] = array(
				"stmt" => "LEFT JOIN Organization O ON O.organizationID = ROL.organizationID",
                "requires" => "ROL"
			);
		}

		$savedStatusID = intval($status->getIDFromName('saved'));
		//also add to not retrieve saved records
		$whereAdd[] = "R.statusID != " . $savedStatusID;

		if (is_array($whereAdd) && count($whereAdd) > 0) {
			$whereStatement = " WHERE " . implode(" AND ", $whereAdd);
		}else{
			$whereStatement = "";
		}

		if ($count) {
			$select = "SELECT COUNT(DISTINCT R.resourceID) count";
			$groupBy = "";
		} else {
			$select = "SELECT R.resourceID, R.titleText, GROUP_CONCAT(DISTINCT AT.shortName SEPARATOR ' / ') as acquisitionType, R.createLoginID, CU.firstName, CU.lastName, R.createDate, S.shortName status,
						GROUP_CONCAT(DISTINCT A.shortName, I.isbnOrIssn ORDER BY A.shortName DESC SEPARATOR '<br />') aliases";
			$groupBy = "GROUP BY R.resourceID";
		}

        // Build a list of tables that are referenced by the select and where statements in order to limit the number of joins performed in the search.
        preg_match_all("/[A-Z]+(?=[.][A-Z]+)/iu", $select, $table_matches);
        $referenced_tables = array_unique($table_matches[0]);

        preg_match_all("/[A-Z]+(?=[.][A-Z]+)/iu", $whereStatement, $table_matches);
        $referenced_tables = array_unique(array_merge($referenced_tables, $table_matches[0]));
        // Remove the R table
        if (($key = array_search('R', $referenced_tables)) !== false) {
            unset($referenced_tables[$key]);
        }

		$joinStmts = array();

		if (!empty($referenced_tables)) {
			$alreadyJoined = array();

			foreach($referenced_tables as $join) {
                // Prevent joining more than once
                if (in_array($join, $alreadyJoined)) {
                	continue;
                }

                // If the table requires another linking table, join that first
				if(!empty($joinTree[$join]['requires'])) {
					// Prevent joining parents more than once
					$parent = $joinTree[$join]['requires'];
					if (!in_array($parent, $alreadyJoined)) {
						$joinStmts[] = $joinTree[$parent]['stmt'];
						$alreadyJoined[] = $parent;
					}
				}

				$joinStmts[] = $joinTree[$join]['stmt'];
                $alreadyJoined[] = $join;
			}
		}

		$query = $select . "
								FROM Resource R
									" . implode("\n", $joinStmts) . "
									" . $whereStatement . "
									" . $groupBy;

		if ($orderBy) {
			$query .= "\nORDER BY " . $orderBy;
		}

		if ($limit) {
			$query .= "\nLIMIT " . $limit;
		}
		return $query;
	}

	public function setIsbnOrIssn($isbnorissns) {
		$this->removeAllIsbnOrIssn();
		foreach ($isbnorissns as $isbnorissn) {
			if (trim($isbnorissn) != '') {
				$isbnOrIssn = new IsbnOrIssn();
				$isbnOrIssn->resourceID = $this->resourceID;
				$isbnOrIssn->isbnOrIssn = $isbnorissn;
				$isbnOrIssn->save();
			}
		}
	}


	/* Protected Functions */


	protected function defineRelationships() {}

	protected function defineIsbnOrIssn() {}

	protected function overridePrimaryKeyName() {}


	/* Private Functions */


	private static function addIdsToResourcesRow($row) {
		$resource = new Resource(new NamedArguments(array('primaryKey' => $row['resourceID'])));
		$isbnOrIssns = $resource->getIsbnOrIssn();
		$row['isbnOrIssns'] = [];
		foreach ($isbnOrIssns as $isbnOrIssn) {
			array_push($row['isbnOrIssns'], $isbnOrIssn->isbnOrIssn);
		}
		return $row;
	}

	private function getDownTimeResults($archivedOnly=false) {
		$query = "SELECT d.*
			  FROM Downtime d
			  WHERE d.entityID='{$this->primaryKey}'
			  AND d.entityTypeID=2";

		if ($archivedOnly) {
			$query .= " AND d.endDate < CURDATE()";
		} else {
			$query .= " AND (d.endDate >= CURDATE() OR d.endDate IS NULL)";
		}
		$query .= "	ORDER BY d.dateCreated DESC";

		return $this->db->processQuery($query, 'assoc');
	}

    // return array of related resource objects
    private function getRelatedResources($key) {
        $query = "SELECT rr.resourceRelationshipID
			FROM ResourceRelationship rr
            JOIN Resource r on rr.resourceID = r.resourceID
			WHERE rr.$key = '" . $this->resourceID . "'
			AND relationshipTypeID = '1'
			ORDER BY r.titleText";

        $result = $this->db->processQuery($query, 'assoc');

        $objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['resourceRelationshipID'])) {
			$object = new ResourceRelationship(new NamedArguments(array('primaryKey' => $result['resourceRelationshipID'])));
			array_push($objects, $object);
		}else{
			$db = DBService::getInstance();
			foreach ($result as $row) {
				$object = new ResourceRelationship(new NamedArguments(array('primaryKey' => $row['resourceRelationshipID'],'db'=>$db)));
				array_push($objects, $object);
			}
		}

        return $objects;

    }
}
?>
