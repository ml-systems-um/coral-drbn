<?php 
namespace resources;
class ResourceSearch {
    private $configuration = FALSE;
    private $resource = FALSE;

    private $defaultSearchParameters = [
		"orderBy" => "R.createDate DESC, TRIM(LEADING 'THE ' FROM UPPER(R.titleText)) asc",
		"page" => 1,
		"recordsPerPage" => 25,
    ];

	public function __construct(){
        $this->resource = new Resource();
        $this->configuration = new \common\Configuration();
	}

    /* Public Functions */
	public function getSearch() {
		if (!\common\CoralSession::get('resourceSearch')) {
			Resource::resetSearch();
		}
		return \common\CoralSession::get('resourceSearch');
	}
	
	public function getSearchDetails() {
		// A successful mysqli_connect must be run before mysqli_real_escape_string will function.  Instantiating a resource model will set up the connection
        $search = $this->getSearch();
        $resource = new Resource();

        $config = $this->configuration;
        debug($search);
		$whereAdd = array();
		$searchDisplay = array();

		//if name is passed in also search alias, organizations and organization aliases
		if ($search['name']) {
			$nameQueryString = $resource->db->escapeString(strtoupper($search['name']));
			$nameQueryString = preg_replace("/ +/", "%", $nameQueryString);
			$nameQueryString = "'%" . $nameQueryString . "%'";

			if ($config->settings->organizationsModule == 'Y') {
				$dbName = $config->settings->organizationsDatabaseName;
				$whereAdd[] = "((UPPER(R.titleText) LIKE " . $nameQueryString . ") OR (UPPER(A.shortName) LIKE " . $nameQueryString . ") OR (UPPER(O.name) LIKE " . $nameQueryString . ") OR (UPPER(OA.name) LIKE " . $nameQueryString . ") OR (UPPER(RP.titleText) LIKE " . $nameQueryString . ") OR (UPPER(RC.titleText) LIKE " . $nameQueryString . ") OR (UPPER(RA.recordSetIdentifier) LIKE " . $nameQueryString . "))";

			}else{
				$whereAdd[] = "((UPPER(R.titleText) LIKE " . $nameQueryString . ") OR (UPPER(A.shortName) LIKE " . $nameQueryString . ") OR (UPPER(O.shortName) LIKE " . $nameQueryString . ") OR (UPPER(RP.titleText) LIKE " . $nameQueryString . ") OR (UPPER(RC.titleText) LIKE " . $nameQueryString . ") OR (UPPER(RA.recordSetIdentifier) LIKE " . $nameQueryString . "))";

			}
			$searchDisplay[] = _("Name contains: ") . $search['name'];

		}

		//get where statements together (and escape single quotes)
		if ($search['resourceISBNOrISSN']) {
			$resourceISBNOrISSN = $resource->db->escapeString(str_replace("-","",$search['resourceISBNOrISSN']));
            $whereAdd[] = "REPLACE(I.isbnOrIssn,'-','') = '" . $resourceISBNOrISSN . "'";
			$searchDisplay[] = _("ISSN/ISBN: ") . $search['resourceISBNOrISSN'];
		}

		if ($search['orderNumber']){
			$orderNum = $resource->db->escapeString($search['orderNumber']);
			$whereAdd[] = "(RA.orderNumber = '{$orderNum}')";
			$searchDisplay[] = _("Order Number:")." {$orderNum}";
		}

		if ($search['stepName']) {
			$status = new Status();
			$completedStatusID = $status->getIDFromName('complete');
			$whereAdd[] = "(R.statusID != $completedStatusID AND RS.stepName = '" . $resource->db->escapeString($search['stepName']) . "' AND RS.stepStartDate IS NOT NULL AND RS.stepEndDate IS NULL)";
			$searchDisplay[] = _("Workflow Step: ") . $search['stepName'];
		}


		if ($search['parent'] != null) {
            if ($search['parent'] == 'None') {
                $parentadd = "(RRP.relationshipTypeID IS NULL AND RRC.relationshipTypeID IS NULL)";
            } else {
                $parentadd = "(" . $search['parent'] . ".relationshipTypeID = 1)";
            }
            $whereAdd[] = $parentadd;
		}



		if ($search['statusID']) {
			$whereAdd[] = "R.statusID = '" . $resource->db->escapeString($search['statusID']) . "'";
			$status = new Status(new NamedArguments(array('primaryKey' => $search['statusID'])));
			$searchDisplay[] = _("Status: ") . $status->shortName;
		}

		if ($search['creatorLoginID']) {
			$whereAdd[] = "R.createLoginID = '" . $resource->db->escapeString($search['creatorLoginID']) . "'";

			$createUser = new User(new NamedArguments(array('primaryKey' => $search['creatorLoginID'])));
			if ($createUser->firstName) {
				$name = $createUser->lastName . ", " . $createUser->firstName;
			}else{
				$name = $createUser->loginID;
			}
			$searchDisplay[] = _("Creator: ") . $name;
		}

		if ($search['resourceFormatID']) {
			$whereAdd[] = "R.resourceFormatID = '" . $resource->db->escapeString($search['resourceFormatID']) . "'";
			$resourceFormat = new ResourceFormat(new NamedArguments(array('primaryKey' => $search['resourceFormatID'])));
			$searchDisplay[] = _("Resource Format: ") . $resourceFormat->shortName;
		}

		if ($search['acquisitionTypeID']) {
			$whereAdd[] = "RA.acquisitionTypeID = '" . $resource->db->escapeString($search['acquisitionTypeID']) . "'";
			$acquisitionType = new AcquisitionType(new NamedArguments(array('primaryKey' => $search['acquisitionTypeID'])));
			$searchDisplay[] = _("Acquisition Type: ") . $acquisitionType->shortName;
		}


		if ($search['resourceNote']) {
			$whereAdd[] = "(UPPER(RNA.noteText) LIKE UPPER('%" . $resource->db->escapeString($search['resourceNote']) . "%') AND RNA.tabName <> 'Product') OR (UPPER(RNR.noteText) LIKE UPPER('%" . $resource->db->escapeString($search['resourceNote']) . "%') AND RNR.tabName = 'Product')";
			$searchDisplay[] = _("Note contains: ") . $search['resourceNote'];
		}

		if ($search['createDateStart']) {
		  $startDate = create_date_from_js_format($search['createDateStart'])->format('Y-m-d');
			$whereAdd[] = "R.createDate >= '". $resource->db->escapeString($startDate) ."'";
			if (!$search['createDateEnd']) {
				$searchDisplay[] = _("Created on or after: ") . $search['createDateStart'];
			} else {
				$searchDisplay[] = _("Created between: ") . $search['createDateStart'] . " and " . $search['createDateEnd'];
			}
		}

		if ($search['createDateEnd']) {
      $endDate = create_date_from_js_format($search['createDateEnd'])->format('Y-m-d');
			$whereAdd[] = "R.createDate <= '" . $resource->db->escapeString($endDate) . "'";
			if (!$search['createDateStart']) {
				$searchDisplay[] = _("Created on or before: ") . $search['createDateEnd'];
			}
		}

		if ($search['startWith']) {
			$whereAdd[] = "TRIM(LEADING 'THE ' FROM UPPER(R.titleText)) LIKE UPPER('" . $resource->db->escapeString($search['startWith']) . "%')";
			$searchDisplay[] = _("Starts with: ") . $search['startWith'];
		}

		//the following are not-required fields with dropdowns and have "none" as an option
		if ($search['fund'] == 'none') {
			$whereAdd[] = "((RPAY.fundID IS NULL) OR (RPAY.fundID = '0'))";
			$searchDisplay[] = _("Fund: none");
		}else if ($search['fund']) {
			$fund = str_replace("-","",$search['fund']);
			$whereAdd[] = "RPAY.fundID = '" . $resource->db->escapeString($fund) . "'";
			$searchDisplay[] = _("Fund: ") . $search['fund'];
		}

		if ($search['resourceTypeID'] == 'none') {
			$whereAdd[] = "((R.resourceTypeID IS NULL) OR (R.resourceTypeID = '0'))";
			$searchDisplay[] = _("Resource Type: none");
		}else if ($search['resourceTypeID']) {
			$whereAdd[] = "R.resourceTypeID = '" . $resource->db->escapeString($search['resourceTypeID']) . "'";
			$resourceType = new ResourceType(new NamedArguments(array('primaryKey' => $search['resourceTypeID'])));
			$searchDisplay[] = _("Resource Type: ") . $resourceType->shortName;
		}


		if ($search['generalSubjectID'] == 'none') {
			$whereAdd[] = "((GDLINK.generalSubjectID IS NULL) OR (GDLINK.generalSubjectID = '0'))";
			$searchDisplay[] = _("Resource Type: none");
		}else if ($search['generalSubjectID']) {
			$whereAdd[] = "GDLINK.generalSubjectID = '" . $resource->db->escapeString($search['generalSubjectID']) . "'";
			$generalSubject = new GeneralSubject(new NamedArguments(array('primaryKey' => $search['generalSubjectID'])));
			$searchDisplay[] = _("General Subject: ") . $generalSubject->shortName;
		}

		if ($search['detailedSubjectID'] == 'none') {
			$whereAdd[] = "((GDLINK.detailedSubjectID IS NULL) OR (GDLINK.detailedSubjectID = '0') OR (GDLINK.detailedSubjectID = '-1'))";
			$searchDisplay[] = _("Resource Type: none");
		}else if ($search['detailedSubjectID']) {
			$whereAdd[] = "GDLINK.detailedSubjectID = '" . $resource->db->escapeString($search['detailedSubjectID']) . "'";
			$detailedSubject = new DetailedSubject(new NamedArguments(array('primaryKey' => $search['detailedSubjectID'])));
			$searchDisplay[] = _("Detailed Subject: ") . $detailedSubject->shortName;
		}

		if ($search['noteTypeID'] == 'none') {
			$whereAdd[] = "(RNA.noteTypeID IS NULL) AND (RNA.noteText IS NOT NULL) AND (RNR.noteTypeID IS NULL) AND (RNR.noteText IS NOT NULL)";
			$searchDisplay[] = _("Note Type: none");
		}else if ($search['noteTypeID']) {
			$whereAdd[] = "((RNA.noteTypeID = '" . $resource->db->escapeString($search['noteTypeID']) . "' AND RNA.tabName <> 'Product') OR (RNR.noteTypeID = '" . $resource->db->escapeString($search['noteTypeID']) . "' AND RNR.tabName = 'Product'))";
			$noteType = new NoteType(new NamedArguments(array('primaryKey' => $search['noteTypeID'])));
			$searchDisplay[] = _("Note Type: ") . $noteType->shortName;
		}


		if ($search['purchaseSiteID'] == 'none') {
			$whereAdd[] = "RPSL.purchaseSiteID IS NULL";
			$searchDisplay[] = _("Purchase Site: none");
		}else if ($search['purchaseSiteID']) {
			$whereAdd[] = "RPSL.purchaseSiteID = '" . $resource->db->escapeString($search['purchaseSiteID']) . "'";
			$purchaseSite = new PurchaseSite(new NamedArguments(array('primaryKey' => $search['purchaseSiteID'])));
			$searchDisplay[] = _("Purchase Site: ") . $purchaseSite->shortName;
		}


		if ($search['authorizedSiteID'] == 'none') {
			$whereAdd[] = "RAUSL.authorizedSiteID IS NULL";
			$searchDisplay[] = _("Authorized Site: none");
		}else if ($search['authorizedSiteID']) {
			$whereAdd[] = "RAUSL.authorizedSiteID = '" . $resource->db->escapeString($search['authorizedSiteID']) . "'";
			$authorizedSite = new AuthorizedSite(new NamedArguments(array('primaryKey' => $search['authorizedSiteID'])));
			$searchDisplay[] = _("Authorized Site: ") . $authorizedSite->shortName;
		}


		if ($search['administeringSiteID'] == 'none') {
			$whereAdd[] = "RADSL.administeringSiteID IS NULL";
			$searchDisplay[] = _("Administering Site: none");
		}else if ($search['administeringSiteID']) {
			$whereAdd[] = "RADSL.administeringSiteID = '" . $resource->db->escapeString($search['administeringSiteID']) . "'";
			$administeringSite = new AdministeringSite(new NamedArguments(array('primaryKey' => $search['administeringSiteID'])));
			$searchDisplay[] = _("Administering Site: ") . $administeringSite->shortName;
		}


		if ($search['authenticationTypeID'] == 'none') {
			$whereAdd[] = "RA.authenticationTypeID IS NULL";
			$searchDisplay[] = _("Authentication Type: none");
		}else if ($search['authenticationTypeID']) {
			$whereAdd[] = "RA.authenticationTypeID = '" . $resource->db->escapeString($search['authenticationTypeID']) . "'";
			$authenticationType = new AuthenticationType(new NamedArguments(array('primaryKey' => $search['authenticationTypeID'])));
			$searchDisplay[] = _("Authentication Type: ") . $authenticationType->shortName;
		}

		if ($search['catalogingStatusID'] == 'none') {
			$whereAdd[] = "(RA.catalogingStatusID IS NULL)";
			$searchDisplay[] = _("Cataloging Status: none");
		} else if ($search['catalogingStatusID']) {
			$whereAdd[] = "RA.catalogingStatusID = '" . $resource->db->escapeString($search['catalogingStatusID']) . "'";
			$catalogingStatus = new CatalogingStatus(new NamedArguments(array('primaryKey' => $search['catalogingStatusID'])));
			$searchDisplay[] = _("Cataloging Status: ") . $catalogingStatus->shortName;
		}

		if ($search['publisher']) {
			$nameQueryString = $resource->db->escapeString(strtoupper($search['publisher']));
			$nameQueryString = preg_replace("/ +/", "%", $nameQueryString);
		  	$nameQueryString = "'%" . $nameQueryString . "%'";
			if ($config->settings->organizationsModule == 'Y'){
				$dbName = $config->settings->organizationsDatabaseName;
				$whereAdd[] = "ROL.organizationRoleID=5 AND ((UPPER(O.name) LIKE " . $nameQueryString . ") OR (UPPER(OA.name) LIKE " . $nameQueryString . "))";
			}else{
				$whereAdd[] = "ROL.organizationRoleID=5 AND (UPPER(O.shortName) LIKE " . $nameQueryString . ")";
			}
			$searchDisplay[] = _("Publisher name contains: ") . $search['publisher'];
		}

		if ($search['platform']) {
			$nameQueryString = $resource->db->escapeString(strtoupper($search['platform']));
			$nameQueryString = preg_replace("/ +/", "%", $nameQueryString);
			$nameQueryString = "'%" . $nameQueryString . "%'";
			if ($config->settings->organizationsModule == 'Y'){
				$dbName = $config->settings->organizationsDatabaseName;
				$whereAdd[] = "ROL.organizationRoleID=3 AND ((UPPER(O.name) LIKE " . $nameQueryString . ") OR (UPPER(OA.name) LIKE " . $nameQueryString . "))";
 			}else{
 				$whereAdd[] = "ROL.organizationRoleID=3 AND (UPPER(O.shortName) LIKE " . $nameQueryString . ")";
 			}
 			$searchDisplay[] = _("Platform name contains: ") . $search['publisher'];
 		}

 		if ($search['provider']) {
 			$nameQueryString = $resource->db->escapeString(strtoupper($search['provider']));
 			$nameQueryString = preg_replace("/ +/", "%", $nameQueryString);
 			$nameQueryString = "'%" . $nameQueryString . "%'";
 			if ($config->settings->organizationsModule == 'Y'){
 				$dbName = $config->settings->organizationsDatabaseName;
 				$whereAdd[] = "ROL.organizationRoleID=4 AND ((UPPER(O.name) LIKE " . $nameQueryString . ") OR (UPPER(OA.name) LIKE " . $nameQueryString . "))";
 			}else{
 				$whereAdd[] = "ROL.organizationRoleID=4 AND (UPPER(O.shortName) LIKE " . $nameQueryString . ")";
 			}
			$searchDisplay[] = _("Provider name contains: ") . $search['publisher'];
		}

		$orderBy = $search['orderBy'];


		$page = $search['page'];
		$recordsPerPage = $search['recordsPerPage'];
		return array("where" => $whereAdd, "page" => $page, "order" => $orderBy, "perPage" => $recordsPerPage, "display" => $searchDisplay);
	}

	public function resetSearch() {
		$this->setSearch(array());
	}

	public function setSearch($search) {
        $defaultOrderBy = $this->defaultSearchParameters['orderBy'];
        $orderBy = ($this->configuration->settings->defaultsort) ?? $defaultOrderBy;
        $this->defaultSearchParameters['orderBy'] = $orderBy;

        //Merge the two arrays. If a Default exists in the provided $search, it will overwrite the default. 
        $outputSearch = array_merge($this->defaultSearchParameters, $search);
        //Now trim the values in the array.
        $trimmedArray = array_map('trim', $outputSearch);

		\common\CoralSession::set('resourceSearch', $trimmedArray);
	}
}
?>