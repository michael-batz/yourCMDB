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
* WebUI element: search form
* @author Michael Batz <michael@yourcmdb.org>
*/

	//panel headline
	echo "<div class=\"container\">";
	echo "<div class=\"panel panel-default cmdb-contentpanel\">";
	echo "<div class=\"panel-heading\">";
	echo "<h3 class=\"panel-title text-center\">";
	echo gettext("Search");
	echo "</h3>";
	echo "</div>";

	//start panel content
	echo "<div class=\"panel-body\">";


	//HTML output
	echo "<form id=\"searchbarForm\" class=\"form-horizontal\" action=\"javascript:void(0);\" method=\"get\" accept-charset=\"UTF-8\" onsubmit=\"javascript:cmdbSearchbarSubmit('#searchbarForm','#searchbarResult')\">";
	//default  search field
	echo "<div class=\"form-group\">";
	echo "<label class=\"col-md-2 col-md-offset-3 control-label\">".gettext("searchstring")."</label>";
	echo "<div class=\"col-md-4\">";
	echo "<input class=\"form-control\" type=\"text\" value=\"$paramSearchString\" name=\"searchstring\" id=\"searchbarSearchstring\" ";
	echo "			onfocus=\"javascript:showAutocompleter('#searchbarSearchstring', 'autocomplete.php?object=quicksearch')\"/>";
	echo "</div>";
	echo "</div>";
	//active objects
	echo "<div class=\"form-group\">";
	echo "<label class=\"col-md-2 col-md-offset-3 control-label\">".gettext("show inactive objects")."</label>";
	echo "<div class=\"col-md-4\">";
	if($paramActiveOnly == "1")
	{
		echo "<input class=\"form-control\" type=\"checkbox\" name=\"activeonly\" value=\"0\"/>";
	}
	else
	{
		echo "<input class=\"form-control\" type=\"checkbox\" name=\"activeonly\" value=\"0\" checked=\"checked\" />";
	}
	echo "</div>";
	echo "</div>";
	//objecttype group
	echo "<div class=\"form-group\">";
	echo "<label class=\"col-md-2 col-md-offset-3 control-label\">".gettext("objecttype group")."</label>";
	echo "<div class=\"col-md-4\">";
	if($paramActiveOnly == "1")
	echo "<select name=\"typegroup\" class=\"form-control\">";
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
        echo "</select>";
	echo "</div>";
	echo "</div>";
	//objecttype
	echo "<div class=\"form-group\">";
	echo "<label class=\"col-md-2 col-md-offset-3 control-label\">".gettext("Type:")."</label>";
	echo "<div class=\"col-md-4\">";
	echo "<select name=\"type\" class=\"form-control\">";
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
	echo "</select>";
	echo "</div>";
	echo "</div>";

	//searchform footer
	echo "<div class=\"form-group\">";
	echo "<div class=\"col-md-4 col-md-offset-5\">";
	echo "<input type=\"submit\" class=\"btn btn-default\" value=\"".gettext("Go")."\" />";
	echo "<input type=\"button\" class=\"btn btn-danger\" value=\"".gettext("Clear Search")."\" onclick=\"javascript:cmdbSearchbarClear()\" />";
	echo "</div>";
	echo "</div>";
	echo "</form>";
?>
