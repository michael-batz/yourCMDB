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
use yourCMDB\fileimporter\FileImportException;
use yourCMDB\fileimporter\FileImportOptionsRequiredException;


	//required parameters: $paramFilename, $paramFormat, $importOptions

	//get number of objects to import
	$fileImporter = new Importer($paramFilename, $paramFormat, $importOptions);
	$countObjectsToImport = $fileImporter->getObjectsToImportCount();

	echo gettext("Importing objects...");

	//load worker
	$data = $_POST;
	$data['action'] = "importWorker";
	$postDataString = json_encode($data);
	echo "<script type=\"text/javascript\">";
	echo "cmdbFileimporterImport($postDataString, '#cmdbFileimporterResult', $countObjectsToImport)";
	echo "</script>";

	//result
	echo "<div class=\"progress\">";
	echo "<div class=\"progress-bar progress-bar-success\" id=\"cmdbFileimporterResult\">";
	echo "</div>";
	echo "</div>";

?>

