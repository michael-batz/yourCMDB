<?php
/********************************************************************
* This file is part of yourCMDB.
*
* Copyright 2013-2016 Michael Batz
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
* WebUI element: load documentation
* @author Michael Batz <michael@yourcmdb.org>
*/

	//get header
	include "include/bootstrap-web.php";
	include "include/auth.inc.php";
	include "include/htmlheader.inc.php";
	include "include/cmdbheader.inc.php";

	//get parameters
	$paramDocument = getHttpPostVar("document", "admin-guide");

	//load document
	switch($paramDocument)
	{
		case "admin-guide":
			include "$docsBaseDir/admin-guide.html";
			break;

		case "development-guide":
			include "$docsBaseDir/development-guide.html";
			break;

		default:
			include "error/Error.php";
			break;
	}

	//load footer
	include "include/cmdbfooter.inc.php";
	include "include/htmlfooter.inc.php";

?>
