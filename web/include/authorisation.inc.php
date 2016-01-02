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
* WebUI element: check authorisation for given application part
* The following variables need to be set:
* - $authAccessgroup
* - $authorisationAppPart
* - $authorisationProvider
*/

	//check authorisation
	$authorisationResult = $authorisationProvider->authorise($authAccessgroup, $authorisationAppPart);

	if($authorisationResult != 2)
	{
		//unauthorised
		header("Location: $baseUrl/unauthorised.php");
		exit();
	}
?>
