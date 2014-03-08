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

//definition of functions

/**
* Loads an url
*/
function openUrl(url)
{
	location.href=url;
};

/**
* Ask for confirmation for an action
*/
function showConfirmation(urlAction)
{
	$( "#jsConfirm" ).dialog({
			modal: true,
			buttons: {
					"Yes": function(){openUrl(urlAction);},
					"Canel": function(){$( this ).dialog("close");},
				}
		});
};

/**
* Show Autocompleter on input fields (use with JS event handler onfocus)
*/
function showAutocompleter(id, source)
{
	$( id  ).autocomplete
	({
		source: source
	});
};

/**
* Hides an element
*/
function hideElement(id)
{
	$( id ).hide('slide', {}, 1000);
}


function jqueryUiStart()
{

	//generate tab view
	$(function() 
	{
		$( "#jsTabs" ).tabs();
	});

	//generate menu
	$(function() 
	{
		$( "#jsMenu" ).menu();
	});

};

//start JqueryUi
jqueryUiStart();
