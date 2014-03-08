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
* WebUI element: error page
* @author Michael Batz <michael@yourcmdb.org>
*/

	//print messages if available
	if(isset($paramMessage) && $paramMessage != "")
	{
		printInfoMessage($paramMessage);
	}
	if(isset($paramError) && $paramError != "")
	{
		printErrorMessage($paramError);
	}


?>


	<h1>yourCMDB Error</h1>

	<p>
		The error above should not be happened. Maybe you use a wrong URL or you found a bug.<br />
		Please check your setup or ask for help on the <a href="http://www.yourcmdb.org">yourCMDB Website</a>.
	</p>
