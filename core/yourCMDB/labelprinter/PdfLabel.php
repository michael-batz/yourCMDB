<?php
/********************************************************************
* This file is part of yourCMDB.
*
* Copyright 2013-2015 Michael Batz
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
		//ToDo: configuration options
		$configPageHeight = 29;
		$configPageWidth = 90;
		$configBorderTop = 2;
		$configBorderBottom = 2;
		$configBorderLeft = 0;

		//calculate coordinates
		$coordXColLeft = $configBorderLeft;
		$coordXColRight = (0.3 * ($configPageWidth - $configBorderLeft)) + $configBorderLeft;
		$widthColLeft = $coordXColRight - $coordXColLeft;
		$widthColRight = $configPageWidth - $coordXColRight;
		$coordYTop = $configBorderTop;
		$heightContent = $configPageHeight - $coordYTop - $configBorderBottom;

		//PDF: start
		$pdf = new FPDF("L", "mm", array($configPageWidth, $configPageHeight));
		$pdf->SetAutoPageBreak(false);
		$pdf->SetMargins(0, 0, 0);
		$pdf->addPage();
		$pdf->SetFont("Helvetica", "", 8);

		//PDF: left column (30% width)
		$pdf->SetXY($coordXColLeft, 0);

		//PDF: print QR code (full width of left column, max 80% of height)
		$imageLength = $widthColLeft;
		if($imageLength > 0.8 * $configPageHeight)
		{
			$imageLength = 0.8 * $configPageHeight;
		}
		$qrCodeBase64 = "data://image/gif;base64," . base64_encode($this->contentQrCode->image(4));
		$pdf->Image($qrCodeBase64, $coordXColLeft, 0, $imageLength, $imageLength, "GIF");

		//PDF: print assetId
		$pdf->SetXY($coordXColLeft, $imageLength);
		$pdf->Cell($imageLength, 1, "#".$this->contentAssetId, 0, 0, "C");


		//PDF: right column (70% width)
		$pdf->SetXY($coordXColRight, $coordYTop);

		//PDF: print summary fields
		$outputSummaryfields = "";
		$outputSummaryfieldsCounts = 0;
		$outputSummaryfieldsMinLineHeight = 3;
		$outputSummaryfieldsMaxLineHeight = 6;
		$outputSummaryfieldsMaxLines = floor($heightContent / $outputSummaryfieldsMinLineHeight);
		foreach(array_keys($this->contentSummaryFields) as $summaryFieldName)
		{
			$summaryFieldValue = $this->contentSummaryFields[$summaryFieldName];
			$outputSummaryfields .= "$summaryFieldValue\n";
			$outputSummaryfieldsCounts++;
			//stop printing summary fields if max number of lines is reached
			if($outputSummaryfieldsCounts >= $outputSummaryfieldsMaxLines)
			{
				break;
			}
		}
		//calulate optimal height of output of summary fiels
		$outputSummaryfieldsHeight = $outputSummaryfieldsMinLineHeight;
		if($outputSummaryfieldsCounts > 0)
		{
			$outputSummaryfieldsHeightOpt = floor($heightContent / $outputSummaryfieldsCounts);
			if($outputSummaryfieldsHeight < $outputSummaryfieldsHeightOpt)
			{
				$outputSummaryfieldsHeight = $outputSummaryfieldsHeightOpt;
			}
			if($outputSummaryfieldsHeight > $outputSummaryfieldsMaxLineHeight)
			{
				$outputSummaryfieldsHeight = $outputSummaryfieldsMaxLineHeight;
			}
		}
		$pdf->MultiCell($widthColRight, $outputSummaryfieldsHeight, $outputSummaryfields);


		//return PDF data
		return $pdf->Output("S");
	}
}
?>
