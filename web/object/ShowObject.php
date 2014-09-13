<?php
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
/**
* WebUI element: show object
* @author Michael Batz <michael@yourcmdb.org>
*/

	//this page needs the following variable to be set: $object

	//get object ressources
	$objectLinks = array_merge($datastore->getObjectLinks($paramId), $datastore->getLinkedObjects($paramId));
	$objectRefs = $datastore->getObjectReferences($paramId);
	$objectEvents = $config->getObjectTypeConfig()->getObjectEvents($object->getType());
	$objectExternalLinks = $config->getObjectTypeConfig()->getObjectLinks($object->getType());

	//create output strings
	$urlList = "object.php?action=list&amp;type=".$object->getType();
	$urlNew = "object.php?action=add&amp;type=".$object->getType();
	$urlDuplicate = "object.php?action=add&amp;type=".$object->getType()."&amp;id=".$object->getId();
	$urlEdit = "object.php?action=edit&amp;type=".$object->getType()."&amp;id=".$object->getId();
	$urlDelete = "javascript:showConfirmation('object.php?action=delete&amp;id=".$object->getId()."')";
	$urlQrCode = $config->getViewConfig()->getQrCodeUrlPrefix() ."/shortlink.php?id=". $object->getId();
	$statusImage = "<img src=\"img/icon_active.png\" alt=\"".gettext("active")."\" title=\"".gettext("active object")."\"/>";
	if($object->getStatus() == 'N')
	{
		$statusImage = "<img src=\"img/icon_inactive.png\" alt=\"".gettext("inactive")."\" title=\"".gettext("inactive object")."\" />";
	}
	$textTitle = "$statusImage ". $object->getType() ." #". $object->getId();

	//create QRcode
	$qrcode = new QR($urlQrCode, $config->getViewConfig()->getQrCodeEccLevel());

	//static comment
	$staticObjectComment = $config->getObjectTypeConfig()->getStaticFieldValue($object->getType(), "comment");
	

	//<!-- confirmation for deleting this object  -->
	echo "<div class=\"blind\" id=\"jsConfirm\" title=\"".gettext("Are you sure?")."\">";
	echo "<p>";
	echo gettext("Do you really want to delete this object?");
	echo "</p>";
	$countObjectRefs = count($objectRefs);
	if($countObjectRefs > 0)
	{
		echo "<p>";
		echo sprintf(gettext("There are %s objects that reference to this object. If you delete this object, all references will be set to null."), $countObjectRefs);
		echo "</p>";
	}
	echo "</div>";

	//<!-- submenu  -->
	echo "<div class=\"submenu\">";
	echo "<a href=\"$urlList\">".gettext("object list")."</a>&nbsp;&nbsp;|&nbsp;&nbsp;";
	echo "<a href=\"$urlNew\">".gettext("add new object")."</a>&nbsp;&nbsp;|&nbsp;&nbsp;";
	echo "<a href=\"$urlDuplicate\">".gettext("duplicate")."</a>&nbsp;&nbsp;|&nbsp;&nbsp;";
	echo "<a href=\"$urlEdit\">".gettext("edit")."</a>&nbsp;&nbsp;|&nbsp;&nbsp;";
	echo "<a href=\"$urlDelete\">".gettext("delete")."</a>";
	echo "</div>";

	//<!-- print messages if available -->
	if(isset($paramMessage) && $paramMessage != "")
	{	
		printInfoMessage($paramMessage); 
	}
	if(isset($paramError) && $paramError != "")
	{	
		printErrorMessage($paramError); 
	}


	echo "<div class=\"objectbox\">";
	echo "<h1>$textTitle</h1>";


	//<!-- object header -->
	echo "<div class=\"objectheader\">";
	echo "<div class=\"objectheaderrow\">";
	//<!-- label with qr code-->
	echo "<div class=\"label\">";
	echo "<img src=\"data:image/gif;base64,".base64_encode($qrcode->image(4))."\" alt=\"".gettext("QR-Code for object")."\" />";
	echo "</div>";
	
	//<!-- summary fields -->
	echo "<div class=\"summary\">";
	echo "<h2>";
	echo gettext("Summary");
	echo "</h2>";
	echo "<table>";
	foreach(array_keys($config->getObjectTypeConfig()->getSummaryFields($object->getType())) as $summaryfield)
	{
		$fieldLabel = $config->getObjectTypeConfig()->getFieldLabel($object->getType(), $summaryfield);
		$fieldValue = $object->getFieldValue($summaryfield);
		echo "<tr><td>$fieldLabel</td><td>$fieldValue</td></tr>";
	}
	echo "</table>";
	echo "</div>";
	
	//<!-- additional information -->
	echo "<div class=\"additional\">";
	//<!-- Object External links -->
	if(count($objectExternalLinks) > 0)
	{
		echo "<div class=\"urls\">";
		echo "<h2>";
		echo gettext("External Links");
		echo "</h2>";
		echo "<ul>";
		foreach($objectExternalLinks as $objectExternalLink)
		{
			$objectExternalLinkName = $objectExternalLink['name'];
			$objectExternalLinkHref = preg_replace_callback("/%(.+?)%/", 
									function ($pregResult)
									{
										global $object; 
										return $object->getFieldValue($pregResult[1]);
									}, 
									$objectExternalLink['href']);
			echo "<li><a href=\"$objectExternalLinkHref\">$objectExternalLinkName</a></li>";
		}
		echo "</ul>";
		echo "</div>";
	}

	//<!-- Object custom events -->
	if(count($objectEvents) > 0)
	{
		echo "<div class=\"urls\">";
		echo "<h2>";
		echo gettext("Custom Events");
		echo "</h2>";
		echo "<ul>";
		foreach($objectEvents as $objectEvent)
		{
			$objectEventName = $objectEvent['name'];
			$objectEventLabel = $objectEvent['label'];
			$objectEventUrl = "object.php?action=sendEvent&amp;event=$objectEventName&amp;id=".$object->getId();
			echo "<li><a href=\"$objectEventUrl\">$objectEventLabel</a></li>";
		}
		echo "</ul>";
		echo "</div>";
	}

	//<!-- object comment -->
	if($staticObjectComment != "")
	{
		echo "<div class=\"comment\">";
		echo "<h2>";
		echo gettext("Comment");
		echo "</h2>";
		echo "<p>$staticObjectComment</p>";
		echo "</div>";
	}
	echo "</div>";

	echo "</div>";
	echo "</div>";


	//<!-- start with tab view -->
	echo "<div id=\"jsTabs\" class=\"objecttabs\">";
	echo "<ul>";
	echo "<li><a href=\"#tabs-1\">".gettext("Fields")."</a></li>";
	echo "<li><a href=\"#tabs-2\">".gettext("Referenced by")."</a></li>";
	echo "<li><a href=\"#tabs-3\">".gettext("Links")."</a></li>";
	echo "<li><a href=\"#tabs-4\">".gettext("Log")."</a></li>";
	echo "</ul>";

	//<!-- object fields -->
	echo "<div id=\"tabs-1\">";
	foreach($config->getObjectTypeConfig()->getFieldGroups($object->getType()) as $groupname)
	{
		echo "<table class=\"cols2\">";
		echo "<tr>";
		echo "<th colspan=\"2\">$groupname</th>";
		echo "</tr>";
		foreach(array_keys($config->getObjectTypeConfig()->getFieldGroupFields($object->getType(), $groupname)) as $field)
		{ 
			$fieldValue = $object->getFieldValue($field);
			$fieldName = $field;
			$fieldLabel = $config->getObjectTypeConfig()->getFieldLabel($object->getType(), $field);
			$fieldType = $config->getObjectTypeConfig()->getFieldType($object->getType(), $field);
			echo "<tr>";
			echo "<td>$fieldLabel:</td>";
			echo "<td>";
			echo showFieldForDataType($object->getType(), $fieldName, $fieldValue, $fieldType, false);
			echo "</td>";
			echo "</tr>";
		}
		echo "</table>";
	}
	echo "</div>";
	
	//<!-- object references -->
	echo "<div id=\"tabs-2\">";
	echo "<table>";
	echo "<tr>";
	echo "<th>".gettext("AssetID")."</th>";
	echo "<th>".gettext("Type")."</th>";
	echo "<th>".gettext("Action")."</th>";
	echo "</tr>";
	//<!-- print object references -->
	foreach($objectRefs as $refObjectId)
	{
		$refObjectType = $datastore->getObject($refObjectId)->getType();
		$urlLinkShow = "object.php?action=show&amp;id=$refObjectId";
		echo "<tr>";
		echo "<td>$refObjectId</td>";
		echo "<td>$refObjectType</td>";
		echo "<td>";
		echo "<a href=\"$urlLinkShow\"><img src=\"img/icon_show.png\" title=\"".gettext("show object with reference")."\" alt=\"".gettext("show")."\" /></a>&nbsp;&nbsp;&nbsp;";
		echo "</td>";
		echo "</tr>";
	}
	echo "</table>";
	echo "</div>";

	//<!-- object links -->
	echo "<div id=\"tabs-3\">";
	echo "<table>";
	echo "<tr>";
	echo "<th>".gettext("AssetID")."</th>";
	echo "<th>".gettext("Type")."</th>";
	echo "<th>".gettext("Action")."</th>";
	echo "</tr>";
	//<!-- print object links -->
	foreach($objectLinks as $linkObjectId)
	{
		$linkObjectType = $datastore->getObject($linkObjectId)->getType();
		$urlLinkShow = "object.php?action=show&amp;id=$linkObjectId";
		$urlLinkDelete = "object.php?action=deleteLink&amp;id=$paramId&amp;idb=$linkObjectId";
		echo "<tr>";
		echo "<td>$linkObjectId</td>";
		echo "<td>$linkObjectType</td>";
		echo "<td>";
		echo "<a href=\"$urlLinkShow\"><img src=\"img/icon_show.png\" title=\"".gettext("show linked object")."\" alt=\"".gettext("show")."\" /></a>&nbsp;&nbsp;&nbsp;";
		echo "<a href=\"$urlLinkDelete\"><img src=\"img/icon_delete.png\" title=\"".gettext("delete link")."\" alt=\"".gettext("delete")."\" /></a>";
		echo "</td>";
		echo "</tr>";
	}
	echo "</table>";
	//<!-- form for adding new object links -->
	echo "<form action=\"object.php\" method=\"get\" accept-charset=\"UTF-8\">";
	echo "<p>";
	echo gettext("Add new link to object with ID:");
	echo "<input type=\"text\" name=\"idb\" />";
	echo "<input type=\"hidden\" name=\"id\" value=\"$paramId\" />";
	echo "<input type=\"hidden\" name=\"action\" value=\"addLink\" />";
	echo "<input type=\"submit\" value=\"".gettext("Go")."\" />";
	echo "</p>";
	echo "</form>";
	echo "</div>";

	//<!-- object log -->
	echo "<div id=\"tabs-4\">";
	echo "<table>";
	echo "<tr>";
	echo "<th>".gettext("Date")."</th>";
	echo "<th>".gettext("Action")."</th>";
	echo "</tr>";
	$i = 0;
	foreach($datastore->getObjectLog($paramId)->getLogEntries() as $logEntry)
	{
		echo "<tr>";
		echo "<td>".$logEntry->getDate()."</td>";
		echo "<td>".$logEntry->getAction()."</td>";
		echo "</tr>";
		$i++;
		if($i >= 30)
		{
			echo "<tr><td colspan=\"2\">";
			echo "more...";
			echo "</td></tr>";
			break;
		}
	}

	echo "</table>";
	echo "</div>";
	echo "</div>";
	echo "</div>";
