<?php
define('FPDF_FONTPATH','../includes/font/');
require('../includes/fpdf/fpdf.php');

$pdf=new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','',6);
$pdf->SetLeftMargin(0);

for ($y = 10; $y <= 270; $y=$y+10) {
	for ($x = 10; $x <= 190; $x=$x+10) {
		$pdf->SetXY($x,$y);
		$pdf->Cell(5,5,$x.' '.$y,'T,L');
	}
}

$pdf->Output('printgrid','I');
?>
