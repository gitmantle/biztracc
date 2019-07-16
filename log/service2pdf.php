<?php
session_start();
$coyname = $_SESSION['s_coyname'];
$coyid = $_SESSION['s_coyid'];
$svrid = $_REQUEST['id'];

$usersession = $_SESSION['usersession'];

define('FPDF_FONTPATH','../includes/font/');
require('../includes/fpdf/fpdf.php');

$admindb = $_SESSION['s_admindb'];
require_once("../db.php");
mysql_select_db($admindb) or die(mysql_error());
$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);

$moduledb = $_SESSION['s_logdb'];
mysql_select_db($moduledb) or die(mysql_error());

date_default_timezone_set($_SESSION['s_timezone']);
$dt = date('d/m/Y');


class PDF extends FPDF
{
	// private variables
	var $colonnes;
	var $format;
	var $angle=0;

	//Page footer
	function Footer()
	{
		//Position at 1.5 cm from bottom
		$this->SetY(-15);
		//Arial italic 8
		$this->SetFont('Arial','I',8);
		//Page number
		$this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
	}

	function RoundedRect($x, $y, $w, $h, $r, $style = '')
	{
		$k = $this->k;
		$hp = $this->h;
		if($style=='F')
			$op='f';
		elseif($style=='FD' or $style=='DF')
			$op='B';
		else
			$op='S';
		$MyArc = 4/3 * (sqrt(2) - 1);
		$this->_out(sprintf('%.2f %.2f m',($x+$r)*$k,($hp-$y)*$k ));
		$xc = $x+$w-$r ;
		$yc = $y+$r;
		$this->_out(sprintf('%.2f %.2f l', $xc*$k,($hp-$y)*$k ));

		$this->_Arc($xc + $r*$MyArc, $yc - $r, $xc + $r, $yc - $r*$MyArc, $xc + $r, $yc);
		$xc = $x+$w-$r ;
		$yc = $y+$h-$r;
		$this->_out(sprintf('%.2f %.2f l',($x+$w)*$k,($hp-$yc)*$k));
		$this->_Arc($xc + $r, $yc + $r*$MyArc, $xc + $r*$MyArc, $yc + $r, $xc, $yc + $r);
		$xc = $x+$r ;
		$yc = $y+$h-$r;
		$this->_out(sprintf('%.2f %.2f l',$xc*$k,($hp-($y+$h))*$k));
		$this->_Arc($xc - $r*$MyArc, $yc + $r, $xc - $r, $yc + $r*$MyArc, $xc - $r, $yc);
		$xc = $x+$r ;
		$yc = $y+$r;
		$this->_out(sprintf('%.2f %.2f l',($x)*$k,($hp-$yc)*$k ));
		$this->_Arc($xc - $r, $yc - $r*$MyArc, $xc - $r*$MyArc, $yc - $r, $xc, $yc - $r);
		$this->_out($op);
	}

	function _Arc($x1, $y1, $x2, $y2, $x3, $y3)
	{
		$h = $this->h;
		$this->_out(sprintf('%.2f %.2f %.2f %.2f %.2f %.2f c ', $x1*$this->k, ($h-$y1)*$this->k,
							$x2*$this->k, ($h-$y2)*$this->k, $x3*$this->k, ($h-$y3)*$this->k));
	}

	function Rotate($angle,$x=-1,$y=-1)
	{
		if($x==-1)
			$x=$this->x;
		if($y==-1)
			$y=$this->y;
		if($this->angle!=0)
			$this->_out('Q');
		$this->angle=$angle;
		if($angle!=0)
		{
			$angle*=M_PI/180;
			$c=cos($angle);
			$s=sin($angle);
			$cx=$x*$this->k;
			$cy=($this->h-$y)*$this->k;
			$this->_out(sprintf('q %.5f %.5f %.5f %.5f %.2f %.2f cm 1 0 0 1 %.2f %.2f cm',$c,$s,-$s,$c,$cx,$cy,-$cx,-$cy));
		}
	}

