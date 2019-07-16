<?php


$regno = 'VERNON';
$min = 276702;
$max = 286702;
$lic = 397611249;
$vtype = '001';
$rucweight = '01';
$site = 'SITE WWW999999';
$dt = '261112';
$tm = '09:23:28';
$hub = '          ';
$uid = '232864';
$ref = '081994456';
$bartxt = $dt.$regno.$lic.$min.$max.$hub.$rucweight.$vtype.$uid;
$filename = 'ruc'.$lic.'.png';
$fname = 'ruc'.$lic;

require_once('../includes/tcpdf/config/lang/eng.php');
require_once('../includes/tcpdf/tcpdf.php');

$newpage = 'Y';
$pdf = new TCPDF('L', 'mm', array(100,50));
	
$pdf->AddPage();
$pdf->SetLeftMargin(0);
// remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->setAutoPageBreak(false,0);

$img = '../log/ruc/land_transport.png';
$pdf->Image($img,0,0,50,50,'png');

$pdf->SetXY(6,6);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Helvetica','',30);
$pdf->Cell(45,13,$regno);
$pdf->SetTextColor(0,0,0);

$pdf->SetXY(10,3);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Times','',9);
$pdf->Cell(50,4,"ROAD USER CHARGES");
$pdf->SetTextColor(0,0,0);

$pdf->write2DBarcode($bartxt,'PDF417',52,6.5,43,20);

$pdf->SetXY(58,3);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Times','',9);
$pdf->Cell(35,4,"DISTANCE LICENCE");
$pdf->SetTextColor(0,0,0);

$pdf->SetXY(6,22);
$pdf->SetTextColor(0,0,0);
$pdf->SetFont('Times','',9);
$pdf->Cell(20,3,"MIN DIST");
$pdf->SetTextColor(0,0,0);

$pdf->SetXY(6,25);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Times','',9);
$pdf->Cell(20,3,"RECORDER");
$pdf->SetTextColor(0,0,0);

$pdf->SetXY(28,22);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Helvetica','',18);
$pdf->Cell(24,6,$min);
$pdf->SetTextColor(0,0,0);

$pdf->SetXY(6,29);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Times','',9);
$pdf->Cell(20,3,"MAX DIST");
$pdf->SetTextColor(0,0,0);

$pdf->SetXY(6,32);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Times','',9);
$pdf->Cell(20,3,"RECORDER");
$pdf->SetTextColor(0,0,0);

$pdf->SetXY(28,29);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Helvetica','',18);
$pdf->Cell(24,6,$max);
$pdf->SetTextColor(0,0,0);

$pdf->SetXY(6,39);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Times','',9);
$pdf->Cell(24,3,"RUC VEH TYP");
$pdf->SetTextColor(0,0,0);

$pdf->SetXY(39,37);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Helvetica','',18);
$pdf->Cell(12,6,$vtype);
$pdf->SetTextColor(0,0,0);

$pdf->SetXY(6,43.5);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Times','',9);
$pdf->Cell(24,3,"LICENCE NO");
$pdf->SetTextColor(0,0,0);

$pdf->SetXY(28,42);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Helvetica','',12);
$pdf->Cell(12,6,$lic);
$pdf->SetTextColor(0,0,0);

$pdf->SetXY(53,39);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Times','',9);
$pdf->Cell(23,3,"RUC WEIGHT");
$pdf->SetTextColor(0,0,0);

$pdf->SetXY(84,34);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Helvetica','',26);
$pdf->Cell(14,12,$rucweight);
$pdf->SetTextColor(0,0,0);

$pdf->SetXY(53,44.5);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Times','',7);
$pdf->Cell(23,2,$site);
$pdf->SetTextColor(0,0,0);

$pdf->SetXY(75,44.5);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Times','',7);
$pdf->Cell(23,2,$tm.' '.$dt);
$pdf->SetTextColor(0,0,0);

$pdf->SetFillColor(18,16,16);
$pdf->Rect(95,35,5,8,'F');

$pdf->StartTransform();
$pdf->Rotate(90, 96, 45);
$pdf->SetTextColor(0,0,0);
$pdf->SetFont('Times','B',8);
$pdf->Cell(5,2,'HBA');
$pdf->SetTextColor(0,0,0);
$pdf->StopTransform();

$pdf->StartTransform();
$pdf->Rotate(90, 91, 39);
$pdf->SetTextColor(0,0,0);
$pdf->SetFont('Times','',7);
$pdf->Cell(30,2,$ref);
$pdf->SetTextColor(0,0,0);
$pdf->StopTransform();


$fn = 'ruc/'.$fname.'.pdf';
$pdf->Output($fn, 'FI');


// convert pdf to jpg
$pdfname = 'ruc/'.$fname.'.pdf';
$jpgname = 'ruc/'.$fname.'.jpg';

$im = new imagick();
$im->setResolution(300,300);
$im->readimage($pdfname.'[0]'); 
$im->setImageFormat('jpeg');    
$im->writeImage($jpgname); 
$im->clear(); 
$im->destroy();


?>
