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
namespace resources\controllers;
use common\{DatabaseObject};
use resources\models as db;
class NoteType extends DatabaseObject {
	//returns array of note types without the INITIAL Note.
	public function allAsArrayForDD() {
		$noteTypeModel = new db\NoteType();
		$results = $noteTypeModel->allAsArrayForDD();
		$resultArray = array();
		$rowArray = array();

		//Take a look at this once I'm at a place it's getting used.
		foreach($results as $row){
			foreach (array_keys($this->attributeNames) as $attributeName) {
					$rowArray[$attributeName] = $row[$attributeName];
			}
			array_push($resultArray, $rowArray);
		}

		return $resultArray;
	}

	//returns noteTypeID for the Creator note type which is
	public function getInitialNoteTypeID() {
		$noteTypeModel = new db\NoteType();
		$result = $noteTypeModel->getInitialNoteTypeID();
		
		$output = ($result[0]['noteTypeID']) ?? '';
		return $output;
	}

    public function getNoteTypeIDByName($name) {
		$noteTypeModel = new db\NoteType();
		$result = $noteTypeModel->getNoteTypeIDByName($name);
		
		$output = ($result[0]['noteTypeID']) ?? NULL;
		return $output;
    }

	//returns number of children for this particular contact role
	public function getNumberOfChildren(){
		$noteTypeModel = new db\NoteType(new NamedArguments(array('primaryKey' => $this->noteTypeID)));
		$result = $noteTypeModel->getNumberOfChildren();
		return $result[0]['childCount'];
	}
}

?>
