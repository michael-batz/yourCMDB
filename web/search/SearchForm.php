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

	//title
	echo "<h2>";
	echo gettext("Advanced Search Options");
	echo "</h2>";


	//HTML output
	echo "<form id=\"searchbarForm\" class=\"form-horizontal\" action=\"javascript:void(0);\" method=\"get\" accept-charset=\"UTF-8\" onsubmit=\"javascript:cmdbSearchbarSubmit('#searchbarForm','#searchbarResult')\">";
	//default search field
	echo "<div class=\"form-group\">";
	echo "<label class=\"col-md-2 col-md-offset-3 control-label\">".gettext("search for text")."</label>";
	echo "<div class=\"col-md-4\">";
	echo "<input class=\"form-control\" type=\"text\" value=\"$filterValueText\" name=\"text\" id=\"searchbarSearchstring\" />";
	echo "</div>";
	echo "</div>";
	//active objects
	/*echo "<div class=\"form-group\">";
	echo "<label class=\"col-md-2 col-md-offset-3 control-label\">".gettext("show inactive objects")."</label>";
	echo "<div class=\"col-md-4\">";
	if($paramActiveOnly == "1")
	{
		echo "<input class=\"form-control\" type=\"checkbox\" name=\"status\" value=\"0\"/>";
	}
	else
	{
		echo "<input class=\"form-control\" type=\"checkbox\" name=\"status\" value=\"0\" checked=\"checked\" />";
	}
	echo "</div>";
	echo "</div>";*/
	//object types positive filter
	/*echo "<div class=\"form-group\">";
	echo "<label class=\"col-md-2 col-md-offset-3 control-label\">".gettext("Type:")."</label>";
	echo "<div class=\"col-md-4\">";
	foreach($paramTypes as $paramType)
	{
		echo "<span id=\"cmdbSearchFormObjPos-$paramType\">";
		echo "<input class=\"form-control\" type=\"hidden\" name=\"type[]\" value=\"$paramType\">";
		echo "<span class=\"label label-default\">$paramType ";
		echo "<a href=\"javascript:cmdbRemoveElement('#cmdbSearchFormObjPos-$paramType')\">";
		echo "<span class=\"glyphicon glyphicon-remove\"></span></a>";
		echo "</span>";
		echo "</span>";
		echo "<br />";
	}
	echo "</div>";
	echo "</div>";

	//object types negative filter
	echo "<div class=\"form-group\">";
	echo "<label class=\"col-md-2 col-md-offset-3 control-label\">".gettext("Not Type:")."</label>";
	echo "<div class=\"col-md-4\">";
	foreach($paramNotTypes as $paramNotType)
	{
		echo "<span id=\"cmdbSearchFormObjNeg-$paramNotType\">";
		echo "<input class=\"form-control\" type=\"hidden\" name=\"notType[]\" value=\"$paramNotType\">";
		echo "<span class=\"label label-default\">$paramNotType ";
		echo "<a href=\"javascript:cmdbRemoveElement('#cmdbSearchFormObjNeg-$paramNotType')\">";
		echo "<span class=\"glyphicon glyphicon-remove\"></span></a>";
		echo "</span>";
		echo "</span>";
		echo "<br />";
	}
	echo "</div>";
	echo "</div>";*/

	//searchform footer
	echo "<div class=\"form-group\">";
	echo "<div class=\"col-md-4 col-md-offset-5\">";
	echo "<input type=\"submit\" class=\"btn btn-danger\" value=\"".gettext("Go")."\" />";
	echo "<input type=\"button\" class=\"btn btn-default\" value=\"".gettext("Clear Search")."\" onclick=\"javascript:cmdbSearchbarClear()\" />";
	echo "</div>";
	echo "</div>";
	echo "</form>";
?>
