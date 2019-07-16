<?php
session_start();
$coyname = $_SESSION['s_coyname'];
$coyid = $_SESSION['s_coyid'];
$rtid = $_REQUEST['uid'];

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
$subscriber = $subid;

$q = "select coyemail,coyphone from companies where coyid = ".$coyid;
$r = mysql_query($q) or die(mysql_error());
$row = mysql_fetch_array($r);
extract($row);
$cphone = $coyphone;
$cemail = $coyemail;

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

$pdf = new PDF("L","mm","A4");
$pdf->AliasNbPages();

$q = "select forest,compartment,route from routes where uid = ".$rtid;
$r = mysql_query($q) or die(mysql_error().' '.$q);
$rrow = mysql_fetch_array($r);
extract($rrow);
$ft = $forest;
$ct = $compartment;
$rt = $route;
	
$pdf->AddPage();
$pdf->SetFont('Arial','',10);
$pdf->SetLeftMargin(0);
			
$pdf->SetXY(20,9);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Arial','B',16);
$pdf->Cell(150,9,$coyname,0,0,"L",1);
$pdf->SetTextColor(0,0,0);
			
$pdf->SetXY(170,9);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Arial','',48);
$pdf->Cell(15,20,$rr,0,0,"L",1);
$pdf->SetTextColor(0,0,0);

$pdf->SetXY(20,19);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Arial','B',16);
$pdf->Cell(150,9,"SITE SPECIFIC HAZARD REPORT",0,0,"L",1);
$pdf->SetTextColor(0,0,0);
		
$pdf->SetXY(20,28);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Arial','',12);
$pdf->Cell(15,4,"The hazards described below are in addition to any that appear in the 'General List of Hazards'.",0,0,"L",1);
$pdf->SetTextColor(0,0,0);
		
$x = 20;
$y = 35;

/*
$pdf->SetXY($x,$y);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Arial','',12);
$pdf->Cell(40,4,"Contractor name: ",0,0,"L",1);
$pdf->SetTextColor(0,0,0);
		
$y = $y + 7;
*/

$pdf->SetXY($x,$y);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Arial','',12);
$pdf->Cell(40,4,"Person completing:",0,0,"L",1);
$pdf->SetTextColor(0,0,0);
		
$pdf->SetXY($x+60,$y);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Arial','',12);
$pdf->Cell(100,4,$uname,0,0,"L",1);
$pdf->SetTextColor(0,0,0);

$y = $y + 7;
		
$pdf->SetXY($x,$y);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Arial','',12);
$pdf->Cell(40,4,"Forest/Compartment/Road:",0,0,"L",1);
$pdf->SetTextColor(0,0,0);

$pdf->SetXY($x+60,$y);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Arial','',12);
$pdf->Cell(110,4,$ft."/ ".$ct."/ ".$rt,0,0,"L",1);
$pdf->SetTextColor(0,0,0);

$x = 20;
$y = $y + 7;
		
$pdf->SetXY($x,$y);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Arial','B',10);
$pdf->Cell(20,4,"Date",0,0,"L",1);
$pdf->SetTextColor(0,0,0);
			
$pdf->SetXY($x+20,$y);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Arial','B',10);
$pdf->Cell(70,4,"Hazard descripton",0,0,"L",1);
$pdf->SetTextColor(0,0,0);
		
$pdf->SetXY($x+90,$y);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Arial','B',10);
$pdf->Cell(20,4,"Risk",0,0,"L",1);
$pdf->SetTextColor(0,0,0);
		
$pdf->SetXY($x+110,$y);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Arial','B',10);
$pdf->Cell(20,4,"Strategy",0,0,"L",1);
$pdf->SetTextColor(0,0,0);
			
$pdf->SetXY($x+130,$y);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Arial','B',10);
$pdf->Cell(70,4,"Action",0,0,"L",1);
$pdf->SetTextColor(0,0,0);
		
$pdf->SetXY($x+200,$y);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Arial','B',10);
$pdf->Cell(20,4,"Local",0,0,"L",1);
$pdf->SetTextColor(0,0,0);
		
$pdf->SetXY($x+220,$y);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Arial','B',10);
$pdf->Cell(20,4,"Mgmnt",0,0,"L",1);
$pdf->SetTextColor(0,0,0);

