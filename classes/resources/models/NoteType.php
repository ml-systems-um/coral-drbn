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
class NoteType extends Model {
	//returns array of note types without the INITIAL Note.
	public function allAsArrayForDD() {
        $this->setQueryCallType('assoc');
        $this->queryArray = [
            "SELECT *",
            "FROM NoteType",
            "WHERE UPPER(shortName) NOT LIKE '%INITIAL%'",
			"ORDER BY shortName, noteTypeID",
        ];
        return $this->callModelQuery();
	}

	//returns noteTypeID for the Creator note type which is
	public function getInitialNoteTypeID() {
        $this->setQueryCallType('assoc');
        $this->queryArray = [
            "SELECT *",
            "FROM NoteType",
            "WHERE UPPER(shortName) LIKE '%INITIAL%'",
			"LIMIT 0,1",
        ];
        return $this->callModelQuery();
	}

    public function getNoteTypeIDByName($name) {
        $this->setQueryCallType('assoc');
        $this->queryArray = [
            "SELECT *",
            "FROM NoteType",
            "WHERE shortName = '{$name}'",
			"LIMIT 0,1",
        ];
        return $this->callModelQuery();
    }

	//returns number of children for this particular contact role
	public function getNumberOfChildren(){
        $this->setQueryCallType('assoc');
        $this->queryArray = [
            "SELECT COUNT(*) AS childCount",
            "FROM ResourceNote",
            "WHERE noteTypeID = '{$this->noteTypeID}'",
        ];
        return $this->callModelQuery();
	}
}

?>
