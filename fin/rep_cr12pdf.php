<?php
session_start();
$usersession = $_SESSION['usersession'];

$admindb = $_SESSION['s_admindb'];
require("../db.php");
mysql_select_db($admindb) or die(mysql_error());
$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);

$glfile = 'ztmp'.$user_id.'_1cr';

$vac = $_SESSION['s_viewac'];
$va = explode('~',$vac);
$ac = $va[0];
$br = trim($va[1]);
$sb = $va[2];

$fromdate = $_SESSION['s_fromdate'];
$todate = $_SESSION['s_todate'];

$moduledb = $_SESSION['s_cltdb'];
mysql_select_db($moduledb) or die(mysql_error());

$q = "select concat(members.firstname,' ',members.lastname) as cname from members,client_company_xref where client_company_xref.crno = ".$ac." and client_company_xref.crsub = ".$sb." and members.member_id = client_company_xref.client_id";
$r = mysql_query($q) or die(mysql_error().' '.$q);
$row = mysql_fetch_array($r);
extract($row);
$acc = $account;

$moduledb = $_SESSION['s_findb'];
mysql_select_db($moduledb) or die(mysql_error());


$q = "select branchname from branch where branch = '".$br."'";
$r = mysql_query($q) or die(mysql_error().' '.$q);
$row = mysql_fetch_array($r);
extract($row);

$coyname = $_SESSION['s_coyname'];
$heading = $cname." - ".$ac." ".$sb." - between '".$fromdate."' and '".$todate."'";


define('FPDF_FONTPATH','../includes/font/');
require('../includes/fpdf/fpdf.php');

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
	$gtw = explode(',',$gwidths);
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

$pdf->AddPage();
$pdf->SetFont('Arial','',10);
$pdf->SetLeftMargin(0);
			
$pdf->SetXY(10,9);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Arial','B',16);
$pdf->Cell(280,9,$coyname,0,0,"L",1);
$pdf->SetTextColor(0,0,0);

$pdf->SetXY(10,18);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(175,5,$heading,0,0,"L",1);
$pdf->SetTextColor(0,0,0);

$x = 10;
$y = 25;

$pdf->SetXY($x,$y);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Arial','B',10);
$pdf->Cell(25,3,"Date",0,0,"L",1);
$pdf->SetTextColor(0,0,0);

$pdf->SetXY($x+25,$y);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Arial','B',10);
$pdf->Cell(25,3,"Account",0,0,"L",1);
$pdf->SetTextColor(0,0,0);

$pdf->SetXY($x+45,$y);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Arial','B',10);
$pdf->Cell(15,3,"Branch",0,0,"L",1);
$pdf->SetTextColor(0,0,0);

$pdf->SetXY($x+60,$y);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Arial','B',10);
$pdf->Cell(10,3,"Sub",0,0,"L",1);
$pdf->SetTextColor(0,0,0);

$pdf->SetXY($x+68,$y);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Arial','B',10);
$pdf->Cell(20,3,"Debit",0,0,"R",1);
$pdf->SetTextColor(0,0,0);

$pdf->SetXY($x+88,$y);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Arial','B',10);
$pdf->Cell(20,3,"Credit",0,0,"R",1);
$pdf->SetTextColor(0,0,0);

$pdf->SetXY($x+110,$y);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Arial','B',10);
$pdf->Cell(20,3,"Balance",0,0,"R",1);
$pdf->SetTextColor(0,0,0);

$pdf->SetXY($x+130,$y);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Arial','B',10);
$pdf->Cell(20,3,"Reference",0,0,"L",1);
$pdf->SetTextColor(0,0,0);

$pdf->SetXY($x+150,$y);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Arial','B',10);
$pdf->Cell(40,3,"Description",0,0,"L",1);
$pdf->SetTextColor(0,0,0);



$q = "select date_format(ddate,get_format(date,'EUR')) as dt,accno,br,subbr,format(debit,2) as db, format(credit,2) as cr, format(runbal,2) as rb, reference, descript1 from ".$glfile;
$r = mysql_query($q) or die(mysql_error().' '.$q);
while ($row = mysql_fetch_array($r)) {
	extract($row);

	$y = $y + 5;
	
	$pdf->SetXY($x,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',9);
	$pdf->Cell(25,3,$dt,0,0,"L",1);
	$pdf->SetTextColor(0,0,0);
	
	$pdf->SetXY($x+25,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',9);
	$pdf->Cell(25,3,$accno,0,0,"L",1);
	$pdf->SetTextColor(0,0,0);
	
	$pdf->SetXY($x+45,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',9);
	$pdf->Cell(15,3,$br,0,0,"L",1);
	$pdf->SetTextColor(0,0,0);
	
	$pdf->SetXY($x+60,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',9);
	$pdf->Cell(10,3,$subbr,0,0,"L",1);
	$pdf->SetTextColor(0,0,0);
	
	$pdf->SetXY($x+68,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',9);
	$pdf->Cell(20,3,$db,0,0,"R",1);
	$pdf->SetTextColor(0,0,0);
	
	$pdf->SetXY($x+88,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',9);
	$pdf->Cell(20,3,$cr,0,0,"R",1);
	$pdf->SetTextColor(0,0,0);
	
	$pdf->SetXY($x+110,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',9);
	$pdf->Cell(20,3,$rb,0,0,"R",1);
	$pdf->SetTextColor(0,0,0);
	
	$pdf->SetXY($x+130,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',9);
	$pdf->Cell(20,3,$reference,0,0,"L",1);
	$pdf->SetTextColor(0,0,0);
	
	$pdf->SetXY($x+150,$y);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',9);
	$pdf->Cell(40,3,$descript1,0,0,"L",1);
	$pdf->SetTextColor(0,0,0);
	
		
	if ($y > 260) {
		$y = 20;
		$pdf->AddPage();
	}

}

$fname = 'gl1ac_'.$ac.'.pdf';
$pdf->Output($fname,'I');

?>
