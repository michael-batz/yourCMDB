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
* WebUI plugin: OpenSearch Plugin
* @author Michael Batz <michael@yourcmdb.org>
*/

        //load WebUI base
        require "include/bootstrap-web.php";

	//get baseUrl from config
	$baseUrl = $config->getViewConfig()->getBaseUrl();

	//print xml header
	echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
?>
	<OpenSearchDescription xmlns="http://a9.com/-/spec/opensearch/1.1/" xmlns:moz="http://www.mozilla.org/2006/browser/search/">
		<ShortName>yourCMDB</ShortName>
		<Description>yourCMDB QuickSearch</Description>
		<InputEncoding>UTF-8</InputEncoding>
		<Image width="16" height="16" type="image/x-icon"><?php echo $baseUrl; ?>/favicon.ico</Image>
		<Url type="text/html" method="GET" template="<?php echo $baseUrl; ?>/search.php?searchstring={searchTerms}" />
		<Url type="application/x-suggestions+json" method="GET" template="<?php echo $baseUrl; ?>/autocomplete.php?object=opensearch&amp;term={searchTerms}" />
	</OpenSearchDescription>