	function sizeOfText( $texte, $largeur )
	{
		$index    = 0;
		$nb_lines = 0;
		$loop     = TRUE;
		while ( $loop )
		{
			$pos = strpos($texte, "\n");
			if (!$pos)
			{
				$loop  = FALSE;
				$ligne = $texte;
			}
			else
			{
				$ligne  = substr( $texte, $index, $pos);
				$texte = substr( $texte, $pos+1 );
			}
			$length = floor( $this->GetStringWidth( $ligne ) );
			$res = 1 + floor( $length / $largeur) ;
			$nb_lines += $res;
		}
		return $nb_lines;
	}

	//********************************************************************
	function MaxLines($ldetails, $gwidths, $font, $atrib, $fontsize)
	//********************************************************************
	{

	$nlines = 0;
	$gtw = split(',',$gwidths);
	$gcount = count($gtw);
	$this->SetFont($font,$attrib,$fontsize);

	for ($n = 0; $n < $gcount; $n++) {
		$text = $ldetails[$n];
		$textlength = $this->GetStringWidth($text);
		$numlines = ceil($textlength/$gtw[$n]);
		if ($numlines > $nlines) {
			$nlines = $numlines;
		} // if
	} // for
	return $nlines;	

	} // function MaxLines
}

$hfontname = "Arial";
$hattrib = "";
$hfontsize = 10;

$offset = 0;

$pdf = new PDF("P","mm","A4");
$pdf->AliasNbPages();

$q = "select * from service where uid = ".$svrid;
$r = mysql_query($q) or die(mysql_error().' '.$q);
$row = mysql_fetch_array($r);
extract($row);
switch ($service_type) {
	case 'A':
		$db = 'servicea';
		break;
	case 'B':
		$db = 'serviceb';
		break;
	case 'C':
		$db = 'servicec';
		break;
}
$svehicleno = $vehicleno;
$shubo = $hubodometer;
$sspeedo = $speedo;
$sjobno = $jobno;
$sworkshop = $workshop;
$svrdue = $servicedue;
$stype = $service_type;

$q = "select * from vehicles where vehicleno = '".$svehicleno."'";
$r = mysql_query($q) or die (mysql_error());
$row = mysql_fetch_array($r);
extract($row);
$cd = explode('-',$cofdate);
$cof = $cd[2].'/'.$cd[1].'/'.$cd[0];
$mk = $make;
$rno = $regno;

$qsvr = "select * from ".$db." where service_id = ".$svrid;
$rsvr = mysql_query($qsvr) or die(mysql_error().' '.$qsvr);
$row = mysql_fetch_array($rsvr);
extract($row);
$dt = explode('-',$ddate);
$dtsvr = $dt[2].'/'.$dt[1].'/'.$dt[0];
		
$pdf->AddPage();
$pdf->SetFont('Arial','',10);
$pdf->SetLeftMargin(0);
			
$pdf->SetXY(15,9);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(216,208,208);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(185,7,$coyname.' - '.$stype.' Service for '.$rno.' ('.$svehicleno.')',0,0,"L",1);

$pdf->SetXY(15,19);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Arial','',10);
$pdf->Cell(60,3,'Workshop: '.$sworkshop,0,0,"L",1);

$pdf->SetXY(75,19);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Arial','',10);
$pdf->Cell(40,3,'Reg No.: '.$rno,0,0,"L",1);

$pdf->SetXY(115,19);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Arial','',10);
$pdf->Cell(40,3,'Job No.: '.$sjobno,0,0,"L",1);

$pdf->SetXY(155,19);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Arial','',10);
$pdf->Cell(40,3,'Job Date: '.$dtsvr,0,0,"L",1);

$pdf->SetXY(15,24);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Arial','',10);
$pdf->Cell(60,3,'Make: '.$mk,0,0,"L",1);

$pdf->SetXY(75,24);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Arial','',10);
$pdf->Cell(40,3,'COF: '.$cof,0,0,"L",1);

$pdf->SetXY(115,24);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Arial','',10);
$pdf->Cell(40,3,'Hub Km: '.$shubo,0,0,"L",1);

