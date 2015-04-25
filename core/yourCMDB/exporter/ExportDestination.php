<?php
/********************************************************************
* This file is part of yourCMDB.
*
* Copyright 2013-2015 Michael Batz
*
*
* yourCMDB is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* yourCMDB is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with yourCMDB.  If not, see <http://www.gnu.org/licenses/>.
*
*********************************************************************/
namespace yourCMDB\exporter;

/**
* Export API - destination for an export task
* @author Michael Batz <michael@yourcmdb.org>
*/
class ExportDestination
{
	//class for export destination
	private $class;

	//parameter array
	private $parameter;

	function __construct($class, $parameter)
	{
		$this->class = $class;
		$this->parameter = $parameter;
	}

	public function getClass()
	{
		return $this->class;
	}

	public function getParameterKeys()
	{
		return array_keys($this->parameter);
	}

	public function getParameterValue($key)
	{
		return $this->parameter[$key];
	}
}
?>
