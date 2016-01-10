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
namespace yourCMDB\labelprinter;

use yourCMDB\config\CmdbConfig;
use yourCMDB\entities\CmdbObject;

/**
* Prints a summary of a CMDB object on a configured LabelPrinter
* @author Michael Batz <michael@yourcmdb.org>
*/
class LabelPrinter
{

	//CmdbObject for creating a label
	private $cmdbObject;

	//name of the labelprinter
	private $labelprinterName;

	//label object
	private $label;

	//printer object
	private $printer;

	/**
	* creates a new label printer
	* @param CmdbObject $object		CmdbObject to print the label for
	* @param string $labelprinterName	name of the configured labelprinter
	* @throws LabelprinterConfigurationException if there is no configured label printer with the given name 
	*/
	public function __construct(\yourCMDB\entities\CmdbObject $object, $labelprinterName)
	{
		//init variables
		$this->cmdbObject = $object;
		$this->labelprinterName = $labelprinterName;

		//get label and printer objects
		$config = CmdbConfig::create();
		$this->label = $config->getLabelprinterConfig()->getLabelObject($this->labelprinterName);
		$this->printer = $config->getLabelprinterConfig()->getPrinterObject($this->labelprinterName);

		//init label
		$this->label->init($this->cmdbObject);
	}

	/**
	* prints the label on the configured printer
	*/
	public function printLabel()
	{
		//print label
		$this->printer->printData($this->label->getContent());
	}

	/**
	* returns the label content as string
	* @return string	label content
	*/
	public function getLabelContent()
	{
		return $this->label->getContent();
	}
}
?>
