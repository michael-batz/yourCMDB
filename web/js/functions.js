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

//definition of functions

/**
* Loads an url
*/
function openUrl(url)
{
	location.href=url;
};

/**
* AJAX loader
*/
function openUrlAjax(url, selector, scrollTo, showWaitingAnimation)
{
	if(showWaitingAnimation)
	{
		$( selector ).html('<p class="waiting"><img src="img/waiting.gif" /></p>');
	}
	$( selector ).load(url);
	if(scrollTo)
	{
		scrollToElement(selector);
	}
};

/**
* Scroll to specific element
*/
function scrollToElement(selector)
{
	$( 'html, body' ).animate({scrollTop: $( selector ).offset().top}, 'slow');
};



/**
* Ask for confirmation for an action
*/
function showConfirmation(urlAction, button1Label, button2Label)
{
	var buttonDefs = {};
	buttonDefs[button1Label] = function(){openUrl(urlAction);};
	buttonDefs[button2Label] = function(){$( this ).dialog("close");};
	$( "#jsConfirm" ).dialog({
			modal: true,
			buttons:buttonDefs,
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
* Show Datepicker on input fields (use with JS event handler onfocus)
*/
function showDatepicker(id)
{
	$( id  ).datepicker
	({
		changeMonth:	true,
		changeYear:	true,
		dateFormat:	"dd.mm.yy"
	});
	$( id ).datepicker('show');
};


/**
* Hides an element
*/
function hideElement(id)
{
	$( id ).hide('slide', {}, 1000);
};


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

	//generate accordion
	$(function()
	{
		$( "#jsAccordion" ).accordion
		(
			{
				heightStyle: "content"
			}
		);
	});

	//setup event handler scroller
	$(window).scroll(function()
	{
		if($(window).scrollTop() > 100)
		{
			$( "#jsScroller" ).show('fade', 1000);
		}
		else
		{
			$( "#jsScroller" ).hide('fade', 1000);
		}
	});

};

//start JqueryUi
jqueryUiStart();
