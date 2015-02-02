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

//JavaScript functions for user settings section

/**
* Shows change password form
*/
function settingsUserDetailsChangePassword(button1Label, button2Label)
{
	var buttonDefs = {};
	//button 1
	buttonDefs[button1Label] = function()
	{
		openUrlAjax('settings/UserDetails.php?' + $( '#settingsUserDetailsChangePasswordForm' ).serialize(), '#settingsTabUserDetails', false, true);
		$( this ).remove();
	};
	//button 2
	buttonDefs[button2Label] = function()
	{
		$( this ).dialog("close");
	};

	//create dialog
	$( "#settingsUserDetailsChangePassword"  ).dialog
	(
		{
			modal:	true,
			buttons:buttonDefs,
			width: 	500
		}
	);
};
