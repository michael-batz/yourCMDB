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
* autoloading of classes
*
*/
function __autoload($className)
{
	$scriptBaseDir = dirname(__FILE__);
	$coreBaseDir = realpath("$scriptBaseDir/../../core");
	$paths = array('', 'security', 'model', 'config', 'controller', 'libs', 'exporter', 'taskscheduler', 'rest');
	$filename = $className.'.php';
	foreach($paths as $path)
	{
		if(file_exists("$coreBaseDir/$path/$filename"))
		{
			include "$coreBaseDir/$path/$filename";
		}
	}
}

/**
* gets an HTTP GET variable or returns a default value
*/
function getHttpGetVar($variableName, $defaultValue)
{
	if(isset($_GET["$variableName"]))
	{
		return $_GET["$variableName"];
	}
	else
	{
		return $defaultValue;
	}
}

/**
* gets an HTTP POST variable or returns a default value
*/
function getHttpPostVar($variableName, $defaultValue)
{
	if(isset($_POST["$variableName"]))
	{
		return $_POST["$variableName"];
	}
	else
	{
		return $defaultValue;
	}
}

/**
* Prints an info message
*/
function printInfoMessage($message)
{
	echo "<p class=\"message-info\" alt=\"Info\" id=\"message-info\" onClick=\"javascript:hideElement('#message-info')\"><img src=\"img/icon_message.png\" class=\"icon\" />$message</p>";
}

/**
* Prints an error message
*/
function printErrorMessage($message)
{
	echo "<p class=\"message-error\" alt=\"Error\" id=\"message-error\" onClick=\"javascript:hideElement('#message-error')\"><img src=\"img/icon_error.png\" class=\"icon\" />$message</p>";
}

//get configuration
$controller = new Controller();
$config = $controller->getCmdbConfig();
$datastore = $controller->getDatastore();
$installTitle = $config->getViewConfig()->getInstallTitle();

//setup i18n with gettext
$i18nLocale = $config->getViewConfig()->getLocale();
$i18nDomain = "web";
$i18nCodeset = "utf-8";
$scriptBaseDir = dirname(__FILE__);
$i18nBaseDir = realpath("$scriptBaseDir/../../i18n");

setlocale(LC_ALL, $i18nLocale);
bindtextdomain($i18nDomain, $i18nBaseDir);
bind_textdomain_codeset($i18nDomain, $i18nCodeset);
textdomain($i18nDomain);
?>
