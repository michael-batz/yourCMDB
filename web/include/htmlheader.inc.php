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
* HTML header for yourCMDB WebUI
* @author Michael Batz <michael@yourcmdb.org>
*/
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<!-- bootstrap setup -->
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />

		<!-- favicon -->
		<link rel="shortcut icon" type="image/x-icon" href="favicon.ico" />

		<!-- OpenSearch plugin -->
		<link rel="search" type="application/opensearchdescription+xml" title="yourCMDB QuickSearch" href="opensearch-plugin.php" />

		<!-- CSS: bootstrap, typeahead, smartmenues, bootstrap-datepicker and yourCMDB custom -->
		<link href="css/bootstrap.min.css" rel="stylesheet" />
		<link href="css/jquery.smartmenus.bootstrap.css" rel="stylesheet" />
		<link href="css/typeahead.css" rel="stylesheet" />
		<link href="css/bootstrap-datepicker.css" rel="stylesheet" />
		<link href="css/yourcmdb.css" rel="stylesheet" />

		<!-- JS: jQuery, bootstrap, typeahead, smartmenues, bootstrap-datepicker and yourCMDB custom -->
		<script src="js/jquery-1.11.2.min.js"></script>
		<script src="js/bootstrap.min.js"></script>
		<script src="js/typeahead.bundle.min.js"></script>
		<script src="js/jquery.smartmenus.min.js"></script>
		<script src="js/jquery.smartmenus.bootstrap.min.js"></script>
		<script src="js/bootstrap-datepicker.min.js"></script>
		<script src="js/yourcmdb.js"></script>

		<title><?php echo $installTitle; ?></title>
	</head>
	<body>
		<noscript><p>You need to enable JavaScript for yourCMDB.</p></noscript>

