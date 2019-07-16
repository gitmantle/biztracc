<?php
session_start();

$usersession = $_SESSION['usersession'];

$_SESSION['s_showreconcilled'] = 'N';

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];

$bankrectable = 'ztmp'.$user_id.'_bankrec';

$findb = $_SESSION['s_findb'];

$coyid = $_SESSION['s_coyid'];
$coyname = $_SESSION['s_coyname'];
$bankbal = $_REQUEST['bankbal'];
$unrec = $_REQUEST['unrec'];
$coybal = $_REQUEST['coybal'];
$dt = date('Y-m-d');
$recdate = $_REQUEST['rdate'];
$rdt = explode('-',$recdate);
$y = $rdt[0];
$m = $rdt[1];
$d = $rdt[2];
$rdate = $d.'/'.$m.'/'.$y;
$heading = 'Bank Reconciliation for '.$coyname.' as at '.$rdate;

require_once('../includes/tcpdf/config/lang/eng.php');
require_once('../includes/tcpdf/tcpdf.php');

$newpage = 'Y';
$pdf = new TCPDF('P', 'mm', 'A4');
	
$pdf->AddPage();
$pdf->SetLeftMargin(0);
// remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->setAutoPageBreak(false,0);

$pdf->SetXY(10,20);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Helvetica','',12);
$pdf->Cell(100,5,$heading);
$pdf->SetTextColor(0,0,0);

$pdf->SetXY(10,30);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Helvetica','',12);
$pdf->Cell(30,5,'Bank Statement Balance');
$pdf->SetTextColor(0,0,0);

$pdf->SetXY(70,30);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Helvetica','',12);
$pdf->Cell(20,5,number_format(doubleval($bankbal),2),0,0,'R');
$pdf->SetTextColor(0,0,0);

$x = 10;
$y = 40;

$pdf->SetXY($x,$y);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Helvetica','',12);
$pdf->Cell(30,5,'Unreconciled entries');
$pdf->SetTextColor(0,0,0);

$y = $y + 5;

$pdf->SetXY($x,$y);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Helvetica','',12);
$pdf->Cell(30,5,'Date');
$pdf->SetTextColor(0,0,0);

$x = $x + 30;

$pdf->SetXY($x,$y);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Helvetica','',12);
$pdf->Cell(20,5,'Deposits');
$pdf->SetTextColor(0,0,0);

$x = $x + 30;

$pdf->SetXY($x,$y);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Helvetica','',12);
$pdf->Cell(30,5,'Payments');
$pdf->SetTextColor(0,0,0);

$x = $x + 30;

$pdf->SetXY($x,$y);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Helvetica','',12);
$pdf->Cell(30,5,'Reference');
$pdf->SetTextColor(0,0,0);

$x = $x + 30;

$pdf->SetXY($x,$y);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Helvetica','',12);
$pdf->Cell(30,5,'Description');
$pdf->SetTextColor(0,0,0);


$db->query("select DATE_FORMAT(ddate,GET_FORMAT(DATE,'EUR')) as ddate,debit,credit,reference,description from ".$findb.".".$bankrectable." where reconciled = 'N'");
$rows = $db->resultset();
foreach ($rows as $row) {
	extract($row);

	$y = $y + 5;
	$x = 10;
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Helvetica','',12);
	$pdf->Cell(30,5,$ddate);
	$pdf->SetTextColor(0,0,0);
	
	$x = $x + 30;
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Helvetica','',12);
	$pdf->Cell(20,5,number_format(doubleval($debit),2),0,0,'R');
	$pdf->SetTextColor(0,0,0);
	
	$x = $x + 30;
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Helvetica','',12);
	$pdf->Cell(20,5,number_format(doubleval($credit),2),0,0,'R');
	$pdf->SetTextColor(0,0,0);

	$x = $x + 30;
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Helvetica','',12);
	$pdf->Cell(30,5,$reference);
	$pdf->SetTextColor(0,0,0);

	$x = $x + 30;
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Helvetica','',12);
	$pdf->Cell(30,5,$description);
	$pdf->SetTextColor(0,0,0);
}

$x = 10;
$y = $y + 10;

$pdf->SetXY($x,$y);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Helvetica','',12);
$pdf->Cell(30,5,'Unreconciled Balance');
$pdf->SetTextColor(0,0,0);

$pdf->SetXY(70,$y);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Helvetica','',12);
$pdf->Cell(20,5,number_format(doubleval($unrec),2),0,0,'R');
$pdf->SetTextColor(0,0,0);

$y = $y + 10;

$pdf->SetXY($x,$y);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Helvetica','',12);
$pdf->Cell(30,5,'Bank + Unreconciled');
$pdf->SetTextColor(0,0,0);

$bplus = $bankbal + $unrec;

$pdf->SetXY(70,$y);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Helvetica','',12);
$pdf->Cell(20,5,number_format(doubleval($bplus),2),0,0,'R');
$pdf->SetTextColor(0,0,0);

$y = $y + 10;

$pdf->SetXY($x,$y);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Helvetica','',12);
$pdf->Cell(30,5,'Company Bank Balance');
$pdf->SetTextColor(0,0,0);

$pdf->SetXY(70,$y);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Helvetica','',12);
$pdf->Cell(20,5,number_format(doubleval($coybal),2),0,0,'R');
$pdf->SetTextColor(0,0,0);

$fname = $coyid.'_bankrec_'.$dt;

$pdf->Output($fname, 'I');

$db->closeDB();
?>