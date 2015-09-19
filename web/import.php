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
* WebUI element: import actions
* @author Michael Batz <michael@yourcmdb.org>
*/
use yourCMDB\fileimporter\ImportOptions;

	//get header
	include "include/bootstrap-web.php";
	include "include/auth.inc.php";
	include "include/htmlheader.inc.php";
	include "include/cmdbheader.inc.php";

	//get parameters
	$paramAction = getHttpPostVar("action", "form");
	$paramFormat = getHttpPostVar("format", "");
	$paramFilename = getHttpPostVar("filename", "");

	//set import options from URL/POST data
	$importOptions = new ImportOptions;
	foreach(array_keys($_POST) as $parameter)
	{
		if($parameter != "action" && $parameter != "format" && $parameter != "filename")
		{
			$importOptions->addOption($parameter, $_POST[$parameter]);
		}
	}

	//load page for action
	switch($paramAction)
	{
		case "form":
			include "import/Form.php";
			break;

		case "preview":
			include "import/Preview.php";
			break;

		case "import":
			include "import/Import.php";
			break;
	}

	//include footer
	include "include/cmdbfooter.inc.php";
	include "include/htmlfooter.inc.php";
?>
