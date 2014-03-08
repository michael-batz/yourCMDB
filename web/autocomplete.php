<?php
/********************************************************************
* This file is part of yourCMDB.
*
* Copyright 2013-2014 Michael Batz
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
/**
* WebUI helper: autocomplete
* @author Michael Batz <michael@yourcmdb.org>
*/

	//include functions
	require "include/base.inc.php";

	//get parameters
	$paramSearchstring = getHttpGetVar("term", "");
	$paramObject = getHttpGetVar("object", "");
	$paramVar1 = getHttpGetVar("var1", "");
	$paramVar2 = getHttpGetVar("var2", "");

	$values = array();

	//get data and create output
	$output = array();
	switch($paramObject)
	{
		case "object":
			$values = $datastore->getAllValuesOfObjectField($paramVar1, $paramVar2);
			foreach($values as $value)
			{
				//get suggestions for empty fields
				if($paramSearchstring == "")
				{
					$output[] = array("id" => $value, "label" => $value, "value" => $value);
				}
				else
				{
					if(strpos(strtolower($value), strtolower($paramSearchstring)) !== false)
					{
						$output[] = array("id" => $value, "label" => $value, "value" => $value);
					}
				}

				//max 10 suggestions
				if(count($output) >= 10)
				{
					break;
				}
			}
		break;

		case "quicksearch":
			//add entry for search in all objects
			$value = "search for $paramSearchstring in all objects";
			$output[] = array("id" => $value, "label" => $value, "value" => $value);

			//add entries for search in object groups
			foreach(array_keys($config->getObjectTypeConfig()->getObjectTypeGroups()) as $groupname)
			{
				$value = "search for $paramSearchstring in group $groupname";
				$output[] = array("id" => $value, "label" => $value, "value" => $value);
			}
		break;

		case "opensearch":
			//add search term
			$output[] = $paramSearchstring;

			//add entry for search in all objects
			$values = Array();
			$values[] = "search for $paramSearchstring in all objects";

			//add entries for search in object groups
			foreach(array_keys($config->getObjectTypeConfig()->getObjectTypeGroups()) as $groupname)
			{
				$values[] = "search for $paramSearchstring in group $groupname";
			}
			$output[] = $values;
		break;

	}

	//JSON output
	echo json_encode($output);
	
?>
