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
		//ToDo: create label
		$pdf = new FPDF();
		$pdf->addPage();
		$pdf->SetFont("Helvetica", "", 8);

		//print QR code
		$qrCodeBase64 = "data://image/gif;base64," . base64_encode($this->contentQrCode->image(4));
		$pdf->Image($qrCodeBase64, 0, 0, 0, 0, "GIF");

		//print assetId
		$pdf->SetXY(40, 5);
		$pdf->Cell(60, 5, "#".$this->contentAssetId, "B");
		$pdf->Ln();
		$pdf->SetX(40);

		//print summary fields
		foreach(array_keys($this->contentSummaryFields) as $summaryFieldName)
		{
			$summaryFieldValue = $this->contentSummaryFields[$summaryFieldName];
			$pdf->Cell(60, 5, "$summaryFieldName: $summaryFieldValue");
			$pdf->Ln();
			$pdf->SetX(40);
		}


		//return PDF data
		return $pdf->Output("S");
	}
}
?>
