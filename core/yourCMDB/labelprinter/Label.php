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

use yourCMDB\entities\CmdbObject;
use yourCMDB\config\CmdbConfig;
use qrcode\QR;

/**
* Label for a CMDB object
* @author Michael Batz <michael@yourcmdb.org>
*/
abstract class Label
{
	//options for creating a label
	protected $labelOptions;

	//CmdbObject for creating a label
	protected $cmdbObject;

	//label content: assetId of CmdbObject
	protected $contentAssetId;

	//label content: summary fields of CmdbObject
	protected $contentSummaryFields;

	//label content: QR code object
	protected $contentQrCode;

	public function __construct(LabelOptions $labelOptions)
	{
		$this->labelOptions = $labelOptions;
	}

	/**
	* Initialize the label with a CmdbObject to print
	* @param \yourCMDB\entities\CmdbObject $object	object to create label for
	*/
	public function init(\yourCMDB\entities\CmdbObject $object)
	{
		$this->cmdbObject = $object;

		//create configuration object
		$config = CmdbConfig::create();

		//get data from CmdbObject
		$this->contentAssetId = $this->cmdbObject->getId();
		$this->contentSummaryFields = Array();
		foreach(array_keys($config->getObjectTypeConfig()->getSummaryFields($object->getType())) as $summaryfield)
		{
			$fieldLabel = $config->getObjectTypeConfig()->getFieldLabel($object->getType(), $summaryfield);
			$fieldValue = $object->getFieldValue($summaryfield);
			$this->contentSummaryFields[$fieldLabel] = $fieldValue;
		}
		$urlQrCode = $config->getViewConfig()->getQrCodeUrlPrefix() .$object->getId();
		$this->contentQrCode = new QR($urlQrCode, $config->getViewConfig()->getQrCodeEccLevel());
	}

	public abstract function getContent();
}
?>
