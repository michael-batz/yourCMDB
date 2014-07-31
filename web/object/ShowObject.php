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

	//get object links
	$objectLinks = array_merge($datastore->getObjectLinks($paramId), $datastore->getLinkedObjects($paramId));

	//get object references
	$objectRefs = $datastore->getObjectReferences($paramId);


	//create output strings
	$urlList = "object.php?action=list&amp;type=".$object->getType();
	$urlNew = "object.php?action=add&amp;type=".$object->getType();
	$urlDuplicate = "object.php?action=add&amp;type=".$object->getType()."&amp;id=".$object->getId();
	$urlEdit = "object.php?action=edit&amp;type=".$object->getType()."&amp;id=".$object->getId();
	$urlDelete = "javascript:showConfirmation('object.php?action=delete&amp;id=".$object->getId()."')";
	$urlQrCode = $config->getViewConfig()->getQrCodeUrlPrefix() ."/shortlink.php?id=". $object->getId();
	$statusImage = "<img src=\"img/icon_active.png\" alt=\"active\" title=\"active object\"/>";
	if($object->getStatus() == 'N')
	{
		$statusImage = "<img src=\"img/icon_inactive.png\" alt=\"inactive\" title=\"inactive object\" />";
	}
	$textTitle = "$statusImage ". $object->getType() ." #". $object->getId();

	//create QRcode
	$qrcode = new QR($urlQrCode, $config->getViewConfig()->getQrCodeEccLevel());

	//static comment
	$staticObjectComment = $config->getObjectTypeConfig()->getStaticFieldValue($object->getType(), "comment");
	
