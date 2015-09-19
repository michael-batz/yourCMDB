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
* WebUI element: preview of import
* @author Michael Batz <michael@yourcmdb.org>
*/

use yourCMDB\fileimporter\Importer;
use yourCMDB\fileimporter\FileImportException;
use yourCMDB\fileimporter\FileImportOptionsRequiredException;

	//required parameters: $paramFilename, $paramFormat, $importOptions

	//save uploaded file in temp directory
	if($paramFilename == "")
	{
		$paramFilename = "../tmp/".time().".import";
		move_uploaded_file($_FILES['file']['tmp_name'], $paramFilename);
	}


	$fileImporter = new Importer($paramFilename, $paramFormat, $importOptions);
	$previewData = null;
	try
	{
		//get data for preview
		$previewData = $fileImporter->getPreviewData();

	}
	catch(FileImportException $e)
	{
		//print error
		$paramError = gettext("Could not read from uploaded file. Please check permissions.");
		include "Form.php";
	}
	catch(FileImportOptionsRequiredException $e)
	{
		//doing nothing
	}

	//show import options page for import format
	switch($paramFormat)
	{
		case "ImportFormatCsv":
			include "formats/PreviewCsv.php";
			break;

	}

?>

