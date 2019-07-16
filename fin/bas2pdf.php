<?php
session_start();
$coyname = $_SESSION['s_coyname'];
$coyid = $_SESSION['s_coyid'];
$gstfile = $_REQUEST['gstfile'];
$dates = $_REQUEST['heading'];

require("../db.php");
$moduledb = $_SESSION['s_findb'];
mysql_select_db($moduledb) or die(mysql_error());

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

$pdf = new FPDF("P","mm","A4");
$pdf->AliasNbPages();

$pdf->AddPage();
$pdf->SetFont('Arial','',10);
$pdf->SetLeftMargin(0);
$pdf->SetAutopageBreak(1,1);
		
$pdf->SetXY(15,10);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Arial','B',16);
$pdf->Cell(160,9,"Goods and Services tax return",0,0,"L",1);
$pdf->SetTextColor(0,0,0);

$pdf->SetXY(15,20);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Arial','B',16);
$pdf->Cell(160,9,$coyname,0,0,"L",1);
$pdf->SetTextColor(0,0,0);

$pdf->SetXY(15,30);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Arial','B',16);
$pdf->Cell(160,9,$dates,0,0,"L",1);
$pdf->SetTextColor(0,0,0);

$x = 15;
$y = 50;

$qr = "select subject,box,amount from ".$gstfile;
$rr = mysql_query($qr) or die(mysql_error().' '.$qr);
while ($row = mysql_fetch_array($rr)) {
	extract($row);

	$pdf->SetLineWidth(0.2);
	$pdf->SetDrawcolor(0,0,0);
	$pdf->Rect($x,$y,80,6,'D');

	$pdf->SetXY($x+2,$y+1.5);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(75,3,$subject,0,0,"L",0);
	$pdf->SetTextColor(0,0,0);
	
	$x = $x + 80;
	
	$pdf->SetLineWidth(0.2);
	$pdf->SetDrawcolor(0,0,0);
	$pdf->Rect($x,$y,15,6,'D');

	$pdf->SetXY($x+2,$y+1.5);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(10,3,$box,0,0,"L",0);
	$pdf->SetTextColor(0,0,0);
	
	$x = $x + 15;
	
	$pdf->SetLineWidth(0.2);
	$pdf->SetDrawcolor(0,0,0);
	$pdf->Rect($x,$y,30,6,'D');

	$pdf->SetXY($x+2,$y+1.5);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(25,3,$amount,0,0,"R",0);
	$pdf->SetTextColor(0,0,0);
	
	
	
	$x = 15;
	$y = $y + 7.5;
	
}






$dte = date('Y-m-d');
$fname =  $_SESSION['s_tradtax'].'_'.$dte.'.pdf';
$pdf->Output($fname,'I');

?>
