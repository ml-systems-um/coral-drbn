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
namespace resources\models;
use common\{Model};
class AcquisitionType extends Model {
	public function sortedArray() {
        $this->setQueryCallType('assoc');
        $this->queryArray = [
            "SELECT *",
            "FROM AcquisitionType",
            "ORDER BY IF(UCASE(shortName)='PAID',1, 2), shortName ASC",
        ];
        return $this->callModelQuery();
	}

	//returns number of children for this particular contact role
	public function getNumberOfChildren(){
        $this->setQueryCallType('assoc');
        $this->queryArray = [
            "SELECT COUNT(*) AS childCount",
            "FROM ResourceAcquisition",
			"WHERE acquisitionTypeID = '{$this->acquisitionTypeID}';",
        ];
        return $this->callModelQuery();
	}

    public function getAcquisitionTypeIDByName($name) {
		$upperName = strtoupper($name);
        $this->setQueryCallType('assoc');
        $this->queryArray = [
            "SELECT acquisitionTypeID",
            "FROM AcquisitionType",
			"WHERE UPPER(shortName) = '{$upperName}';",
        ];
        return $this->callModelQuery();
	}
}

?>
