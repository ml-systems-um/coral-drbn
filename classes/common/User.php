<?php

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
namespace common;
use common\{DatabaseObject, Utility, Configuration, NamedArguments, Privilege};

class User extends DatabaseObject {
	protected function defineRelationships() {}
	protected function overridePrimaryKeyName() {
		$this->primaryKeyName = 'loginID';
	}

	//used to formulate display name in case firstname/last name isn't set up or user doesn't exist
	//format: firstname {space} lastname
	public function getDisplayName(){
		if ($this->firstName){
			return $this->firstName . " " . $this->lastName;
		}else if ($this->loginID){
			return $this->loginID;
		}else{
			return false;
		}
	}

	//used to formulate display name in case firstname/last name isn't set up or user doesn't exist for drop down
	//format lastname {comma} firstname
	public function getDDDisplayName(){
		if ($this->firstName){
			return $this->lastName . ", " . $this->firstName;
		}else if ($this->loginID){
			return $this->loginID;
		}
	}

    public function hasPrivilege($privilegeArray = []){
        $privilege = new Privilege(new NamedArguments(array('primaryKey' => $this->privilegeID)));
        $upperCasePrivilege = mb_strtoupper($privilege->shortName);
        $hasPrivilege = in_array($upperCasePrivilege, $privilegeArray);
        return $hasPrivilege;
    }

	public function allAsArray() {
		$query = "SELECT * FROM User ORDER BY lastName, loginID";
		$result = $this->db->processQuery($query, 'assoc');

		$resultArray = array();
		$rowArray = array();

		if (isset($result['loginID'])){
			foreach (array_keys($result) as $attributeName) {
				$rowArray[$attributeName] = $result[$attributeName];
			}
			array_push($resultArray, $rowArray);
		}else{
			foreach ($result as $row) {
				foreach (array_keys($this->attributeNames) as $attributeName) {
					$rowArray[$attributeName] = $row[$attributeName];
				}
				array_push($resultArray, $rowArray);
			}
		}

		return $resultArray;
	}

	public function hasOpenSession() {
		$util = new Utility();
		$config = new Configuration();

		$dbName = $config->settings->authDatabaseName;
		$sessionID = $util->getSessionCookie();
		$query = [
			"SELECT DISTINCT sessionID",
			"FROM {$dbName}.Session",
			"WHERE loginID = '{$this->loginID}'",
			"AND sessionID='{$sessionID}'",
			"LIMIT 1",
		];
		$queryString = implode(" ", $query);
		$result = $this->db->processQuery($queryString, 'assoc');
		return isset($result[0]['sessionID']);
	}
}

?>
