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
	$( id  ).val(password);
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
* remove an element
*/
function cmdbRemoveElement(id)
{
	$( id  ).remove();
};

/**
* add access right entry
*/
function cmdbAdminAuthorisationEditGroupAddEntry(id)
{
	var htmlstring;
	var fieldid;
	fieldid = $( 'tr' ).length;
	htmlstring = '<tr id="adminAuthorisationEditGroupFieldnewAccess_' + fieldid + '">';
	htmlstring += '<td><input type="text" name="newAccess_' + fieldid + '" /></td>';
	htmlstring += '<td><select name="newAccessSelect_' + fieldid + '">';
	htmlstring += '<option value="0">no access</option>';
	htmlstring += '<option value="1">read only</option>';
	htmlstring += '<option value="2">read-write</option>';
	htmlstring += '</select></td>';
	htmlstring += '<td><a href="javascript:removeElement(\'#adminAuthorisationEditGroupFieldnewAccess_' + fieldid + '\')">';
	htmlstring += '<span class="glyphicon glyphicon-tresh" title="delete"></span></a></td>';
	htmlstring += '</tr>';
	$( id  ).add(htmlstring).appendTo( id );
};

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
					hint:		false,
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

	//start event handler for modal forms
	//adds addtional form elements for HTML data attributes started with 'form-'
	//loads dynamic form content if HTML data attribute 'dynform-url' is set
	$(document).bind('ready ajaxComplete', function()
	{
		$( ".modal" ).on('show.bs.modal', function (event)
		{
			var source = $(event.relatedTarget);
			//get all HTML 5 data attributes
			var data = source.data();
			//check for dynamic from content
			if('dynform' in data)
			{
				$(this).find("form").load(data['dynform']);
			}

			//check for additional form elements
			for(var i in data)
			{
				var regex = /form(.*)/;
				var matches = regex.exec(i);
				//only add form elements if the attribute starts with 'form-'
				if(matches != null)
				{
					var name = matches[1].toLowerCase();
					var value = data[i];
					var output = '<input type="hidden" name="' + name + '" value="' + value  + '">';
					//remove old element
					$(this).find("form :input[name='" + name  +"']").remove();
					//add new element
					$(this).find("form").append(output);
				}
			}
		});
	});

	//setup event handler scroller
	$(window).scroll(function()
	{
		if($(window).scrollTop() > 100)
		{
			$( "#cmdbScroller" ).show('slow');
		}
		else
		{
			$( "#cmdbScroller" ).hide('slow');
		}
	});

	//autofocus for login page
	$(function()
	{
		$( "#cmdbLoginUsername" ).focus();
	});

}


//startup
cmdbJsStart();
