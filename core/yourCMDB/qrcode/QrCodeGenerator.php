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
namespace yourCMDB\qrcode;

use Endroid\QrCode\QrCode;

/**
* QrCode generator
* @author Michael Batz <michael@yourcmdb.org>
*/
class QrCodeGenerator
{
	//URL for QrCode
	private $url;

	//ECC level
	private $eccLevel;

	public function __construct($url, $eccLevel)
	{
		$this->url = $url;
		$this->eccLevel = $eccLevel;
	}

	/**
	* Returns the QrCode as png image
	* @return $string PNG image data
	*/
	public function getPngImage()
	{
		//create QrCode with QrCode library
		$qr = new QrCode();
		$qr->setText($this->url);
		$qr->setErrorCorrection($this->eccLevel);
		$qr->setImageType(QrCode::IMAGE_TYPE_PNG);

		//turn on output buffering to get exporters STDOUT
		ob_start();

		//output QR code
		$qr->render();

		//get output from buffer
		$output = ob_get_contents();

		//end and clear output buffer
		ob_end_clean();

		return $output;
	}
}
?>