$pdf->SetXY(155,24);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Arial','',10);
$pdf->Cell(40,3,'Speedo Km: '.$sspeedo,0,0,"L",1);

$pdf->SetXY(15,29);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(216,208,208);
$pdf->SetFont('Arial','',6);
$pdf->Cell(185,2,'This check sheet is to be used in conjuction with the manufacturers specifications and service requirements',0,0,"C",1);

$x = 15;
$y = 33;

switch ($service_type) {
	case 'A':

	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(255,255,255);
	$pdf->SetFillColor(0,0,0);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,4,'CHECK',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(255,255,255);
	$pdf->SetFillColor(0,0,0);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,4,'OK/Done',0,0,"L",1);
	
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Cooling system and test conditioner',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$check1,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Air filter',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$check2,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Power steering oil level',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$check3,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Transmission oil level',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$check4,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Diff & final drives oil level',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$check5,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Clutch & brake fluid level',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$check6,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Battery condition & level',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$check7,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Engine oil level',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$check8,0,0,"L",1);
	
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(255,255,255);
	$pdf->SetFillColor(0,0,0);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,4,'GREASE',0,0,"L",1);
	
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Turntable plate, pivots and jaws',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$grease1,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Driveline',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$grease2,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Kingpins',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$grease3,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Steering joints',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$grease4,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Suspension pivots',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$grease5,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Clutch & throttle linkage',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$grease6,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Lubricate doors & hinges',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$grease7,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Any other pivots & linkages',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$grease8,0,0,"L",1);
	
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(255,255,255);
	$pdf->SetFillColor(0,0,0);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,4,'CHASSIS',0,0,"L",1);
	
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Check & adjust front wheel bearings',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$chassis1,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Check king pins',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$chassis2,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Check & adjust brakes',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$chassis3,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Check & adjust clutch',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$chassis4,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Cross members - cracks / loose',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$chassis5,0,0,"L",1);
	
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(255,255,255);
	$pdf->SetFillColor(0,0,0);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,4,'VISUAL INSPECTION OF',0,0,"L",1);
	
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Engine oil leaks',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$visual1,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Transmission oil leaks',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$visual2,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Diff & axle oil leaks',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$visual3,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Power steering system & steering box',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$visual4,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'All drive belts',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$visual5,0,0,"L",1);
	
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(255,255,255);
	$pdf->SetFillColor(0,0,0);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,4,'EXHUAST',0,0,"L",1);
	
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Leaks',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$exhaust1,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Pipework & clamps',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$exhaust2,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Muffler & mountings',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$exhaust3,0,0,"L",1);
	
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(255,255,255);
	$pdf->SetFillColor(0,0,0);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,4,'DRIVE LINE',0,0,"L",1);
	
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Universal joints',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$drive1,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Hangar bearing',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$drive2,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Diff flange',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$drive3,0,0,"L",1);
	
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(255,255,255);
	$pdf->SetFillColor(0,0,0);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,4,'STEERING BOX',0,0,"L",1);
	
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Steering box adjustment',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$steering1,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Steering box mounting',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$steering2,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Tie rod & drag link ends',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$steering3,0,0,"L",1);
	
	$x = $x + 94;
	$y = 33;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(255,255,255);
	$pdf->SetFillColor(0,0,0);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,4,'FRONT & REAR SUSPENSION',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(255,255,255);
	$pdf->SetFillColor(0,0,0);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,4,'OK/Done',0,0,"L",1);
	
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Spring leaves & clamps',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$suspension1,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Air bags - chaffing / mounting',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$suspension2,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Air bags - leveling valve',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$suspension3,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Brackets, shackles, pins & bushes',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$suspension4,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'U bolts & centre bolts',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$suspension5,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Torque rods & panhard rods',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$suspension6,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Spring mounts & hangars',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$suspension7,0,0,"L",1);
	
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(255,255,255);
	$pdf->SetFillColor(0,0,0);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,4,'AIR SYSTEM',0,0,"L",1);
	
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Air leaks - applied & released',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$air1,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Tank & mounting',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$air2,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Hose & pipework chafing',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$air3,0,0,"L",1);
	
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(255,255,255);
	$pdf->SetFillColor(0,0,0);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,4,'FUEL SYSTEM',0,0,"L",1);
	
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Leaks',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$fuel1,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Pipework, tank & mounting',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$fuel2,0,0,"L",1);
	
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(255,255,255);
	$pdf->SetFillColor(0,0,0);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,4,'MUDGUARDS & BODY',0,0,"L",1);
	
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Mountings & brackets',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$body1,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Mudflaps',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$body2,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Body cracks',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$body3,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Mezz floor security & condition',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$body4,0,0,"L",1);
	
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(255,255,255);
	$pdf->SetFillColor(0,0,0);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,4,'TURNTABLE / RINGFEEDER',0,0,"L",1);
	
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Mounting bolts',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$turn1,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Cracks',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$turn2,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Fifth wheel service per schedule',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$turn3,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Ringfeeder operation',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$turn4,0,0,"L",1);
	
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(255,255,255);
	$pdf->SetFillColor(0,0,0);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,4,'ELECTRICAL',0,0,"L",1);
	
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Headlamps - both beams',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$electric1,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Park lights',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$electric2,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Turn indicators - front, side & rear',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$electric3,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Roof lights',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$electric4,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Tail lights',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$electric5,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Brake lights',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$electric6,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Number plate light',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$electric7,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Reflectors',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$electric8,0,0,"L",1);
	
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(255,255,255);
	$pdf->SetFillColor(0,0,0);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,4,'GENERAL',0,0,"L",1);
	
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Wheels, nuts & studs',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$gen1,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Tyres - damage, wear, match',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$gen2,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Engine mounts',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$gen3,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Door hinges, catches, locks',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$gen4,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Wiper blades & arms',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$gen5,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Driver controls & instruments',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$gen6,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Tail lift & hoist hydraulic oil level',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$gen7,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Tail lift & hoist operation',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$gen8,0,0,"L",1);
	
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(255,255,255);
	$pdf->SetFillColor(0,0,0);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,4,'ROAD TEST',0,0,"L",1);
	
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Fill out lube sticker - use SPEEDO Km',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$road1,0,0,"L",1);

	break;

