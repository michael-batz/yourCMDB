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
* yourCMDB bootstrap
* must be included
* @author Michael Batz <michael@yourcmdb.org>
*/

//define base directories
$scriptBaseDir = dirname(__FILE__);
$coreBaseDir = realpath("$scriptBaseDir");

//configure class loading
require_once "ClassLoader.php";
//class loading: Doctrine
new ClassLoader("Doctrine\Common", "$coreBaseDir/libs/composer/vendor/doctrine/common/lib");
new ClassLoader("Doctrine\Common\Cache", "$coreBaseDir/libs/composer/vendor/doctrine/cache/lib");
new ClassLoader("Doctrine\Common\Collections", "$coreBaseDir/libs/composer/vendor/doctrine/collections/lib");
new ClassLoader("Doctrine\Common\Annotations", "$coreBaseDir/libs/composer/vendor/doctrine/annotations/lib");
new ClassLoader("Doctrine\Common\Lexer", "$coreBaseDir/libs/composer/vendor/doctrine/lexer/lib");
new ClassLoader("Doctrine\Common\Inflector", "$coreBaseDir/libs/composer/vendor/doctrine/inflector/lib");
new ClassLoader("Doctrine\DBAL", "$coreBaseDir/libs/composer/vendor/doctrine/dbal/lib");
new ClassLoader("Doctrine\ORM", "$coreBaseDir/libs/composer/vendor/doctrine/orm/lib");
//class loading: Doctrine Migrations
new ClassLoader("Doctrine\DBAL\Migrations", "$coreBaseDir/libs/composer/vendor/doctrine/migrations/lib");
//class loading: yourCMDB
new ClassLoader("yourCMDB", "$coreBaseDir");
//class loading: QR code helper class
new ClassLoader("Endroid\QrCode", "$coreBaseDir/libs/QrCode/src");
//class loading: FPDF
new ClassLoader("fpdf", "$coreBaseDir/libs");
?>
