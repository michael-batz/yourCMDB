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
namespace yourCMDB\config;

use yourCMDB\printer\PrinterOptions;
use yourCMDB\labelprinter\LabelOptions;
use yourCMDB\labelprinter\LabelprinterConfigurationException;

/**
* Class for access to labelprinter configuration
* @author Michael Batz <michael@yourcmdb.org>
*/
class LabelprinterConfig
{
	//array with configured labelprinter names for printing
	private $labelprintersPrint;

	//array with configured labelprinter names for showing labels
	private $labelprintersShow;

	//array with Label objects: format labelprintername -> Label
	private $labels;

	//array with Printer objects: format labelprintername -> Printer
	private $printer;

	/**
	* creates a LabelprinterConfig object from xml file labelprinter-configuration.xml
	*/
	public function __construct($xmlfile)
	{
		//initialize arrays
		$this->labelprintersPrint = Array();
		$this->labelprintersShow = Array();
		$this->labels = Array();
		$this->printer = Array();
		
		//read XML configuration
		$xmlobject = simplexml_load_file($xmlfile);
		foreach($xmlobject->xpath('//labelprinter') as $labelprinter)
		{
			//get labelprinter name
			$labelprinterName = (string) $labelprinter['name'];

			//create Label objects
			foreach($labelprinter[0]->label as $label)
			{
				$labelClass = "\\yourCMDB\\labelprinter\\".(string) $label['class'];
				$labelOptions = new LabelOptions();
				foreach($label[0]->parameter as $parameter)
				{
					$parameterKey = (string) $parameter['key'];
					$parameterValue = (string) $parameter['value'];
					$labelOptions->addOption($parameterKey, $parameterValue);
				}
				$this->labels[$labelprinterName] = new $labelClass($labelOptions);
			}

			//create Printer objects
			foreach($labelprinter[0]->printer as $printer)
			{
				$printerClass = "\\yourCMDB\\printer\\".(string) $printer['class'];
				$printerOptions = new PrinterOptions();
				foreach($printer[0]->parameter as $parameter)
				{
					$parameterKey = (string) $parameter['key'];
					$parameterValue = (string) $parameter['value'];
					$printerOptions->addOption($parameterKey, $parameterValue);
				}
				$this->printer[$labelprinterName] = new $printerClass($printerOptions);
			}

			//save labelprinter name
			if(isset($this->printer[$labelprinterName]))
			{
				$this->labelprintersPrint[] = $labelprinterName;
			}
			else
			{
				$this->labelprintersShow[] = $labelprinterName;
			}
		}
	}

	/**
	* Returns an array with names of all configured labelprinters which are used to print labels on a printer
	* @return array 	Array with names of all configured labelprinters
	*/
	public function getLabelprinterNamesForPrinting()
	{
		return $this->labelprintersPrint;
	}

	/**
	* Returns an array with names of all configured labelprinters which are used to show labels on WebUI
	* @return array 	Array with names of all configured labelprinters
	*/
	public function getLabelprinterNamesForShowing()
	{
		return $this->labelprintersShow;
	}

	/**
	* Returns a Label Object for the given labelprinter name
	* @throws LabelprinterConfigurationException	if no label was found for the given labelprinter name
	* @param string $labelprinterName	name of the label printer
	* @return \yourCMDB\labelprinter\Label	Label object for the given label printer name
	*/
	public function getLabelObject($labelprinterName)
	{
		if(!isset($this->labels[$labelprinterName]))
		{
			throw new LabelprinterConfigurationException("No labels for labelprinter $labelprinterName found");
		}
		return $this->labels[$labelprinterName];
	}

	/**
	* Returns a Printer Object for the given labelprinter name
	* @throws LabelprinterConfigurationException	if no printer was found for the given labelprinter name
	* @param string $labelprinterName	name of the label printer
	* @return \yourCMDB\printer\Printer	Printer object for the given label printer name
	*/
	public function getPrinterObject($labelprinterName)
	{
		if(!isset($this->printer[$labelprinterName]))
		{
			throw new LabelprinterConfigurationException("No printer for labelprinter $labelprinterName found");
		}
		return $this->printer[$labelprinterName];
	}

}
?>
