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
/**
* WebUI helper: autocomplete
* @author Michael Batz <michael@yourcmdb.org>
*/

	//include functions
	include "include/bootstrap-web.php";
	include "include/auth.inc.php";

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
		//var1 : fieldkey
		case "object":
			$values = $objectController->getAllFieldValues(null, $paramVar1, $paramSearchstring, 10, "yourCMDB-API");
			foreach($values as $value)
			{
				$output[] = $value;
			}
		break;

		case "quicksearch":
			//check, if searchstring consists of multiple words
			$searchstring = $paramSearchstring;
			$searchstringFirstPart = "";
			if(preg_match("/(.*) (.*?)$/", $paramSearchstring, $matches) === 1)
			{
				$searchstring = $matches[2];
				$searchstringFirstPart = $matches[1];
			}
			$values = $objectController->getAllFieldValues(null, null, $searchstring, 10, $authUser);
			foreach($values as $value)
			{
				if($paramSearchstring != $searchstring)
				{
					$value = "$searchstringFirstPart $value";
				}
				$output[] = $value;
			}
		break;

		case "opensearch":
			//add searchterm
			$output[] = $paramSearchstring;

			//add suggestions
			//check, if searchstring consists of multiple words
			$searchstring = $paramSearchstring;
			$searchstringFirstPart = "";
			if(preg_match("/(.*) (.*?)$/", $paramSearchstring, $matches) === 1)
			{
				$searchstring = $matches[2];
				$searchstringFirstPart = $matches[1];
			}
			$values = $objectController->getAllFieldValues(null, null, $searchstring, 10, $authUser);
			$editedValues = Array();
			foreach($values as $value)
			{
				if($paramSearchstring != $searchstring)
				{
					$value = "$searchstringFirstPart $value";
				}
				$editedValues[] = $value;
			}
			$output[] = $editedValues;
		break;
	}

	//JSON output
	echo json_encode($output);
	
?>
