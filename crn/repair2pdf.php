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

$qsvr = "select * from repairs where service_id = ".$svrid;
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
$pdf->Cell(185,7,$coyname.' - Repair for '.$rno.' ('.$svehicleno.')',0,0,"L",1);

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

$y = $y + 5;

$pdf->SetXY(15,29);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(216,208,208);
$pdf->SetFont('Arial','',6);
$pdf->Cell(185,2,' ',0,0,"C",1);


$x = 15;
$y = 33;

$pdf->SetXY($x,$y);
$pdf->SetTextColor(255,255,255);
$pdf->SetFillColor(0,0,0);
$pdf->SetFont('Arial','',10);
$pdf->Cell(91,4,'DEFECTS',0,0,"L",1);

$pdf->SetXY($x+94,$y);
$pdf->SetTextColor(255,255,255);
$pdf->SetFillColor(0,0,0);
$pdf->SetFont('Arial','',10);
$pdf->Cell(91,4,'REPAIRS',0,0,"L",1);

if ($defect1 <> '') {
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->MultiCell(91,4,$defect1,1,"L",0);
	
	$pdf->SetXY($x+94,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->MultiCell(91,4,$repair1,1,"L",0);
}

if ($defect2 <> '') {
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->MultiCell(91,4,$defect2,1,"L",0);
	
	$pdf->SetXY($x+94,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->MultiCell(91,4,$repair2,1,"L",0);
}

if ($defect3 <> '') {
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->MultiCell(91,4,$defect3,1,"L",0);
	
	$pdf->SetXY($x+94,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->MultiCell(91,4,$repair3,1,"L",0);
}

if ($defect4 <> '') {
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->MultiCell(91,4,$defect4,1,"L",0);
	
	$pdf->SetXY($x+94,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->MultiCell(91,4,$repair4,1,"L",0);
}

if ($defect5 <> '') {
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->MultiCell(91,4,$defect5,1,"L",0);
	
	$pdf->SetXY($x+94,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->MultiCell(91,4,$repair5,1,"L",0);
}

if ($defect6 <> '') {
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->MultiCell(91,4,$defect6,1,"L",0);
	
	$pdf->SetXY($x+94,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->MultiCell(91,4,$repair6,1,"L",0);
}

if ($defect7 <> '') {
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->MultiCell(91,4,$defect7,1,"L",0);
	
	$pdf->SetXY($x+94,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->MultiCell(91,4,$repair7,1,"L",0);
}

if ($defect8 <> '') {
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->MultiCell(91,4,$defect8,1,"L",0);
	
	$pdf->SetXY($x+94,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->MultiCell(91,4,$repair8,1,"L",0);
}

if ($defect9 <> '') {
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->MultiCell(91,4,$defect9,1,"L",0);
	
	$pdf->SetXY($x+94,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->MultiCell(91,4,$repair9,1,"L",0);
}

if ($defect10 <> '') {
	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->MultiCell(91,4,$defect10,1,"L",0);
	
	$pdf->SetXY($x+94,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->MultiCell(91,4,$repair10,1,"L",0);
}

$y = $y + 5;

$pdf->SetXY($x,$y);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Arial','',10);
$pdf->Cell(71,3,'Serviceman: '.$serviceman,0,0,"L",1);

$fname = 'Repair_'.$svrid.'.pdf';
$pdf->Output($fname,'I');


?>
