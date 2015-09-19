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
* WebUI element: import from file after preview
* @author Michael Batz <michael@yourcmdb.org>
*/
use yourCMDB\fileimporter\Importer;
use \Exception;


	//required parameters: $paramFilename, $paramFormat, $importOptions
	$fileImporter = new Importer($paramFilename, $paramFormat, $importOptions);
	$output = 0;
	try
	{
		//get data for preview
		$output = $fileImporter->import();

	}
	catch(Exception $e)
	{
		//print error
		$output = "error";
	}

	//output
	echo $output;

?>

