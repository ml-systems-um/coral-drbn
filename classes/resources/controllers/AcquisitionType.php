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
use common\{NamedArguments, DatabaseObject};
use resources\models as db;
class AcquisitionType extends DatabaseObject {
	public function sortedArray() {
		$acquisitionTypeModel = new db\AcquisitionType();
		$results = $acquisitionTypeModel->sortedArray();
		$resultArray = [];
		$rowArray = [];
		foreach ($results as $row) {
			foreach (array_keys($this->attributeNames) as $attributeName) {
				$rowArray[$attributeName] = $row[$attributeName];
			}
			array_push($resultArray, $rowArray);
			$rowArray = [];
		}
		return $resultArray;
	}

	//returns number of children for this particular contact role
	public function getNumberOfChildren(){
		$acquisitionTypeModel = new db\AcquisitionType(new NamedArguments(array('primaryKey' => $this->acquisitionTypeID)));
		$result = $acquisitionTypeModel->getNumberOfChildren();

		return $result[0]['childCount'];
	}

    public function getAcquisitionTypeIDByName($name) {
		$acquisitionTypeModel = new db\AcquisitionType();
		$result = $acquisitionTypeModel->getAcquisitionTypeIDByName($name);

		return $result[0]['acquisitionTypeID'];
	}
}

?>
