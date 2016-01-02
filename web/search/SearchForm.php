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
	$urlSearchForm = $urlBase . $searchFilter->getUrlQueryStringWithRemovedFilterTypes(Array("text", "status"));
	echo "<form id=\"searchbarForm\" class=\"form-horizontal\" action=\"javascript:void(0);\" method=\"get\" accept-charset=\"UTF-8\" onsubmit=\"javascript:cmdbSearchbarSubmit('$urlSearchForm', '#searchbarForm','#searchbarResult')\">";
	//default search field
	echo "<div class=\"form-group\">";
	echo "<label class=\"col-md-2 col-md-offset-3 control-label\">".gettext("search for text")."</label>";
	echo "<div class=\"col-md-4\">";
	echo "<input class=\"form-control\" type=\"text\" value=\"$filterValueText\" name=\"text\" id=\"searchbarSearchstring\" />";
	echo "</div>";
	echo "</div>";
	//status filter
	echo "<div class=\"form-group\">";
	echo "<label class=\"col-md-2 col-md-offset-3 control-label\">".gettext("only active objects")."</label>";
	echo "<div class=\"col-md-4\">";
	if($filterValueStatus == 'A')
	{
		echo "<input type=\"checkbox\" name=\"status\" value=\"A\" checked=\"checked\" />";
	}
	else
	{
		echo "<input type=\"checkbox\" name=\"status\" value=\"A\"/>";
	}
	echo "</div>";
	echo "</div>";

	//search filter
	echo "<div class=\"form-group\">";
	echo "<label class=\"col-md-2 col-md-offset-3 control-label\">".gettext("search filter")."</label>";
	echo "<div class=\"col-md-4\">";
	//search filter: positive object types
	foreach($filterValuesPosObjTypes as $filterValue)
	{
		$filter = "type=$filterValue";
		$urlRemovedFilter = $urlBase . $searchFilter->getUrlQueryStringWithRemovedFilter($filter);
		$urlJsRemovedFilter = "javascript:cmdbOpenUrlAjax('$urlRemovedFilter', '#searchbarResult', false, true)";
		echo "<span class=\"label label-default\">$filter ";
		echo "<a href=\"$urlJsRemovedFilter\" title=\"".gettext("remove filter")."\">";
		echo "<span class=\"glyphicon glyphicon-remove\"></span></a>";
		echo "</span>";
		echo "<br />";
	}
	//search filter: negative object types
	foreach($filterValuesNegObjTypes as $filterValue)
	{
		$filter = "notType=$filterValue";
		$urlRemovedFilter = $urlBase . $searchFilter->getUrlQueryStringWithRemovedFilter($filter);
		$urlJsRemovedFilter = "javascript:cmdbOpenUrlAjax('$urlRemovedFilter', '#searchbarResult', false, true)";
		echo "<span class=\"label label-default\">$filter ";
		echo "<a href=\"$urlJsRemovedFilter\" title=\"".gettext("remove filter")."\">";
		echo "<span class=\"glyphicon glyphicon-remove\"></span></a>";
		echo "</span>";
		echo "<br />";
	}
	echo "</div>";
	echo "</div>";


	//searchform footer
	$urlJsSearchFormClear = "javascript:cmdbOpenUrlAjax('$urlBase', '#searchbarResult', false, true)";
	echo "<div class=\"form-group\">";
	echo "<div class=\"col-md-4 col-md-offset-5\">";
	echo "<input type=\"submit\" class=\"btn btn-danger\" value=\"".gettext("Go")."\" />";
	echo "<input type=\"button\" class=\"btn btn-default\" value=\"".gettext("Clear Search")."\" onclick=\"$urlJsSearchFormClear\" />";
	echo "</div>";
	echo "</div>";
	echo "</form>";
?>