?>

	<!-- confirmation for deleting this object  -->
	<div class="blind" id="jsConfirm" title="Are you sure?">
		<p>Do you really want to delete this object?</p>
		<?php
			$countObjectRefs = count($objectRefs);
			if($countObjectRefs > 0)
			{
				echo "<p>There are $countObjectRefs objects that reference to this object. If you delete this object, all references will be set to null.</p>";
			}
		?>
	</div>

	<!-- submenu  -->
	<div class="submenu">
		<a href="<?php echo $urlList; ?>">object list</a>&nbsp;&nbsp;|&nbsp;&nbsp;
		<a href="<?php echo $urlNew; ?>">add new object</a>&nbsp;&nbsp;|&nbsp;&nbsp;
		<a href="<?php echo $urlDuplicate; ?>">duplicate</a>&nbsp;&nbsp;|&nbsp;&nbsp;
		<a href="<?php echo $urlEdit; ?>">edit</a>&nbsp;&nbsp;|&nbsp;&nbsp;
		<a href="<?php echo $urlDelete; ?>">delete</a>
	</div>

	<!-- print messages if available -->
	<?php 
		if(isset($paramMessage) && $paramMessage != "")
		{	
			printInfoMessage($paramMessage); 
		}
		if(isset($paramError) && $paramError != "")
		{	
			printErrorMessage($paramError); 
		}

	?>


	<div class="objectbox">
		<h1><?php echo $textTitle; ?></h1>


		<!-- object header -->
		<div class="objectheader">
			<div class="objectheaderrow">
				<!-- label with qr code-->
				<div class="label">
					<?php
					echo "<img src=\"data:image/gif;base64,".base64_encode($qrcode->image(4))."\" alt=\"QR-Code for object\" />";
					?>
				</div>
	
				<!-- summary fields -->
				<div class="summary">
					<h2>Summary</h2>
					<table>
						<?php
						foreach(array_keys($config->getObjectTypeConfig()->getSummaryFields($object->getType())) as $summaryfield)
						{
							$fieldLabel = $config->getObjectTypeConfig()->getFieldLabel($object->getType(), $summaryfield);
							$fieldValue = $object->getFieldValue($summaryfield);
							echo "<tr><td>$fieldLabel</td><td>$fieldValue</td></tr>";
						}?>
					</table>
				</div>
	
				<!-- additional information -->
				<div class="additional">
					<!-- Object External links -->
					<div class="urls">
						<h2>External Links</h2>
						<ul>
							<?php
							foreach($config->getObjectTypeConfig()->getObjectLinks($object->getType()) as $objectExternalLink)
							{
								$objectExternalLinkName = $objectExternalLink['name'];
								$objectExternalLinkHref = preg_replace_callback(
												"/%(.+?)%/", 
												function ($pregResult)
													{global $object; return $object->getFieldValue($pregResult[1]);}, 
												$objectExternalLink['href']);
								echo "<li><a href=\"$objectExternalLinkHref\">$objectExternalLinkName</a></li>";
							}
							?>
						</ul>
					</div>

					<!-- Object custom events -->
					<div class="urls">
						<h2>Custom Events</h2>
						<ul>
							<?php
							foreach($config->getObjectTypeConfig()->getObjectEvents($object->getType()) as $objectEvent)
							{
								$objectEventName = $objectEvent['name'];
								$objectEventLabel = $objectEvent['label'];
								$objectEventUrl = "object.php?action=sendEvent&amp;event=$objectEventName&amp;id=".$object->getId();
								echo "<li><a href=\"$objectEventUrl\">$objectEventLabel</a></li>";
							}
							?>
						</ul>
					</div>

					<!-- object comment -->
					<div class="comment">
						<h2>Comment</h2>
						<p><?php echo $staticObjectComment; ?></p>
					</div>
				</div>
			</div>
		</div>


		<!-- start with tab view -->
		<div id="jsTabs" class="objecttabs">
			<ul>
				<li><a href="#tabs-1">Fields</a></li>
				<li><a href="#tabs-2">Referenced by</a></li>
				<li><a href="#tabs-3">Links</a></li>
				<li><a href="#tabs-4">Log</a></li>
			</ul>
			<!-- object fields -->
			<div id="tabs-1">
				<?php
				foreach($config->getObjectTypeConfig()->getFieldGroups($object->getType()) as $groupname)
				{ ?>
					<table class="cols2">
					<tr>
						<th colspan="2"><?php echo $groupname;?></th>
					</tr>
					<?php
					foreach(array_keys($config->getObjectTypeConfig()->getFieldGroupFields($object->getType(), $groupname)) as $field)
					{ 
						$fieldValue = $object->getFieldValue($field);
						$fieldName = $field;
						$fieldLabel = $config->getObjectTypeConfig()->getFieldLabel($object->getType(), $field);
						$fieldType = $config->getObjectTypeConfig()->getFieldType($object->getType(), $field);
					  ?>
						<tr>
							<td><?php echo $fieldLabel;?>:</td>
							<td><?php showFieldForDataType($object->getType(), $fieldName, $fieldValue, $fieldType, false);?></td>
						</tr>
					<?php
					} ?>
	       			        </table>
				<?php
				} ?>
			</div>
	
			<!-- object references -->
			<div id="tabs-2">
				<table>
					<tr>
						<th>AssetID</th>
						<th>Type</th>
						<th>Action</th>
					</tr>
					<!-- print object references -->
					<?php
					foreach($objectRefs as $refObjectId)
					{
						$refObjectType = $datastore->getObject($refObjectId)->getType();
						$urlLinkShow = "object.php?action=show&amp;id=$refObjectId";
						?>
						<tr>
							<td><?php echo $refObjectId; ?></td>
							<td><?php echo $refObjectType; ?></td>
							<td>
								<a href="<?php echo $urlLinkShow;?>"><img src="img/icon_show.png" title="show object with reference" alt="show" /></a>&nbsp;&nbsp;&nbsp;
							</td>
						</tr>
					<?php
					}?>

				</table>

			</div>
			<!-- object links -->
			<div id="tabs-3">
				<table>
					<tr>
						<th>AssetID</th>
						<th>Type</th>
						<th>Action</th>
					</tr>
					<!-- print object links -->
					<?php
					foreach($objectLinks as $linkObjectId)
					{
						$linkObjectType = $datastore->getObject($linkObjectId)->getType();
						$urlLinkShow = "object.php?action=show&amp;id=$linkObjectId";
						$urlLinkDelete = "object.php?action=deleteLink&amp;id=$paramId&amp;idb=$linkObjectId";
						?>
						<tr>
							<td><?php echo $linkObjectId; ?></td>
							<td><?php echo $linkObjectType; ?></td>
							<td>
								<a href="<?php echo $urlLinkShow;?>"><img src="img/icon_show.png" title="show linked object" alt="show" /></a>&nbsp;&nbsp;&nbsp;
								<a href="<?php echo $urlLinkDelete;?>"><img src="img/icon_delete.png" title="delete link" alt="delete" /></a>
							</td>
						</tr>
					<?php
					}?>

				</table>

				<!-- form for adding new object links -->
				<form action="object.php" method="get" accept-charset="UTF-8">
					<p>Add new link to object with ID:
					<input type="text" name="idb" />
					<input type="hidden" name="id" value="<?php echo $paramId; ?>" />
					<input type="hidden" name="action" value="addLink" />
					<input type="submit" value="Go" />
					</p>
				</form>
			</div>

			<!-- object log -->
			<div id="tabs-4">
				<table>
					<tr>
						<th>Date</th>
						<th>Action</th>
					</tr>
					<?php
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
							echo "<tr><td colspan=\"2\">more...</td></tr>";
							break;
						}
					}
					?>
				</table>
			</div>

		</div>
	
	</div>