//*************************************************************************************
// B Service
//*************************************************************************************

	case 'B';
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(255,255,255);
	$pdf->SetFillColor(0,0,0);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,4,'CHECK',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(255,255,255);
	$pdf->SetFillColor(0,0,0);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,4,'OK/Done',0,0,"L",1);
	
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Cooling system and test conditioner',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$check1,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Air filter',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$check2,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Power steering oil level',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$check3,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Transmission oil level',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$check4,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Diff & final drives oil level',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$check5,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Clutch & brake fluid level',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$check6,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Battery condition & level',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$check7,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,'',0,0,"L",1);
	
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(255,255,255);
	$pdf->SetFillColor(0,0,0);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,4,'GREASE',0,0,"L",1);
	
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Turntable plate, pivots and jaws',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$grease1,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Driveline',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$grease2,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Kingpins',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$grease3,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Steering joints',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$grease4,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Suspension pivots',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$grease5,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Clutch & throttle linkage',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$grease6,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Lubricate doors & hinges',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$grease7,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Any other pivots & linkages',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$grease8,0,0,"L",1);
	
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(255,255,255);
	$pdf->SetFillColor(0,0,0);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,4,'CHASSIS',0,0,"L",1);
	
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Check & adjust front wheel bearings',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$chassis1,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Check king pins',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$chassis2,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Check & adjust brakes',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$chassis3,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Check & adjust clutch',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$chassis4,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Cross members - cracks / loose',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$chassis5,0,0,"L",1);
	
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(255,255,255);
	$pdf->SetFillColor(0,0,0);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,4,'VISUAL INSPECTION OF',0,0,"L",1);
	
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Engine oil leaks',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$visual1,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Transmission oil leaks',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$visual2,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Diff & axle oil leaks',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$visual3,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Power steering system & steering box',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$visual4,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'All drive belts',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$visual5,0,0,"L",1);
	
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(255,255,255);
	$pdf->SetFillColor(0,0,0);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,4,'EXHUAST',0,0,"L",1);
	
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Leaks',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$exhaust1,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Pipework & clamps',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$exhaust2,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Muffler & mountings',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$exhaust3,0,0,"L",1);
	
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(255,255,255);
	$pdf->SetFillColor(0,0,0);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,4,'DRIVE LINE',0,0,"L",1);
	
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Universal joints',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$drive1,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Hangar bearing',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$drive2,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Diff flange',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$drive3,0,0,"L",1);
	
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(255,255,255);
	$pdf->SetFillColor(0,0,0);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,4,'STEERING BOX',0,0,"L",1);
	
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Steering box adjustment',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$steering1,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Steering box mounting',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$steering2,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Tie rod & drag link ends',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$steering3,0,0,"L",1);
	
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(255,255,255);
	$pdf->SetFillColor(0,0,0);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,4,'REPLACE',0,0,"L",1);
	
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Engine oil & filter',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$replace1,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Fuel filter',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$replace2,0,0,"L",1);
	
	
	$x = $x + 94;
	$y = 33;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(255,255,255);
	$pdf->SetFillColor(0,0,0);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,4,'FRONT & REAR SUSPENSION',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(255,255,255);
	$pdf->SetFillColor(0,0,0);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,4,'OK/Done',0,0,"L",1);
	
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Spring leaves & clamps',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$suspension1,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Air bags - chaffing / mounting',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$suspension2,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Air bags - leveling valve',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$suspension3,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Brackets, shackles, pins & bushes',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$suspension4,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'U bolts & centre bolts',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$suspension5,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Torque rods & panhard rods',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$suspension6,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Spring mounts & hangars',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$suspension7,0,0,"L",1);
	
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(255,255,255);
	$pdf->SetFillColor(0,0,0);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,4,'AIR SYSTEM',0,0,"L",1);
	
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Air leaks - applied & released',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$air1,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Tank & mounting',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$air2,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Hose & pipework chafing',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$air3,0,0,"L",1);
	
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(255,255,255);
	$pdf->SetFillColor(0,0,0);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,4,'FUEL SYSTEM',0,0,"L",1);
	
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Leaks',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$fuel1,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Pipework, tank & mounting',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$fuel2,0,0,"L",1);
	
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(255,255,255);
	$pdf->SetFillColor(0,0,0);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,4,'MUDGUARDS & BODY',0,0,"L",1);
	
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Mountings & brackets',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$body1,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Mudflaps',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$body2,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Body cracks',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$body3,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Mezz floor security & condition',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$body4,0,0,"L",1);
	
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(255,255,255);
	$pdf->SetFillColor(0,0,0);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,4,'TURNTABLE / RINGFEEDER',0,0,"L",1);
	
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Mounting bolts',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$turn1,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Cracks',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$turn2,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Fifth wheel service per schedule',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$turn3,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Ringfeeder operation',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$turn4,0,0,"L",1);
	
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(255,255,255);
	$pdf->SetFillColor(0,0,0);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,4,'ELECTRICAL',0,0,"L",1);
	
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Headlamps - both beams',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$electric1,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Park lights',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$electric2,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Turn indicators - front, side & rear',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$electric3,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Roof lights',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$electric4,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Tail lights',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$electric5,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Brake lights',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$electric6,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Number plate light',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$electric7,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Reflectors',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$electric8,0,0,"L",1);
	
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(255,255,255);
	$pdf->SetFillColor(0,0,0);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,4,'GENERAL',0,0,"L",1);
	
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Wheels, nuts & studs',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$gen1,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Tyres - damage, wear, match',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$gen2,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Engine mounts',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$gen3,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Door hinges, catches, locks',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$gen4,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Wiper blades & arms',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$gen5,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Driver controls & instruments',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$gen6,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Tail lift & hoist hydraulic oil level',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$gen7,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Tail lift & hoist operation',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$gen8,0,0,"L",1);
	
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(255,255,255);
	$pdf->SetFillColor(0,0,0);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,4,'ROAD TEST',0,0,"L",1);
	
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Fill out lube sticker - use SPEEDO Km',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$road1,0,0,"L",1);

	$y = $y + 5;

	break;
	
	
