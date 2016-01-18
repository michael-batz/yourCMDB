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
use fpdf\FPDF;

/**
* Label in PDF format for a CMDB object
* @author Michael Batz <michael@yourcmdb.org>
*/
class PdfLabel extends Label
{
	public function getContent()
	{
		//get configuration options
		$configPageHeight = $this->labelOptions->getOption("PageHeight", "29");
		$configPageWidth = $this->labelOptions->getOption("PageWidth", "90");
		$configPageRotation = $this->labelOptions->getOption("PageRotation", "0");
		$configBorderTop = $this->labelOptions->getOption("BorderTop", "2");
		$configBorderBottom = $this->labelOptions->getOption("BorderBottom", "2");
		$configBorderLeft = $this->labelOptions->getOption("BorderLeft", "2");
		$configTitlePrefix = $this->labelOptions->getOption("TitlePrefix", "yourCMDB");

		//calculate coordinates
		$coordXColLeft = $configBorderLeft;
		$coordXColRight = (0.3 * ($configPageWidth - $configBorderLeft)) + $configBorderLeft;
		$widthColLeft = $coordXColRight - $coordXColLeft;
		$widthColRight = $configPageWidth - $coordXColRight;
		$coordYTop = $configBorderTop;
		$heightTitle = ($configPageHeight - $coordYTop - $configBorderBottom) * 0.2;
		$heightContent = $configPageHeight - $coordYTop - $configBorderBottom - $heightTitle;

		//PDF: start
		$pdf = new FPDF();
		$pdf->SetAutoPageBreak(false);
		$pdf->SetMargins(0, 0, 0);
		$pdf->addPage("L", array($configPageWidth, $configPageHeight), $configPageRotation);
		$pdf->SetFont("Helvetica", "", 8);

		//PDF: left column (30% width)
		$pdf->SetXY($coordXColLeft, 0);

		//PDF: print QR code (full width of left column, max 95% of height)
		$imageLength = $widthColLeft;
		if($imageLength > 0.95 * $configPageHeight)
		{
			$imageLength = 0.95 * $configPageHeight;
		}
		$qrCodeBase64 = "data://image/gif;base64," . base64_encode($this->contentQrCode->image(4));
		$pdf->Image($qrCodeBase64, $coordXColLeft, 0, $imageLength, $imageLength, "GIF");


		//PDF: right column (70% width)
		$pdf->SetXY($coordXColRight, $coordYTop);

		//PDF: print title (20% height)
		$pdf->Cell($widthColRight, $heightTitle, "$configTitlePrefix #".$this->contentAssetId, 'B', 1, "L");
		$pdf->SetX($coordXColRight);

		//PDF: print content fields
		$outputContentfields = "";
		$outputContentfieldsCounts = 0;
		$outputContentfieldsMinLineHeight = 3;
		$outputContentfieldsMaxLineHeight = 6;
		$outputContentfieldsMaxLines = floor($heightContent / $outputContentfieldsMinLineHeight);
		foreach(array_keys($this->contentFields) as $contentFieldName)
		{
			//only ISO8859-1 is supported with PDFs default fonts
			$contentFieldValue = utf8_decode($this->contentFields[$contentFieldName]);
			$outputContentfields .= "$contentFieldValue\n";
			$outputContentfieldsCounts++;
			//stop printing fields if max number of lines is reached
			if($outputContentfieldsCounts >= $outputContentfieldsMaxLines)
			{
				break;
			}
		}
		//calulate optimal height of output of content fiels
		$outputContentfieldsHeight = $outputContentfieldsMinLineHeight;
		if($outputContentfieldsCounts > 0)
		{
			$outputContentfieldsHeightOpt = floor($heightContent / $outputContentfieldsCounts);
			if($outputContentfieldsHeight < $outputContentfieldsHeightOpt)
			{
				$outputContentfieldsHeight = $outputContentfieldsHeightOpt;
			}
			if($outputContentfieldsHeight > $outputContentfieldsMaxLineHeight)
			{
				$outputContentfieldsHeight = $outputContentfieldsMaxLineHeight;
			}
		}
		$pdf->MultiCell($widthColRight, $outputContentfieldsHeight, $outputContentfields, 0, 'L');


		//return PDF data
		return $pdf->Output("S");
	}

	public function getContentType()
	{
		return "application/pdf";
	}
}
?>
