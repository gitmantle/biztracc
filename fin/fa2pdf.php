<?php
session_start();
$usersession = $_SESSION['usersession'];

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];
$subscriber = $subid;
$sname = $row['uname'];

ini_set('display_errors', true);

$coyname = $_SESSION['s_coyname'];
$hed = $_REQUEST['heading'];
$heading = $coyname.' - '.$hed;

define('FPDF_FONTPATH','../includes/font/');
require('../includes/fpdf/fpdf.php');

$asfile = 'ztmp'.$user_id.'_assets';

$findb = $_SESSION['s_findb'];

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

$pdf=new PDF('L','mm','A4');
$pdf->AliasNbPages();
$pdf->AddPage();

$pdf->SetFillColor(0,120,199);
$pdf->SetTextColor(255);
$pdf->SetFont('Arial','B',14);
$pdf->SetLeftMargin(20);
$pdf->Cell(250,7,$heading,1,0,'C',1);
$pdf->Ln();		

$db->query("select aname,bought,acost,adepn,abv,atot,Totline from ".$findb.".".$asfile);
$rows = $db->resultset();

		$pdf->SetLeftMargin(30);
	    $w=array(80,30,30,30,30,30);  
		$pdf->SetTextColor(0);
		$pdf->SetFont('Arial','B',11);			
		$pdf->SetFillColor(0);
		$pdf->Cell($w[0],6,'Asset','0',0,'L',0);
		$pdf->Cell($w[1],6,'Bought','0',0,'L',0);
		$pdf->Cell($w[2],6,'Cost','0',0,'R',0);
		$pdf->Cell($w[2],6,'Depreciation','0',0,'R',0);
		$pdf->Cell($w[4],6,'Book Value','0',0,'R',0);
		$pdf->Cell($w[5],6,'Total','0',0,'R',0);
		$pdf->SetLeftMargin(20);
	
		$pdf->Ln();		

	
	foreach ($rows as $row) {
		extract($row);
		$pdf->SetLeftMargin(30);
	    $w=array(80,30,30,30,30,30);  
		$pdf->SetTextColor(0);
		$pdf->SetFont('Arial','',10);			
		$pdf->SetFillColor(0);
		$pdf->Cell($w[0],6,$aname,'0',0,'L',0);
		$pdf->Cell($w[1],6,$bought,'0',0,'L',0);
		$pdf->Cell($w[2],6,number_format($acost,2),'0',0,'R',0);
		$pdf->Cell($w[3],6,number_format($adepn,2),'0',0,'R',0);
		$pdf->Cell($w[4],6,number_format($abv,2),'0',0,'R',0);
		$pdf->Cell($w[5],6,number_format($atot,2),'0',0,'R',0);
		$pdf->SetLeftMargin(20);
	
		$pdf->Ln();		
	
	}

$db->closeDB();


$pdf->Output();
?>

