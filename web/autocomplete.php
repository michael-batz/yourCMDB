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
			$values = $datastore->getAllFieldValues($paramVar1, $paramVar2, $paramSearchstring, 10);
			foreach($values as $value)
			{
				$output[] = array("id" => $value, "label" => $value, "value" => $value);
			}
		break;

		case "quicksearch":
			$values = $datastore->getAllFieldValues(null, null, $paramSearchstring, 10);
			foreach($values as $value)
			{
				$output[] = array("id" => $value, "label" => $value, "value" => $value);
			}
		break;

		case "opensearch":
			$values = $datastore->getAllFieldValues(null, null, $paramSearchstring, 10);
			foreach($values as $value)
			{
				$output[] = array("id" => $value, "label" => $value, "value" => $value);
			}
		break;
	}

	//JSON output
	echo json_encode($output);
	
?>
