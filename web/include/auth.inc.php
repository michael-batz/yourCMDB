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
* WebUI element: authenication with user session
*/

	session_start();
	$baseUrl = $config->getViewConfig()->getBaseUrl();
	$authProvider = $config->getSecurityConfig()->getAuthProvider("web");
	//set defaults
	$authAuthenticated = false;
	$authUser = "";
	$authAccessgroup = "";

	//get HTTP POST vars for authentication
	$authUserPost = getHttpPostVar("authUser", "");
	$authPasswordPost = getHttpPostVar("authPassword", "");

	//check, if user is already authenticated
	if(isset($_SESSION['authAuthenticated'], $_SESSION['authUser'], $_SESSION['authAccessgroup']) && $_SESSION['authAuthenticated'] == true)
	{
		//if user is authenticated, set session vars
		$authAuthenticated = true;
		$authUser = $_SESSION['authUser'];
		$authAccessgroup = $_SESSION['authAccessgroup'];
	}
	//try to authenticate if HTTP POST vars are set
	elseif($authUserPost != "")
	{
		//authentication function
		if($authProvider->authenticate($authUserPost,$authPasswordPost))
		{
			$authAuthenticated = true;
			$authUser = $authUserPost;
			$authAccessgroup = $authProvider->getAccessGroup($authUser);
			$_SESSION['authAuthenticated'] = true;
			$_SESSION['authUser'] = $authUser;
			$_SESSION['authAccessgroup'] = $authAccessgroup;
		}
		//redirect to login page with error flag
		else
		{
			header("Location: $baseUrl/login.php?error=1");
			exit();
		}
	
	}
	
	//check authentication
	if(!$authAuthenticated)
	{
		header("Location: $baseUrl/login.php");
		exit();
	}
?>
