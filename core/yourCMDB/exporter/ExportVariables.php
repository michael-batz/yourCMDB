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
* Export API - variables for an export task
* @author Michael Batz <michael@yourcmdb.org>
*/
class ExportVariables
{

	//all defined variables
	private $variables;

	/**
	* create a new instance
	* @param $exportVariables  	Array of ExportVariable
	*/
	function __construct($exportVariables)
	{
		$this->variables = $exportVariables;
	}

	/**
	* Returns the variable of the given name
	* @param $name			variable name
	* @return ExportVariable	ExportVariable or null, if not found
	*/
	public function getVariable($name)
	{
		foreach($this->variables as $variable)
		{
			if($variable->getName() == $name)
			{
				return $variable;
			}
		}

		return null;
	}

	/**
	* Returns an array with all variable names
	*/
	public function getVariableNames()
	{
		$names = Array();
		foreach($this->variables as $variable)
		{
			$names[] = $variable->getName();
		}
		return $names;
	}	
}
?>
