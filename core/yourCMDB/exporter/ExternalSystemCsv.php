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
namespace yourCMDB\exporter;

use yourCMDB\entities\CmdbObject;

/**
* Export API - External System
* Write CMDB objects to a CSV file
* @author Michael Batz <michael@yourcmdb.org>
*/
class ExternalSystemCsv implements ExternalSystem
{
	//ExportDestination
	private $destination;

	//ExportVariables
    private $variables;

    //csv filename
    private $filename;

    //csv delimiter
    private $delimiter;

    //csv enclosure
    private $enclosure;

    //output filehandle
    private $outputstream;

	public function setUp(ExportDestination $destination, ExportVariables $variables)
	{
		//save parameters
		$this->destination = $destination;
        $this->variables = $variables;

        //setup parameters
        $parameterKeys = $destination->getParameterKeys();
        $this->filename = "php://output";
        if(in_array("csv_filename", $parameterKeys))
        {
            $this->filename = $destination->getParameterValue("csv_filename");
        }
        $this->delimiter = ";";
        if(in_array("csv_delimiter", $parameterKeys))
        {
            $this->delimiter = $destination->getParameterValue("csv_delimiter");
        }
        $this->enclosure = "\"";
        if(in_array("csv_enclosure", $parameterKeys))
        {
            $this->enclosure = $destination->getParameterValue("csv_enclosure");
        }

        //create outputstram
        $this->outputstream = fopen($this->filename, 'w');

        //create CSV header
        fputcsv($this->outputstream, $this->variables->getVariableNames(), $this->delimiter, $this->enclosure);
	}

	public function addObject(\yourCMDB\entities\CmdbObject $object)
	{
        //get values of ExportVariables
        $values = [];
        foreach($this->variables->getVariableNames() as $field)
        {
		    $values[] = $this->variables->getVariable($field)->getValue($object);
        }

        //write values to CSV
        fputcsv($this->outputstream, $values, $this->delimiter, $this->enclosure);
	}

	public function finishExport()
    {
        //close outputstream
        fclose($this->outputstream);
    }
}
?>
