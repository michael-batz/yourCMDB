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
* WebUI element: export data as CSV
* @author Michael Batz <michael@yourcmdb.org>
*/

//CSV write function
function writeCsv($outstream, $dataset, $delimiter, $enclosure)
{
	if($enclosure != "")
	{
		fputcsv($outstream, $dataset, $delimiter, $enclosure);
	}
	else
	{
		fputcsv($outstream, $dataset, $delimiter);
	}
}



$fieldnames = array_keys($config->getObjectTypeConfig()->getFields($paramType));

//get CSV parameters
$exportOptions = $config->getDataExchangeConfig()->getExportOptions("CSV");
$delimiter = ';';
$enclosure = "";
if(isset($exportOptions['delimiter']))
{
	$delimiter = $exportOptions['delimiter'];
}
if(isset($exportOptions['enclosure']))
{
	$enclosure = $exportOptions['enclosure'];
}

//send header
header("Content-Type: application/force-download");
header("Content-Disposition: attachment; filename=output.csv");

//open filehandler
$outstream = fopen("php://output", 'w');

//generate CSV header
$csvHeader = Array();
$csvHeader[] = "AssetID";
$csvHeader[] = "Active";
foreach($fieldnames as $fieldname)
{
	$csvHeader[] = $fieldname;
}
writeCsv($outstream, $csvHeader, $delimiter, $enclosure);

//generate CSV output data
foreach($objects as $object)
{
	$dataset = Array();
	$dataset[] = $object->getId();
	$dataset[] = $object->getStatus();
	foreach($fieldnames as $fieldname)
	{
		$dataset[] = $object->getFieldValue($fieldname);
	}
	writeCsv($outstream, $dataset, $delimiter, $enclosure);
}

//close filehandler
fclose($outstream);


?>