$pdf->SetXY($x+240,$y);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Arial','B',10);
$pdf->Cell(25,4,"Risk Score",0,0,"L",1);
$pdf->SetTextColor(0,0,0);
			
$qh = "select * from site_hazards where routeid = ".$rtid;
$rh = mysql_query($qh) or die(mysql_error().' '.$qh);

$x = 20;
$y = $y + 5;

while ($row = mysql_fetch_array($rh)) {
	extract($row);
	
	$rr = 0;
	$h = 0;
	$d = 0;
	$c = 0;
	

	switch ($harm) {
		case 'None':
			$h = 0;
			break;
		case 'Insignificant':
			$h = 1;
			break;
		case 'Minor':
			$h = 2;
			break;
		case 'Temporary harm':
			$h = 3;
			break;
		case 'Serious harm':
			$h = 4;
			break;
		case 'Fatalities':
			$h = 5;
			break;
	}
	
	switch ($damage) {
		case 'None':
			$d = 0;
			break;
		case 'Under $100':
			$d = 1;
			break;
		case 'Under $1,000':
			$d = 2;
			break;
		case 'Under $5,000':
			$d = 3;
			break;
		case 'Under $50,000':
			$d = 4;
			break;
		case 'Over $50,000':
			$d = 5;
			break;
	}
	
	switch ($reoccur) {
		case 'Rare':
			$c = 1;
			break;
		case 'Possible':
			$c = 2;
			break;
		case 'Moderate':
			$c = 3;
			break;
		case 'Likely':
			$c = 4;
			break;
		case 'Certain':
			$c = 5;
			break;
		default:
			$c = 1;
			break;
	}
	
	
	$hd = max($h,$d);
	$rr = $hd * $c;


	$dt = explode('-',$ddate);
	$yr = $dt[0];
	$m = $dt[1];
	$d = $dt[2];
	$date = $d.'/'.$m.'/'.$yr;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',9);
	$pdf->Cell(25,4,$date,0,0,"L",1);
	$pdf->SetTextColor(0,0,0);

	$pdf->SetXY($x+20,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->MultiCell(70,4,$hazard,0,"L",0);
	$pdf->SetTextColor(0,0,0);
	
	$gy1 = $pdf->GetY();
				
	$pdf->SetXY($x+90,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,4,$risk,0,0,"L",1);
	$pdf->SetTextColor(0,0,0);
			
	$pdf->SetXY($x+110,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,4,$strategy,0,0,"L",1);
	$pdf->SetTextColor(0,0,0);
		
	$pdf->SetXY($x+130,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->MultiCell(70,4,$action,0,"L",0);
	$pdf->SetTextColor(0,0,0);
	
	$gy2 = $pdf->GetY();

				
	$pdf->SetXY($x+200,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,4,$local,0,0,"L",1);
	$pdf->SetTextColor(0,0,0);

	$pdf->SetXY($x+220,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,4,$management,0,0,"L",1);
	$pdf->SetTextColor(0,0,0);

	$pdf->SetXY($x+240,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(25,4,$rr,0,0,"L",1);
	$pdf->SetTextColor(0,0,0);
	
	$maxy = max($gy1,$gy2);
	$y = $maxy + 2;

}

$y = $y + 15;

$pdf->SetXY($x,$y);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Arial','',9);
$pdf->Cell(40,4,"NB. All persons coming onto this site shall read this hazard register and, in acknowlegment or their understanding, place their name and signature on the form",0,0,"L",1);
$pdf->SetTextColor(0,0,0);

$y = $y + 15;

$pdf->SetXY($x,$y);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Arial','',10);
$pdf->Cell(40,4,"Company representative",0,0,"L",1);
$pdf->SetTextColor(0,0,0);

$pdf->line($x+45,$y+4,$x+150,$y+4);

$y = $y + 20;

$pdf->SetXY($x,$y);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Arial','',10);
$pdf->Cell(40,4,"Name",0,0,"L",1);
$pdf->SetTextColor(0,0,0);

$pdf->line($x+45,$y+4,$x+120,$y+4);

$pdf->SetXY($x+150,$y);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Arial','',10);
$pdf->Cell(40,4,"Signature",0,0,"L",1);
$pdf->SetTextColor(0,0,0);

$pdf->line($x+190,$y+4,$x+260,$y+4);

$fname = 'hazards/Hazard_'.$rtid.'_'.$subscriber.'.pdf';
$pdf->Output($fname,'F');


?>
