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

//JavaScript functions for search bar



/**
* clear search form
*/
function searchbarClear()
{
	//clear other input fields
	$( "#searchbarForm input[name='searchstring']" ).val('');
	$( "#searchbarForm input[name='activeonly']" ).prop('checked', false);
	$( "#searchbarForm select[name='typegroup']" ).val('');
	$( "#searchbarForm select[name='type']" ).val('');
};

function searchbarSubmit(selectorForm, selectorResult)
{
	var url = 'search/SearchResult.php?' + $( selectorForm ).serialize();
	openUrlAjax(url, selectorResult, true, true);
};
