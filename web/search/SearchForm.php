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
* WebUI element: search form
* @author Michael Batz <michael@yourcmdb.org>
*/


	//get data
	$objectTypes = $config->getObjectTypeConfig()->getObjectTypeGroups();

	//searchstrings
	$searchstring0 = "";
	if(isset($paramSearchString[0]))
	{
		$searchstring0 = $paramSearchString[0];
	}

	//print messages if available
	if(isset($paramMessage) && $paramMessage != "")
	{
		printInfoMessage($paramMessage);
	}
	if(isset($paramError) && $paramError != "")
	{
		printErrorMessage($paramError);
	}

	//HTML output
	echo "<h1>";
	echo gettext("Search");
	echo "</h1>";




	echo "<div class=\"searchbar\">";
	echo "<form action=\"search.php\" method=\"get\" accept-charset=\"UTF-8\">";


	//search strings
	echo "<table id=\"searchbarStrings\">";
	echo "<tr>";
	echo "<td><input id=\"quicksearch\" type=\"text\" value=\"$searchstring0\" name=\"searchstring[]\" /></td>";
	echo "<td>&nbsp;</td>";
	echo "</tr>";
	//additional search strings
	for($i = 1; $i < count($paramSearchString); $i++)
	{
		echo "<tr>";
		echo "<td><input type=\"text\" name=\"searchstring[]\" value=\"{$paramSearchString[$i]}\" /></td>";
		echo "<td><a href=\"#\" onclick=\"javascript:searchbarRemoveField($(this).parent().parent())\"><img src=\"img/icon_delete.png\" alt=\"delete\"/></a></td>";
		echo "</tr>";
	}
	//add searchstring link
	echo "<tr>";
	echo "<td colspan=\"2\">";
	echo "<a href=\"#\" onclick=\"javascript:searchbarAddField('#searchbarStrings', '".gettext('searchstring')."', 'searchstring[]', '')\">";
	echo "<img src=\"img/icon_show.png\" alt=\"add searchstring\"/>add searchstring";
	echo "</a>";
	echo "</td>";
	echo "</tr>";
	echo "</table>";
	//active objects
	echo "<table id=\"searchbarOptions\">";
	echo "<tr>";
	echo "<td>".gettext("show inactive objects")."</td>";
	if($paramActiveOnly == "1")
	{
		echo "<td><input type=\"checkbox\" name=\"activeonly\" value=\"0\"/></td>";
	}
	else
	{
		echo "<td><input type=\"checkbox\" name=\"activeonly\" value=\"0\" checked=\"checked\" /></td>";
	}
	echo "</tr>";
	//objecttype group
	echo "<tr>";
	echo "<td>".gettext("objecttype group")."</td>";
	echo "<td><select name=\"typegroup\">";
	echo "<option></option>";
        foreach(array_keys($objectTypes) as $group)
        {
		if($paramTypeGroup == $group)
		{
                	echo "<option selected=\"selected\">$group</option>";
		}
		else
		{
                	echo "<option>$group</option>";
		}
        }
        echo "</select></td>";
	echo "</tr>";
	//objecttype
	echo "<tr>";
	echo "<td>".gettext("Type:")."</td>";
	echo "<td><select name=\"type\">";
	echo "<option></option>";
	foreach(array_keys($objectTypes) as $group)
	{
		echo "<optgroup label=\"$group\">";
		foreach($objectTypes[$group] as $type)
		{
			if($paramType == $type)
			{
				echo "<option selected=\"selected\">$type</option>";
			}
			else
			{
				echo "<option>$type</option>";
			}
		}
		echo "</optgroup>";
	}
	echo "</select></td>";
	echo "</tr>";
	echo "</table>";

	//searchbar footer
	echo "<p id=\"searchbarFooter\">";
	echo "<input type=\"submit\" value=\"".gettext("Go")."\" />";
	echo "<input type=\"reset\" value=\"".gettext("Clear")."\" />";
	echo "</p>";
	echo "</form>";
	echo "</div>";
?>
