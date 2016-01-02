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
* WebUI element: search
* @author Michael Batz <michael@yourcmdb.org>
*/
	use yourCMDB\exceptions\CmdbObjectNotFoundException;

	//include base
	include "include/bootstrap-web.php";
	include "include/auth.inc.php";

	//include header
	include "include/htmlheader.inc.php";
	include "include/cmdbheader.inc.php";

	//load search result and search form using AJAX
	$paramQueryString = $_SERVER['QUERY_STRING'];
	echo "<div id=\"searchbarResult\">";
	echo "<script type=\"text/javascript\">";
	echo "cmdbOpenUrlAjax('search/SearchResult.php?$paramQueryString', '#searchbarResult', false, true);";
	echo "</script>";
	echo "</div>";

	//include footer
	include "include/cmdbfooter.inc.php";
	include "include/htmlfooter.inc.php";
?>
