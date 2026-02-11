<?php
/*
**************************************************************************************************************************
** CORAL Common Module v. 1.0
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
class EmailHeader extends DynamicObject {
	protected $fieldName;
	protected $fieldBody;

	protected function init(NamedArguments $arguments) {
		$this->fieldName = $this->fieldNameFromName($arguments->name);
		$this->fieldBody = $arguments->body;
	}

	protected function fieldNameFromName($name) {
		$headerName = ucfirst($name);
		// Hypenate camelCase
		$headerName = preg_replace('/([a-z])([A-Z])/', '\1-\2', $headerName);
		return $headerName;
	}

	public function text() {
		return self::$this->fieldName . ': ' . $this->fieldBody . "\n";
	}

}
?>