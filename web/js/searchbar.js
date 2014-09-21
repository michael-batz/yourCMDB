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

//JavaScript functions for search bar

/**
* Show SearchBar (use with JS event handler onfocus)
*/
function showSearchBar(id)
{
	$( id  ).show('blind');
};

/**
* Hide SearchBar (use with JS event handler onblur)
*/
function hideSearchBar(id)
{
	$( id  ).hide('blind');
};

/**
* add search field
*/
function searchbarAddField(id, caption, name, value)
{
	var htmlstring;
	htmlstring = '<tr>';
	htmlstring += '<td>' + caption + '</td>';
	htmlstring += '<td><input type="text" name="' + name + '" value="' + value + '"><input type="button" value="remove" onclick="javascript:searchbarRemoveField($(this).parent().parent())"/></td>';
	htmlstring += '</tr>';
	$( id  ).add(htmlstring).prependTo( id );
};

/**
* add search field
*/
function searchbarRemoveField(id)
{
	$( id  ).remove();
};
