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
* yourCMDB JavaScript functions
* @author Michael Batz <michael@yourcmdb.org>
*/

/**
* AJAX loader
*/
function cmdbOpenUrlAjax(url, selector, scrollTo, showWaitingAnimation)
{
	if(showWaitingAnimation)
	{
		$( selector ).html('<p class="waiting"><img src="img/waiting.gif" /></p>');
	}
	$( selector ).load(url);
	if(scrollTo)
	{
		cmdbScrollToElement(selector);
	}
};

/**
* Scroll to specific element
*/
function cmdbScrollToElement(selector)
{
	$( 'html, body' ).animate({scrollTop: $( selector ).offset().top}, 'slow');
};

/**
* show password in a password field
*/
function cmdbShowPassword(id)
{
	//get value
	var value = $( id ).attr("value");
	var parentObject = $( id ).parent();
	$( id  ).remove();
	parentObject.html(value);
	
};

/**
* create password in the given input field
*/
function cmdbCreatePassword(id)
{
	//create new password
	var alphabet = "abcdefghijklmnopqrstuvwxyz1234567890";
	var length = 12;
	var password = '';
	for(var i = 0; i < length; i++)
	{
		password = password + alphabet.charAt(cmdbRandomBetweenInt(0, alphabet.length - 1));
	}
	$( id  ).attr('value', password);
};

function cmdbRandomBetweenInt(min, max)
{
	return Math.floor(Math.random() * (max - min + 1)) - min;
};

/**
* clear search form
*/
function cmdbSearchbarClear()
{
	//clear other input fields
	$( "#searchbarForm input[name='searchstring']" ).val('');
	$( "#searchbarForm input[name='activeonly']" ).prop('checked', false);
	$( "#searchbarForm select[name='typegroup']" ).val('');
	$( "#searchbarForm select[name='type']" ).val('');
};

function cmdbSearchbarSubmit(selectorForm, selectorResult)
{
	var url = 'search/SearchResult.php?' + $( selectorForm ).serialize();
	cmdbOpenUrlAjax(url, selectorResult, true, true);
};

/**
* Hide modal form and open URL by ajax
*/
function cmdbSubmitModal(selectorModal, url, selectorTarget, scrollTo, showWaitingAnimation)
{
	$(selectorModal).modal('hide');
	$(selectorModal).on('hidden.bs.modal', function()
	{
		cmdbOpenUrlAjax(url, selectorTarget, scrollTo, showWaitingAnimation);
	});
}

/**
* start some JavaScript functionality on startup
*/
function cmdbJsStart()
{

	//start typeahead.js autocomplete
	$(function()
	{
		//autocomplete for object fields
		$( "input.typeahead-object"  ).each(function()
		{
			var cmdbSuggestionsFieldvalues = new Bloodhound(
			{
				queryTokenizer:	Bloodhound.tokenizers.whitespace,
				datumTokenizer: Bloodhound.tokenizers.whitespace,
				remote: 
				{
					url: 		'autocomplete.php?object=object&var1=' + this.id + '&term=%QUERY%',
					wildcard:	'%QUERY%',
					cache:		false
				}
			});


			$(this).typeahead
			(
				{
					hint:		true,
					minLength:	1,
					highlight:	true
				},
				{
					source:		cmdbSuggestionsFieldvalues,
				}
			);
		});

		//autocomplete for searchbar
		$( "input.typeahead-searchbar"  ).each(function()
		{
			var cmdbSuggestionsAssetids = new Bloodhound(
			{
				queryTokenizer:	Bloodhound.tokenizers.whitespace,
				datumTokenizer: Bloodhound.tokenizers.whitespace,
				remote: 
				{
					url: 		'autocomplete.php?object=assetids&term=%QUERY%',
					wildcard:	'%QUERY%',
					cache:		false
				}
			});

			var cmdbSuggestionsFieldvalues = new Bloodhound(
			{
				queryTokenizer:	Bloodhound.tokenizers.whitespace,
				datumTokenizer: Bloodhound.tokenizers.whitespace,
				remote: 
				{
					url: 		'autocomplete.php?object=quicksearch&var1=&term=%QUERY%',
					wildcard:	'%QUERY%',
					cache:		false
				}
			});
			
			$(this).typeahead
			(
				{
					hint:		true,
					minLength:	1,
					highlight:	true
				},
				{
					source:		cmdbSuggestionsAssetids,
					display:	'data',
					templates:
							{
								suggestion:	function(data)
										{
											var output;
											output = '<div><span class="glyphicon glyphicon-barcode"></span>';
											output += data.value;
											output += '</div>';
											return output;
										}
							}
				},
				{
					source:		cmdbSuggestionsFieldvalues,
					templates:
							{
								suggestion:	function(data)
										{
											var output;
											output = '<div><span class="glyphicon glyphicon-search"></span>';
											output += data;
											output += '</div>';
											return output;
										}
							}
				}
			);
		});


	});

	//start bootstap-datepicker
	$(function()
	{
		$( "input.datepicker-object"  ).each(function()
		{
			$(this).datepicker
			({
				format:		'dd.mm.yyyy'
			});
		});
	});

	//start event handler for modal delete confirmation
	$(function()
	{
		$( "#confirmDeleteList" ).on('show.bs.modal', function (event)
		{
			var source = $(event.relatedTarget);
			var linkDelete = source.data('linkdelete');
			$( "#modalButtonGo" ).attr("href", linkDelete);
		});
	});

}


//startup
cmdbJsStart();

/*****************************************************************************
* ToDo: old javascript
*****************************************************************************/

/**
* Loads an url
*/
function openUrl(url)
{
	location.href=url;
};


/**
* Hides an element
*/
function hideElement(id)
{
	$( id ).hide('slide', {}, 1000);
};

/**
* remove an element
*/
function removeElement(id)
{
	$( id  ).remove();
};


/*function cmdbJsStart()
{

	//autofocus for login page
	$(function()
	{
		$( "#loginUsername" ).focus();
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

};*/

