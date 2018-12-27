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
* WebUI element: show object
* @author Michael Batz <michael@yourcmdb.org>
*/
	use yourCMDB\qrcode\QrCodeGenerator;
	use yourCMDB\helper\VariableSubstitution;

	//this page needs the following variable to be set: $object

	//get object ressources
	$objectLinks = $objectLinkController->getObjectLinks($object, $authUser);
	$objectRefs = $objectController->getObjectReferences($paramId, $authUser);
	$objectLogEntries = $objectLogController->getLogEntries($object, 31, 0, $authUser);
	$objectEvents = $config->getObjectTypeConfig()->getObjectEvents($object->getType());
	$objectExternalLinks = $config->getObjectTypeConfig()->getObjectLinks($object->getType());
	$objectLabelPrintersPrint = $config->getLabelprinterConfig()->getLabelprinterNamesForPrinting();
	$objectLabelPrintersShow = $config->getLabelprinterConfig()->getLabelprinterNamesForShowing();

	//create output strings
	$urlList = "object.php?action=list&amp;type=".$object->getType();
	$urlNew = "object.php?action=add&amp;type=".$object->getType();
	$urlDuplicate = "object.php?action=add&amp;type=".$object->getType()."&amp;id=".$object->getId();
	$urlEdit = "object.php?action=edit&amp;type=".$object->getType()."&amp;id=".$object->getId();
	$urlDelete = "object.php?action=delete&amp;id=".$object->getId();
	$urlQrCode = $config->getViewConfig()->getQrCodeUrlPrefix() .$object->getId();
	$urlLabelPrinting = "objectlabel.php?id=".$object->getId()."&amp;";
	$statusImage = "<span class=\"label label-success\" title=\"".gettext("active object")."\">A</span>";
	if($object->getStatus() == 'N')
	{
		$statusImage = "<span class=\"label label-danger\" title=\"".gettext("inactive object")."\">N</span>";
	}
	$textTitle = "$statusImage ". $object->getType() ." #". $object->getId();

	//create QRcode
	$qrcode = new QrCodeGenerator($urlQrCode, $config->getViewConfig()->getQrCodeEccLevel());

	//static comment
	$staticObjectComment = $config->getObjectTypeConfig()->getStaticFieldValue($object->getType(), "comment");
	

	//<!-- confirmation for deleting this object  -->
	echo "<div class=\"modal fade\" id=\"confirmDelete\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"confirmDeleteLabel\" aria-hidden=\"true\">";
	echo "<div class=\"modal-dialog\">";
	echo "<div class=\"modal-content\">";
	//confirmation: header
	echo "<div class=\"modal-header\">";
	echo "<button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>";
	echo "<h4 class=\"modal-title\" id=\"confirmDeleteLabel\">".gettext("Are you sure...?")."</h4>";
	echo "</div>";
	//confirmation: body
	echo "<div class=\"modal-body\">";
        echo "<p>";
	echo gettext("Do you really want to delete this object?");
	$countObjectRefs = count($objectRefs);
	if($countObjectRefs > 0)
	{
		echo sprintf(gettext("	There are %s objects that reference to this object. 
					If you delete this object, all references will be set to null."), 
				$countObjectRefs);
	}
	echo "</p>";
	echo "</div>";
	//confirmation: footer
	echo "<div class=\"modal-footer\">";
	echo "<a href=\"$urlDelete\"class=\"btn btn-danger\">".gettext("delete")."</a>";
	echo "<button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">".gettext("cancel")."</button>";
	echo "</div>";
	echo "</div>";
	echo "</div>";
	echo "</div>";


	//<!-- submenu  -->
	echo "<ul class=\"nav nav-pills\">";
	echo "<li><a href=\"$urlList\"><span class=\"glyphicon glyphicon-th-list\"></span>".gettext("object list")."</a></li>";
	echo "<li><a href=\"$urlNew\"><span class=\"glyphicon glyphicon-plus\"></span>".gettext("add new object")."</a></li>";
	echo "<li><a href=\"$urlDuplicate\"><span class=\"glyphicon glyphicon-forward\"></span>".gettext("duplicate")."</a></li>";
	echo "<li><a href=\"$urlEdit\"><span class=\"glyphicon glyphicon-pencil\"></span>".gettext("edit")."</a></li>";
	echo "<li><a href=\"#\" data-toggle=\"modal\" data-target=\"#confirmDelete\"><span class=\"glyphicon glyphicon-trash\"></span>".gettext("delete")."</a></li>";
	echo "<li class=\"dropdown\"><a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\" role=\"button\" aria-expanded=\"false\">";
        echo "<span class=\"glyphicon glyphicon-print\"></span>".gettext("print label")."<span class=\"caret\"></span></a>";
	echo "<ul class=\"dropdown-menu\">";
	//label printer show
	foreach($objectLabelPrintersShow  as $objectLabelPrinter)
	{
		$urlLabelPrintingEntry = $urlLabelPrinting ."action=show&amp;labelprinter=$objectLabelPrinter";
		echo "<li><a href=\"$urlLabelPrintingEntry\" target=\"_blank\">";
		echo "<span class=\"glyphicon glyphicon-open-file\"></span>".gettext("show as")." $objectLabelPrinter</a></li>";
	}
	//label printer print
	foreach($objectLabelPrintersPrint  as $objectLabelPrinter)
	{
		$urlLabelPrintingEntry = $urlLabelPrinting ."action=print&amp;labelprinter=$objectLabelPrinter";
		$urlLabelPrintingJs = "javascript:cmdbAjaxActionWithStatus('$urlLabelPrintingEntry', '#messagebar')";
		echo "<li><a href=\"$urlLabelPrintingJs\"><span class=\"glyphicon glyphicon-print\"></span>".gettext("print on")." $objectLabelPrinter</a></li>";
	}
	echo "</ul>";
	echo "</li>";
	echo "</ul>";

	//print messagebar
	include "include/messagebar.inc.php";

	echo "<div class=\"container\" id=\"cmdb-objecttable\">";
	echo "<div class=\"row\" id=\"cmdb-objecttable-head\">";
	echo "<h1 class=\"text-center\">$textTitle</h1>";
	echo "</div>";


	//<!-- object header -->
	echo "<div class=\"row\">";
	//<!-- label with qr code-->
	echo "<div class=\"col-md-3\">";
	echo "<img src=\"data:image/png;base64,".base64_encode($qrcode->getPngImage())."\" alt=\"".gettext("QR-Code for object")."\" />";
	echo "</div>";
	
	//<!-- summary fields -->
	echo "<div class=\"col-md-6\">";
	echo "<h2>";
	echo gettext("Summary");
	echo "</h2>";
	echo "<table class=\"table table-condensed cmdb-cleantable cmdb-table2cols\">";
	foreach(array_keys($config->getObjectTypeConfig()->getSummaryFields($object->getType())) as $summaryfield)
	{
		$fieldLabel = $config->getObjectTypeConfig()->getFieldLabel($object->getType(), $summaryfield);
		$fieldValue = $object->getFieldValue($summaryfield);
		$fieldType = $config->getObjectTypeConfig()->getFieldType($object->getType(), $summaryfield);
		echo "<tr>";
		echo "<td>$fieldLabel</td>";
		echo "<td>";
		echo showFieldForDataType($object->getType(), $summaryfield, $fieldValue, $fieldType, false);
		echo "</td>";
		echo "</tr>";
	}
	echo "</table>";
	echo "</div>";
	
	//<!-- additional information -->
	echo "<div class=\"col-md-3\">";
	//<!-- Object External links -->
	if(count($objectExternalLinks) > 0)
	{
		echo "<div>";
		echo "<h2>";
		echo gettext("External Links");
		echo "</h2>";
		echo "<ul class=\"cmdb-linklist\">";
		foreach($objectExternalLinks as $objectExternalLink)
		{
			$objectExternalLinkName = $objectExternalLink['name'];
			$objectExternalLinkHref = VariableSubstitution::substituteObjectVariables($objectExternalLink['href'], $object, true, 1);

			//only show link, if 1 var in link was not empty or the link has no vars
			if($objectExternalLinkHref != "")
			{
				echo "<li><a href=\"$objectExternalLinkHref\" target=\"_blank\"><span class=\"glyphicon glyphicon-new-window\"></span>$objectExternalLinkName</a></li>";
			}
		}
		echo "</ul>";
		echo "</div>";
	}

	//<!-- Object custom events -->
	if(count($objectEvents) > 0)
	{
		echo "<div>";
		echo "<h2>";
		echo gettext("Custom Events");
		echo "</h2>";
		echo "<ul class=\"cmdb-linklist\">";
		foreach($objectEvents as $objectEvent)
		{
			$objectEventName = $objectEvent['name'];
			$objectEventLabel = $objectEvent['label'];
			$objectEventUrl = "object.php?action=sendEvent&amp;event=$objectEventName&amp;id=".$object->getId();
			echo "<li><a href=\"$objectEventUrl\"><span class=\"glyphicon glyphicon-new-window\"></span>$objectEventLabel</a></li>";
		}
		echo "</ul>";
		echo "</div>";
	}

	//<!-- object comment -->
	if($staticObjectComment != "")
	{
		//replace variables
		$staticObjectComment = VariableSubstitution::substituteObjectVariables($staticObjectComment, $object);

		echo "<div class=\"comment\">";
		echo "<h2>";
		echo gettext("Comment");
		echo "</h2>";
		echo "<p>$staticObjectComment</p>";
		echo "</div>";
	}
	echo "</div>";

	echo "</div>";


	//<!-- start with tab view -->
	echo "<div class=\"row\">";
	echo "<div role=\"tabpanel\">";
	echo "<ul class=\"nav nav-tabs\" role=\"tablist\">";
	echo "<li role=\"presentation\" class=\"active\"><a href=\"#tabs-1\" aria-controls=\"tabs-1\" role=\"tab\" data-toggle=\"tab\">".gettext("Fields")."</a></li>";
	echo "<li role=\"presentation\"><a href=\"#tabs-2\" aria-controls=\"tabs-2\" role=\"tab\" data-toggle=\"tab\">".gettext("Referenced by")."</a></li>";
	echo "<li role=\"presentation\"><a href=\"#tabs-3\" aria-controls=\"tabs-3\" role=\"tab\" data-toggle=\"tab\">".gettext("Links")."</a></li>";
	echo "<li role=\"presentation\"><a href=\"#tabs-4\" aria-controls=\"tabs-4\" role=\"tab\" data-toggle=\"tab\">".gettext("Log")."</a></li>";
	echo "</ul>";
	echo "<div class=\"tab-content\">";

	//Tab: object fields
	echo "<div role=\"tabpanel\" class=\"tab-pane fade in active\" id=\"tabs-1\">";
	echo "<table class=\"table table-hover cmdb-cleantable cmdb-table2cols\">";
	foreach($config->getObjectTypeConfig()->getFieldGroups($object->getType()) as $groupname)
	{
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
	}
	echo "</table>";
	echo "</div>";
	
	//Tab: object references
	echo "<div role=\"tabpanel\" class=\"tab-pane fade\" id=\"tabs-2\">";
	echo "<table class=\"table table-hover cmdb-cleantable\">";
	echo "<tr>";
	echo "<th>".gettext("AssetID")."</th>";
	echo "<th>".gettext("Type")."</th>";
	echo "<th>".gettext("Summary")."</th>";
	echo "<th>".gettext("Action")."</th>";
	echo "</tr>";
	//<!-- print object references -->
	foreach($objectRefs as $refObject)
	{
		$refObjectType = $refObject->getType();
        $refObjectId = $refObject->getId();
        $refObjectSummary = getObjectSummary($refObject);
		$urlLinkShow = "object.php?action=show&amp;id=$refObjectId";
		echo "<tr>";
		echo "<td>$refObjectId</td>";
		echo "<td>$refObjectType</td>";
		echo "<td>$refObjectSummary</td>";
		echo "<td>";
		echo "<a href=\"$urlLinkShow\"><span class=\"glyphicon glyphicon-eye-open\" title=\"".gettext("show object with reference")."\"></span></a>&nbsp;&nbsp;&nbsp;";
		echo "</td>";
		echo "</tr>";
	}
	echo "</table>";
	echo "</div>";

	//Tab: object links
	echo "<div role=\"tabpanel\" class=\"tab-pane fade\" id=\"tabs-3\">";
	echo "<table class=\"table table-hover cmdb-cleantable\">";
	echo "<tr>";
	echo "<th>".gettext("AssetID")."</th>";
	echo "<th>".gettext("Type")."</th>";
	echo "<th>".gettext("Action")."</th>";
	echo "</tr>";
	foreach($objectLinks as $objectLink)
	{
		$objectLinkLinkedObject = $objectLink->getObjectA();
		if($objectLinkLinkedObject->getId() == $object->getId())
		{
			$objectLinkLinkedObject = $objectLink->getObjectB();
		}
		$linkObjectType = $objectLinkLinkedObject->getType();
		$linkObjectId = $objectLinkLinkedObject->getId();
		$urlLinkShow = "object.php?action=show&amp;id=$linkObjectId";
		$urlLinkDelete = "object.php?action=deleteLink&amp;id=$paramId&amp;idb=$linkObjectId";
		echo "<tr>";
		echo "<td>$linkObjectId</td>";
		echo "<td>$linkObjectType</td>";
		echo "<td>";
		echo "<a href=\"$urlLinkShow\"><span class=\"glyphicon glyphicon-eye-open\" title=\"".gettext("show linked object")."\"></span></a>&nbsp;&nbsp;&nbsp;";
		echo "<a href=\"$urlLinkDelete\"><span class=\"glyphicon glyphicon-trash\" title=\"".gettext("delete link")."\"></span></a>";
		echo "</td>";
		echo "</tr>";
	}
	echo "</table>";
	//<!-- form for adding new object links -->
	echo "<form action=\"object.php\" method=\"get\" accept-charset=\"UTF-8\">";
	echo "<p class=\"text-center\">";
	echo gettext("Add new link to object with ID:");
	echo "<input type=\"text\" name=\"idb\" />";
	echo "<input type=\"hidden\" name=\"id\" value=\"$paramId\" />";
	echo "<input type=\"hidden\" name=\"action\" value=\"addLink\" />";
	echo "<input type=\"submit\" value=\"".gettext("Go")."\" />";
	echo "</p>";
	echo "</form>";
	echo "</div>";

	//Tab: object log
	echo "<div role=\"tabpanel\" class=\"tab-pane fade table-responsive\" id=\"tabs-4\">";
	echo "<table class=\"table table-hover cmdb-cleantable\">";
	echo "<tr>";
	echo "<th>".gettext("Date")."</th>";
	echo "<th>".gettext("Action")."</th>";
	echo "<th>".gettext("Description")."</th>";
	echo "<th>".gettext("User")."</th>";
	echo "</tr>";
	$i = 0;
	foreach($objectLogEntries as $logEntry)
	{
		echo "<tr>";
		echo "<td class=\"cmdb-nowrap\">".$logEntry->getTimestamp()->format("Y-m-d H:i")."</td>";
		echo "<td class=\"cmdb-nowrap\">".htmlspecialchars($logEntry->getAction())."</td>";
		echo "<td>".htmlspecialchars($logEntry->getDescription())."</td>";
		echo "<td>".htmlspecialchars($logEntry->getUser())."</td>";
		echo "</tr>";
		$i++;
		if($i >= 30)
		{
			echo "<tr><td colspan=\"4\">";
			echo "more...";
			echo "</td></tr>";
			break;
		}
	}

	echo "</table>";
	echo "</div>";

	//close tab content
	echo "</div>";
	echo "</div>";
	echo "</div>";
	echo "</div>";
