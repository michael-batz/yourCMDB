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
* WebUI element: show label of a CmdbObject
* loaded directly
* @author Michael Batz <michael@yourcmdb.org>
*/

	//include base functions and search form
	include "include/bootstrap-web.php";
	include "include/auth.inc.php";

	use yourCMDB\labelprinter\LabelPrinter;
	use yourCMDB\labelprinter\LabelprinterConfigurationException;
	use yourCMDB\printer\exceptions\PrinterNotFoundException;
	use yourCMDB\printer\exceptions\PrintUnauthorizedException;
	use yourCMDB\printer\exceptions\PrinterErrorException;
	use yourCMDB\printer\exceptions\PrintException;
	use \Exception;

	//get parameters
	$paramId = getHttpGetVar("id", 0);
	$paramAction = getHttpGetVar("action", "print");
	$paramLabelprinter = getHttpGetVar("labelprinter", "");

	$status = 0;
	$statusMessage = "";

	switch($paramAction)
	{
		case "print":
			try
			{
				//print label on labelprinter
				$object= $objectController->getObject($paramId, $authUser);
				$labelPrinter = new LabelPrinter($object, $paramLabelprinter);
				$labelPrinter->printLabel();
				$status = 0;
				$statusMessage = sprintf(gettext("Label successfully printed on %s"), $paramLabelprinter);
			}
			catch(LabelprinterConfigurationException $e)
			{
				$status = 1;
				$statusMessage = sprintf(gettext("Labelprinter with name %s not found"), $paramLabelprinter);
			}
			catch(PrinterNotFoundException $e)
			{
				$status = 1;
				$statusMessage = sprintf(gettext("Error printing label on %s. The configured printer could not be found."), $paramLabelprinter);
			}
			catch(PrintUnauthorizedException $e)
			{
				$status = 1;
				$statusMessage = sprintf(gettext("Error printing label on %s. Insufficient rights for printing on that device."), $paramLabelprinter);
			}
			catch(PrinterErrorException $e)
			{
				$status = 1;
				$statusMessage = sprintf(gettext("Error printing label on %s. There was a problem with the printer device."), $paramLabelprinter);
			}
			catch(Exception $e)
			{
				$status = 1;
				$statusMessage = sprintf(gettext("Error printing label on %s. Internal Error."), $paramLabelprinter);
			}

			//create output JSON
			$result = Array();
			$result['status'] = $status;
			$result['status_message'] = $statusMessage;

			//return output
			echo json_encode($result);
		break;

		case "show":
			try
			{
				//show label on WebUI
				$object= $objectController->getObject($paramId, $authUser);
				$labelPrinter = new LabelPrinter($object, $paramLabelprinter);
				$contentType = $labelPrinter->getLabelContentType();
				$content = $labelPrinter->getLabelContent();
	
				//return content
				header("Content-Type: $contentType");
				echo $content;
			}
			catch(Exception $e)
			{
				//show error message
				include "include/htmlheader.inc.php";
				include "include/cmdbheader.inc.php";
				$paramError = sprintf(gettext("Error showing label for object %s and label printer %s"), $paramId, $paramLabelprinter);
				include "error/Error.php";
				include "include/cmdbfooter.inc.php";
				include "include/htmlfooter.inc.php";
			}
	}

?>