//*************************************************************************************
// C Service
//*************************************************************************************

	case 'C';
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(255,255,255);
	$pdf->SetFillColor(0,0,0);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,4,'CHECK',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(255,255,255);
	$pdf->SetFillColor(0,0,0);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,4,'OK/Done',0,0,"L",1);
	
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Valve clearance & adjust',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$check1,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Air filter',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$check2,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Check 7 re-pack front wheel bearings',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$check3,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Remove belts & check fan hub & bearings',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$check4,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Clutch & adjust if required',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$check5,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Clutch & brake fluid levels',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$check6,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Top up auto lube if required',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$check7,0,0,"L",1);
	
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(255,255,255);
	$pdf->SetFillColor(0,0,0);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,4,'GREASE',0,0,"L",1);
	
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Turntable plate, pivots and jaws',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$grease1,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Driveline',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$grease2,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Kingpins',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$grease3,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Steering joints',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$grease4,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Suspension pivots',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$grease5,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Clutch & throttle linkage',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$grease6,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Lubricate doors & hinges',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$grease7,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Any other pivots & linkages',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$grease8,0,0,"L",1);
	
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(255,255,255);
	$pdf->SetFillColor(0,0,0);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,4,'CHASSIS',0,0,"L",1);
	
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Check & adjust front wheel bearings',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$chassis1,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Check king pins',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$chassis2,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Check & adjust brakes',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$chassis3,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Check & adjust clutch',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$chassis4,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Cross members - cracks / loose',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$chassis5,0,0,"L",1);
	
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(255,255,255);
	$pdf->SetFillColor(0,0,0);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,4,'VISUAL INSPECTION OF',0,0,"L",1);
	
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Engine oil leaks',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$visual1,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Transmission oil leaks',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$visual2,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Diff & axle oil leaks',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$visual3,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Power steering system & steering box',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$visual4,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'All drive belts',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$visual5,0,0,"L",1);
	
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(255,255,255);
	$pdf->SetFillColor(0,0,0);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,4,'EXHUAST',0,0,"L",1);
	
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Leaks',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$exhaust1,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Pipework & clamps',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$exhaust2,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Muffler & mountings',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$exhaust3,0,0,"L",1);
	
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(255,255,255);
	$pdf->SetFillColor(0,0,0);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,4,'DRIVE LINE',0,0,"L",1);
	
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Universal joints',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$drive1,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Hangar bearing',0,0,"L",1);

	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$drive2,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Diff flange',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$drive3,0,0,"L",1);
	
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(255,255,255);
	$pdf->SetFillColor(0,0,0);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,4,'STEERING BOX',0,0,"L",1);
	
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Steering box adjustment',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$steering1,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Steering box mounting',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$steering2,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Tie rod & drag link ends',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$steering3,0,0,"L",1);
	
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(255,255,255);
	$pdf->SetFillColor(0,0,0);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,4,'REPLACE',0,0,"L",1);
	
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Engine oil & filter',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$replace1,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Fuel filter',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$replace2,0,0,"L",1);

	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Transmission oil & filter',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$replace3,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Power steering oil & filter',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$replace4,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Differential oil',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$replace5,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Coolant at 24 months & filter',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$replace6,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Clutch & brake fluid at 24 months',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$replace7,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Air dryer desiccant at 24 months',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(21,3,$replace8,0,0,"L",1);
	


	$x = $x + 94;
	$y = 33;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(255,255,255);
	$pdf->SetFillColor(0,0,0);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,4,'FRONT & REAR SUSPENSION',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(255,255,255);
	$pdf->SetFillColor(0,0,0);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,4,'OK/Done',0,0,"L",1);
	
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Spring leaves & clamps',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$suspension1,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Air bags - chaffing / mounting',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$suspension2,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Air bags - leveling valve',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$suspension3,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Brackets, shackles, pins & bushes',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$suspension4,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'U bolts & centre bolts',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$suspension5,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Torque rods & panhard rods',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$suspension6,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Spring mounts & hangars',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$suspension7,0,0,"L",1);
	
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(255,255,255);
	$pdf->SetFillColor(0,0,0);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,4,'AIR SYSTEM',0,0,"L",1);
	
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Air leaks - applied & released',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$air1,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Tank & mounting',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$air2,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Hose & pipework chafing',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$air3,0,0,"L",1);
	
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(255,255,255);
	$pdf->SetFillColor(0,0,0);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,4,'FUEL SYSTEM',0,0,"L",1);
	
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Leaks',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$fuel1,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Pipework, tank & mounting',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$fuel2,0,0,"L",1);
	
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(255,255,255);
	$pdf->SetFillColor(0,0,0);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,4,'MUDGUARDS & BODY',0,0,"L",1);
	
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Mountings & brackets',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$body1,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Mudflaps',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$body2,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Body cracks',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$body3,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Mezz floor security & condition',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$body4,0,0,"L",1);
	
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(255,255,255);
	$pdf->SetFillColor(0,0,0);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,4,'TURNTABLE / RINGFEEDER',0,0,"L",1);
	
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Mounting bolts',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$turn1,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Cracks',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$turn2,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Fifth wheel service per schedule',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$turn3,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Ringfeeder operation',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$turn4,0,0,"L",1);
	
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(255,255,255);
	$pdf->SetFillColor(0,0,0);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,4,'ELECTRICAL',0,0,"L",1);
	
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Headlamps - both beams',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$electric1,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Park lights',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$electric2,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Turn indicators - front, side & rear',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$electric3,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Roof lights',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$electric4,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Tail lights',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$electric5,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Brake lights',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$electric6,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Number plate light',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$electric7,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Reflectors',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$electric8,0,0,"L",1);
	
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(255,255,255);
	$pdf->SetFillColor(0,0,0);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,4,'GENERAL',0,0,"L",1);
	
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Wheels, nuts & studs',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$gen1,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Tyres - damage, wear, match',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$gen2,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Engine mounts',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$gen3,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Door hinges, catches, locks',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$gen4,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Wiper blades & arms',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$gen5,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Driver controls & instruments',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$gen6,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Tail lift & hoist hydraulic oil level',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$gen7,0,0,"L",1);
	
	$y = $y + 4;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Tail lift & hoist operation',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$gen8,0,0,"L",1);
	
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(255,255,255);
	$pdf->SetFillColor(0,0,0);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,4,'ROAD TEST',0,0,"L",1);
	
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(71,3,'Fill out lube sticker - use SPEEDO Km',0,0,"L",1);
	
	$pdf->SetXY($x+71,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,3,$road1,0,0,"L",1);

	$y = $y + 25;

	break;
	
}

$x = 15;

$pdf->SetXY($x,$y);
$pdf->SetTextColor(255,255,255);
$pdf->SetFillColor(0,0,0);
$pdf->SetFont('Arial','',10);
$pdf->Cell(71,4,'COMMENTS',0,0,"L",1);

$y = $y + 5;

$pdf->SetXY($x,$y);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Arial','',10);
$pdf->MultiCell(180,4,$comments,1,"L",0);

$y = $y + 5;

$pdf->SetXY($x,$y);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Arial','',10);
$pdf->Cell(71,3,'Serviceman: '.$serviceman,0,0,"L",1);

$pdf->SetXY($x+94,$y);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Arial','',10);
$pdf->Cell(20,3,'Next '.$stype.' Service due at Kms '.$servicedue,0,0,"L",1);


$fname = 'Service_'.$svrid.'.pdf';
$pdf->Output($fname,'I');


?>
