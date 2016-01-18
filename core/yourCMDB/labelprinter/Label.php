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

	//label content: specific fields of CmdbObject
	protected $contentFields;

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

		//get ID from CmdbObject
		$this->contentAssetId = $this->cmdbObject->getId();

		//get some fields from CmdbObject. Use label fields if defined and fallback to summary fields
		$this->contentFields = Array();
		$labelFields = $config->getObjectTypeConfig()->getLabelFields($object->getType());
		$summaryFields = $config->getObjectTypeConfig()->getSummaryFields($object->getType());
		$contentFieldSource = $labelFields;
		if(count($labelFields) <= 0)
		{
			$contentFieldSource = $summaryFields;
		}
		foreach(array_keys($contentFieldSource) as $fieldName)
		{
			$fieldLabel = $config->getObjectTypeConfig()->getFieldLabel($object->getType(), $fieldName);
			$fieldValue = $object->getFieldValue($fieldName);
			$this->contentFields[$fieldLabel] = $fieldValue;
		}

		//get QR code data
		$urlQrCode = $config->getViewConfig()->getQrCodeUrlPrefix() .$object->getId();
		$this->contentQrCode = new QR($urlQrCode, $config->getViewConfig()->getQrCodeEccLevel());
	}

	/**
	* Returns the Label content in its specific format
	* @return string	label content
	*/
	public abstract function getContent();

	/**
	* Returns the MIME type of the label content
	* @return string	MIME type, e.g. application/pdf
	*/
	public abstract function getContentType();
}
?>
