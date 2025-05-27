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

class UserGroup extends DatabaseObject {

	protected function defineRelationships() {}

	protected function overridePrimaryKeyName() {}




	//returns array of user objects associated with this group
	public function getUsers(){

		$query = "SELECT U.* FROM User U, UserGroupLink UGL
					WHERE UGL.userGroupID = '" . $this->userGroupID . "'
					AND UGL.loginID = U.loginID
					ORDER BY U.lastName, U.firstName, U.loginID";

		$result = $this->db->processQuery($query, 'assoc');

		$objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['loginID'])){
			$object = new User(new NamedArguments(array('primaryKey' => $result['loginID'])));
			array_push($objects, $object);
		}else{
			foreach ($result as $row) {
				$object = new User(new NamedArguments(array('primaryKey' => $row['loginID'])));
				array_push($objects, $object);
			}
		}

		return $objects;
	}

	public function getUserLoginIDs(){
		$groupID = $this->userGroupID;
		$query = [
			"SELECT UGL.loginID",
			"FROM UserGroupLink UGL",
			"WHERE UGL.userGroupID = '{$groupID}'",
		];
		$queryString = implode(" ", $query);
		$result = $this->db->processQuery($queryString, 'assoc');
		//need to do this since it could be that there's only one result and dbService doesn't actually return the row as an array (instead the row is the entire result).
		$oneResult = (isset($result['loginID']));
		$output = [];
		if($oneResult){
			$output[] = $result['loginID'];
		} else {
			foreach($result as $row){
				$output[] = $row['loginID'];
			}
		}
		return $output;
	}


	public function removeUser($userID){
		$groupID = $this->userGroupID;
		$removedUser = ($userID) ?? FALSE;
		if($removedUser){
			$query = "DELETE FROM UserGroupLink WHERE userGroupID = '{$groupID}' AND loginID = '{$removedUser}'";
			return $this->db->processQuery($query);
		} else {return false;}
	}

	//deletes all user links associated with this user group
	public function removeUsers(){

		$query = "DELETE FROM UserGroupLink WHERE userGroupID = '" . $this->userGroupID . "'";

		return $this->db->processQuery($query);
	}






}

?>
