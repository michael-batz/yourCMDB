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
* WebUI element: search bar
* @author Michael Batz <michael@yourcmdb.org>
*/


	echo "<div class=\"box\">";
	echo "<h1>";
	echo gettext("Search");
	echo "</h1>";

	//Search by AssetID
	echo "<form action=\"object.php\" method=\"get\" accept-charset=\"UTF-8\">";
	echo "<p>";
	echo gettext("Asset ID:");
	echo "<br />";
	echo "<input type=\"text\" name=\"id\" />";
	echo "<input type=\"hidden\" name=\"action\" value=\"show\" />";
	echo "<input type=\"submit\" value=\"".gettext("Go")."\" />";
	echo "</p>";
	echo "</form>";

	//Search by field value
	echo "<form action=\"search.php\" method=\"get\" accept-charset=\"UTF-8\">";
	echo "<p>";
	echo gettext("Searchstring:");
	echo "<br />";
	echo "<input id=\"quicksearchInput\" type=\"text\" name=\"searchstring[]\" ";
	echo "		onfocus=\"javascript:showAutocompleter('#quicksearchInput', 'autocomplete.php?object=quicksearch')\"/>";
	echo "<input type=\"submit\" value=\"".gettext("Go")."\" />";
	echo "</p>";
	echo "</form>";


	echo "</div>";

?>
