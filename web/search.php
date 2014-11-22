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
* WebUI element: search
* @author Michael Batz <michael@yourcmdb.org>
*/


	//get header
	include "include/base.inc.php";
	include "include/auth.inc.php";
	include "include/htmlheader.inc.php";
	include "include/yourcmdbheader.inc.php";

	//search functions
	include "search/SearchFunctions.php";

	//show search form
	include "search/SearchForm.php";

	//load search result using AJAX
	include "search/SearchResultFrame.php";

	//include footer
	include "include/yourcmdbfooter.inc.php";
	include "include/htmlfooter.inc.php";
?>
