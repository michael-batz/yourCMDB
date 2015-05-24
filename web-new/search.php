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

	//search functions
	include "search/SearchFunctions.php";

	//searchbar function: assetId
	if(preg_match("/^assetid:([0-9]+)$/", $paramSearchString, $matches) === 1)
	{
		$paramId = $matches[1];
		try
		{
			$object= $objectController->getObject($paramId, $authUser);

			//show object page
			include "object/ObjectUiHelper.php";
			include "object/ShowObject.php";
			include "include/cmdbfooter.inc.php";
			include "include/htmlfooter.inc.php";
			exit();
		}
		catch(CmdbObjectNotFoundException $e)
		{
			//show error message and search form
			$paramError = sprintf(gettext("No object with AssetID %s found..."), $paramId);
			include "include/messagebar.inc.php";
		}
	}

	//show search form
	include "search/SearchForm.php";

	//load search result using AJAX
	include "search/SearchResultFrame.php";

	//include footer
	include "include/cmdbfooter.inc.php";
	include "include/htmlfooter.inc.php";
?>
