<?php
error_reporting (E_ALL ^ E_NOTICE ^ E_WARNING);
session_start();

define('FPDF_FONTPATH','../../includes/font/');
require('../../includes/fpdf/fpdf.php');

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
	function MaxLines($ldetails, $gwidths, $font, $attrib, $fontsize)
	//********************************************************************
	{
	
	$nlines = 0;
	$gtw = explode(',',$gwidths);
	$gcount = count($gtw) - 1;
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

//*****************************************************************************
// get data
//****************************************************************************

require("../../db.php"); 

$template =  $_SESSION['sp_templatedb'].'.'.$_SESSION['sp_template'];
$postaladdress = $_SESSION['sp_postaladdress'];
$deliveryaddress = $_SESSION['sp_deliveryaddress'];
$footmessage =	$_SESSION['sp_footmessage'];
$detailstable =	$_SESSION['sp_templatedb'].'.'.$_SESSION['sp_detailstable'];
$header1 =	$_SESSION['sp_heading1'];
$header2 =	$_SESSION['sp_heading2'];

$moduledb = $_SESSION['s_findb'];
mysql_select_db($moduledb) or die(mysql_error());

$q = "select * from globals";
$r = mysql_query($q) or die (mysql_error().' '.$q);
$row = mysql_fetch_array($r);
extract($row);

$fromadd = $ad1." ".$ad2."\n";
$fromadd .= $ad3."\n";
$fromadd .= "Phone: ".$telno."\n";
$fromadd .= "Fax:   ".$faxno."\n";
$fromadd .= "email: ".$email;
$email_from = $email;
$coyname = $coyname;

$query = "select * from ".$template." where item = 'page'";
$result = mysql_query($query) or die(mysql_error().' '.$query);
$row = mysql_fetch_array($result);
extract($row);
$st = explode(',',$content);
$orient = $st[0];
$units = $st[1];
$paper = $st[2];
$fpdfstring = "'".$orient."','".$units."','".$paper."'";
$ft = explode(',',$font);
$fontname = $ft[0];
$attrib = $ft[1];
$ftsize = $ft[2];

$pdf=new PDF($orient,$units,$paper);
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont($fontname,$attrib,$ftsize);
$pdf->SetLeftMargin(0);

//***************************************************************************
function heading() 
//***************************************************************************
{
	global $template, $pdf, $orient, $paper, $postaladdress, $deliveryaddress, $fromadd, $header1, $header2;
	date_default_timezone_set($_SESSION['s_timezone']);

	$dt = date("j F,Y");
	
	$query = "select * from ".$template." where item = 'watermark'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($_SESSION['watermark'] == 'Y') {
		$ft = explode(',',$font);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		$pdf->SetTextColor($textcolor);
		$pdf->Rotate(45,55,190);
		$pdf->Text($xcoord,$ycoord,$content);
		$pdf->Rotate(0);
		$pdf->SetTextColor(0,0,0);		
	}

	//if A4 and Portrait add fold marks
	if ($orient == 'P' && $paper == 'A4') {
		$pdf->Line(1,99,5,99);
		$pdf->Line(205,99,209,99);
		$pdf->Line(1,198,5,198);
		$pdf->Line(205,198,209,198);
	}

	$query = "select * from ".$template." where item = 'image'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$pdf->Image($content,$xcoord,$ycoord,$cellwidth,$cellheight,'jpg');
	}	
		
	$query = "select * from ".$template." where item = 'box1'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->Cell($cellwidth,$cellheight,'',$border,$nextpos,$align,$fill);
	}
	
	$query = "select * from ".$template." where item = 'box2'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->Cell($cellwidth,$cellheight,'',$border,$nextpos,$align,$fill);
	}
	
	$query = "select * from ".$template." where item = 'box3'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->Cell($cellwidth,$cellheight,'',$border,$nextpos,$align,$fill);
	}
	
	$query = "select * from ".$template." where item = 'box4'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->Cell($cellwidth,$cellheight,'',$border,$nextpos,$align,$fill);
	}
	
	$query = "select * from ".$template." where item = 'box5'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->Cell($cellwidth,$cellheight,'',$border,$nextpos,$align,$fill);
	}
	
	$query = "select * from ".$template." where item = 'box6'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->Cell($cellwidth,$cellheight,'',$border,$nextpos,$align,$fill);
	}
	
	$query = "select * from ".$template." where item = 'box7'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->Cell($cellwidth,$cellheight,'',$border,$nextpos,$align,$fill);
	}
	
	$query = "select * from ".$template." where item = 'box8'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->Cell($cellwidth,$cellheight,'',$border,$nextpos,$align,$fill);
	}
	
	$query = "select * from ".$template." where item = 'box9'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->Cell($cellwidth,$cellheight,'',$border,$nextpos,$align,$fill);
	}
	
	
	$query = "select * from ".$template." where item = 'rbox1'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->RoundedRect($xcoord, $ycoord, $cellwidth, $cellheight, 2.5, 'D');
	}
	
	$query = "select * from ".$template." where item = 'rbox2'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->RoundedRect($xcoord, $ycoord, $cellwidth, $cellheight, 2.5, 'D');
	}
	
	$query = "select * from ".$template." where item = 'rbox3'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->RoundedRect($xcoord, $ycoord, $cellwidth, $cellheight, 2.5, 'D');
	}
	
	$query = "select * from ".$template." where item = 'rbox4'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->RoundedRect($xcoord, $ycoord, $cellwidth, $cellheight, 2.5, 'D');
	}
	
	$query = "select * from ".$template." where item = 'rbox5'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->RoundedRect($xcoord, $ycoord, $cellwidth, $cellheight, 2.5, 'D');
	}
	
	$query = "select * from ".$template." where item = 'rbox6'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->RoundedRect($xcoord, $ycoord, $cellwidth, $cellheight, 2.5, 'D');
	}
	
	$query = "select * from ".$template." where item = 'rbox7'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->RoundedRect($xcoord, $ycoord, $cellwidth, $cellheight, 2.5, 'D');
	}
	
	$query = "select * from ".$template." where item = 'rbox8'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->RoundedRect($xcoord, $ycoord, $cellwidth, $cellheight, 2.5, 'D');
	}
	
	$query = "select * from ".$template." where item = 'rbox9'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->RoundedRect($xcoord, $ycoord, $cellwidth, $cellheight, 2.5, 'D');
	}
	
	$query = "select * from ".$template." where item = 'fromname'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$font);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
	}
	
	$query = "select * from ".$template." where item = 'fromaddress'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$font);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->MultiCell($cellwidth,$cellheight,$fromadd);
	}
	
	$query = "select * from ".$template." where item = 'toaddress'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$font);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->MultiCell($cellwidth,$cellheight,$deliveryaddress);
	}
	
	$query = "select * from ".$template." where item = 'delivery'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$font);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->MultiCell($cellwidth,$cellheight,$content);
	}

	
	$query = "select * from ".$template." where item = 'header1'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$font);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->Cell($cellwidth,$cellheight,$header1,$border,$nextpos,$align,$fill);
	}
	
	$query = "select * from ".$template." where item = 'header2'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$font);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->Cell($cellwidth,$cellheight,$header2,$border,$nextpos,$align,$fill);
	}
	
	$query = "select * from ".$template." where item = 'ref1'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$font);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->Cell($cellwidth,$cellheight,$reference,$border,$nextpos,$align,$fill);
	}

	$query = "select * from ".$template." where item = 'notes'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$font);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->MultiCell($cellwidth,$cellheight,$notes);
	}
	
	$query = "select * from ".$template." where item = 'label1'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$font);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
	}
	
	$query = "select * from ".$template." where item = 'label2'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$font);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
	}
	
	$query = "select * from ".$template." where item = 'label3'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$font);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
	}
	
	$query = "select * from ".$template." where item = 'label4'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$font);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
	}
	
	$query = "select * from ".$template." where item = 'label5'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$font);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
	}
	
	$query = "select * from ".$template." where item = 'label6'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$font);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
	}
	
	$query = "select * from ".$template." where item = 'label7'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$font);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
	}
	
	$query = "select * from ".$template." where item = 'label8'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$font);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
	}
	
	$query = "select * from ".$template." where item = 'label9'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$font);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
	}
	
	$query = "select * from ".$template." where item = 'label10'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$font);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
	}
	
	$query = "select * from ".$template." where item = 'label11'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$font);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
	}
	
	$query = "select * from ".$template." where item = 'label12'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$font);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
	}
	
	$query = "select * from ".$template." where item = 'label13'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$font);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
	}
	
	$query = "select * from ".$template." where item = 'label14'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$font);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
	}
	
	$query = "select * from ".$template." where item = 'label15'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$font);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
	}
	
	$query = "select * from ".$template." where item = 'label16'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$font);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
	}
	
	$query = "select * from ".$template." where item = 'label17'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$font);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
	}
	
	$query = "select * from ".$template." where item = 'label18'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$font);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
	}
	
	$query = "select * from ".$template." where item = 'label19'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$font);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
	}
	
	$query = "select * from ".$template." where item = 'label20'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$font);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
	}
	
	
	
	$query = "select * from ".$template." where item = 'docdate'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$font);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->Cell($cellwidth,$cellheight,$dt,$border,$nextpos,$align,$fill);
	}
	$query = "select * from ".$template." where item = 'ref2'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$font);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->Cell($cellwidth,$cellheight,$xref,$border,$nextpos,$align,$fill);
	}
	
	$query = "select * from ".$template." where item = 'gst'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$font);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->Cell($cellwidth,$cellheight,$gstabn,$border,$nextpos,$align,$fill);
	}
	
} // function heading()


//******************************************************************************
function details()
//******************************************************************************
{
	global $template, $pdf, $lastpage, $currentline, $detailstable, $did;
	
	$q = "select * from ".$detailstable." where depot_id = ".$did;
	$rdetails = mysql_query($q) or die(mysql_error().' '.$q);
	$numlines = mysql_num_rows($rdetails);
	while ($row = mysql_fetch_array($rdetails)) {
		$linedetails[] = $row;
	}

	$query = "select * from ".$template." where item = 'gridtitle'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$font);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		$gtw = explode(',',$gridwidths);
		$galign = explode(',',$align);
		$gt = explode(',',$content);
		$gcount = count($gt) - 1;
		
		for ($n = 0; $n <= $gcount; $n++) {
			$pdf->Cell($gtw[$n],$cellheight,$gt[$n],$border,$nextpos,$galign[$n],$fill);
		}
	}
	
	
	$query = "select * from ".$template." where item = 'griddetail'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	$gtw = explode(',',$gridwidths);
	$gcount = count($gtw) - 1;
	$xpos = $xcoord;
	$ypos = $ycoord;
	$numlines = count($linedetails);
	if ($include == 'Y' && $numlines > 0) {
		$ft = explode(',',$font);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		$galign = explode(',',$align);
		$gformat = explode(',',$content);
		
		$maxypos = $ycoord;
		
		while ($currentline < $numlines) {
			
			//if ($pdf->PageNo() == 1) {
				$xpos = $xcoord;
				$ypos = $ycoord;
				$maxypos = $ycoord;
				
				for ($i = $currentline; $i < $numlines; $i++) {
					$row = $linedetails[$i];
					
					// work out the maximum number of lines required for this entry and go to next page if not enough space
					$spaceneeded = $pdf->MaxLines($linedetails[$i],$gridwidths,$fontname,$attrib,$ftsize);
					if (($spaceneeded * $cellheight) > (230 - $ypos)) {
						$lastpage = 'N';
						$currentline++;
						$pdf->AddPage();
						break;
					}			
	
					// print row
					for ($n = 0; $n <= $gcount; $n++) {
						
						if ($gformat[$n] == 'N') {
							$texte = number_format($row[$n],2);
						} else {
							$texte = $row[$n];
						}
						
						$length    = $pdf->GetStringWidth( $texte );
						//$textLines = $pdf->sizeOfText( $texte, $length );
						
						$pdf->SetXY( $xpos, $ypos);
						$pdf->MultiCell( $gtw[$n]-2, $cellheight , $texte, $border, $galign[$n]);
						$pdf->Ln();
						if ( $maxypos < ($pdf->GetY()  ) ) {
							$maxypos = $pdf->GetY() ;
						}
						$xpos += $gtw[$n];		
						
					} //for
					
					$xpos = $xcoord;
					$ypos = $maxypos;
					$currentline = $i +1;
					
					if ($ypos > 230 && ($currentline < $numlines)) {
						break;
					} 
					
					if ($currentline >= $numlines) {
						$lastpage = 'Y';
					}
					
				// draw boxes around grid details
				$nextx = $xcoord;
				for ($b = 0; $b < $gcount+1; $b++) {
					$boxlength = 124;
					$pdf->SetXY($nextx,$ycoord-1);
					$pdf->Cell($gtw[$b],$boxlength,'',1);	
					$nextx += $gtw[$b];
				}	// for
	
				} // foreach
			/*	
			} else { // if pageno > 1
				$xpos = $xcoord;
				$ypos = $ycoord - 80;
				$maxypos = $ycoord - 80;
			
				for ($i = $currentline; $i < $numlines; $i++) {
					$row = $linedetails[$i];
					
					// work out the maximum number of lines required for this entry and go to next page if not enough space
					$spaceneeded = $pdf->MaxLines($linedetails[$i],$gridwidths,$fontname,$attrib,$ftsize);
					if (($spaceneeded * $cellheight) > (230 - $ypos)) {
						$lastpage = 'N';
						$currentline++;
						$pdf->AddPage();
						break;
					}			
	
					// print row
					for ($n = 0; $n <= $gcount; $n++) {
						
						if ($gformat[$n] == 'N') {
							$texte = number_format($row[$n],2);
						} else {
							$texte = $row[$n];
						}
						
						$length    = $pdf->GetStringWidth( $texte );
						//$textLines = $pdf->sizeOfText( $texte, $length );
						
						$pdf->SetXY( $xpos, $ypos);
						$pdf->MultiCell( $gtw[$n]-2, $cellheight , $texte, $border, $galign[$n]);
						$pdf->Ln();
						if ( $maxypos < ($pdf->GetY()  ) ) {
							$maxypos = $pdf->GetY() ;
						}
						$xpos += $gtw[$n];		
						
					} //for
					
					$xpos = $xcoord;
					$ypos = $maxypos;
					$currentline = $i + 1;
					
					if ($ypos > 230 && ($currentline < $numlines)) {
						break;
					} 
					
					if ($currentline >= $numlines) {
						$lastpage = 'Y';
					}
					
				// draw boxes around grid details
				$nextx = $xcoord;
				for ($b = 0; $b < $gcount+1; $b++) {
					$boxlength = 124 + 80;
					$pdf->SetXY($nextx,$ycoord-81);
					$pdf->Cell($gtw[$b],$boxlength,'',1);	
					$nextx += $gtw[$b];
				}	// for
	
				} // foreach
			
			}
			*/
		} //while
		
	} else {		//if included
		$lastpage = 'Y';
	}

} // function details()

//********************************************************************************
function footer()
//********************************************************************************
{
	global $template, $pdf, $reference, $tradingref, $remmitance, $footmessage, $signature;
	
	$query = "select * from ".$template." where item = 'footmessage'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$font);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->MultiCell($cellwidth,$cellheight,$footmessage);
	}
	
	$query = "select * from ".$template." where item = 'footbox1'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->Cell($cellwidth,$cellheight,'',$border,$nextpos,$align,$fill);
	}
	
	$query = "select * from ".$template." where item = 'footlabel1'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$font);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
	}
	
	$query = "select * from ".$template." where item = 'footlabel2'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$font);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
	}
	
	$query = "select * from ".$template." where item = 'footlabel3'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$font);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
	}


	$query = "select * from ".$template." where item = 'footlabel4'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$font);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
	}
	
	$query = "select * from ".$template." where item = 'footlabel5'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$font);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
	}
	
	$query = "select * from ".$template." where item = 'footlabel6'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$font);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
	}
	
	$query = "select * from ".$template." where item = 'footlabel7'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$font);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
	}
	
	$query = "select * from ".$template." where item = 'footlabel8'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$font);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
	}
	
	$query = "select * from ".$template." where item = 'footlabel9'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$font);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
	}
	
	
	
	$query = "select * from ".$template." where item = 'totval'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$font);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->Cell($cellwidth,$cellheight,number_format($totalvalue,2),$border,$nextpos,$align,$fill);
	}
	
	$query = "select * from ".$template." where item = 'tottax'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$font);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->Cell($cellwidth,$cellheight,number_format($totaltax,2),$border,$nextpos,$align,$fill);
	}
	
	$query = "select * from ".$template." where item = 'totdue'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$font);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->Cell($cellwidth,$cellheight,number_format($totaldue,2),$border,$nextpos,$align,$fill);
	}

	$query = "select * from ".$template." where item = 'signature'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y' && $signature <> '') {
		$pdf->Image($signature,$xcoord,$ycoord,$cellwidth,$cellheight,'png');
	}	
	

	//Remittance Advice  slip
	
	if ($remmitance == 'Y') {
		$pdf->Line(20,252,200,252);
		$query = "select * from ".$template." where item = 'remmitance'";
		$result = mysql_query($query) or die(mysql_error());
		$row = mysql_fetch_array($result);
		extract($row);
		if ($include == 'Y') {		
			$ft = explode(',',$font);
			$fontname = $ft[0];
			$attrib = $ft[1];
			$ftsize = $ft[2];
			$pdf->SetXY($xcoord,$ycoord);
			$pdf->SetFont($fontname,$attrib,$ftsize);
			
			$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);		
			
			$query = "select * from ".$template." where item = 'remitbox'";
			$result = mysql_query($query) or die(mysql_error());
			$row = mysql_fetch_array($result);
			extract($row);
			if ($include == 'Y') {
				$pdf->SetXY($xcoord,$ycoord);
				$pdf->Cell($cellwidth,$cellheight,'',$border,$nextpos,$align,$fill);
			}			
					
			$query = "select * from ".$template." where item = 'remitlabel1'";
			$result = mysql_query($query) or die(mysql_error());
			$row = mysql_fetch_array($result);
			extract($row);
			if ($include == 'Y') {
				$ft = explode(',',$font);
				$fontname = $ft[0];
				$attrib = $ft[1];
				$ftsize = $ft[2];
				$pdf->SetXY($xcoord,$ycoord);
				$pdf->SetFont($fontname,$attrib,$ftsize);
				
				$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
			}			
				
			$query = "select * from ".$template." where item = 'remitlabel2'";
			$result = mysql_query($query) or die(mysql_error());
			$row = mysql_fetch_array($result);
			extract($row);
			if ($include == 'Y') {
				$ft = explode(',',$font);
				$fontname = $ft[0];
				$attrib = $ft[1];
				$ftsize = $ft[2];
				$pdf->SetXY($xcoord,$ycoord);
				$pdf->SetFont($fontname,$attrib,$ftsize);
				
				$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
			}			
				
			$query = "select * from ".$template." where item = 'remitlabel3'";
			$result = mysql_query($query) or die(mysql_error());
			$row = mysql_fetch_array($result);
			extract($row);
			if ($include == 'Y') {
				$ft = explode(',',$font);
				$fontname = $ft[0];
				$attrib = $ft[1];
				$ftsize = $ft[2];
				$pdf->SetXY($xcoord,$ycoord);
				$pdf->SetFont($fontname,$attrib,$ftsize);
				
				$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
			}			
				
			$query = "select * from ".$template." where item = 'remitlabel4'";
			$result = mysql_query($query) or die(mysql_error());
			$row = mysql_fetch_array($result);
			extract($row);
			if ($include == 'Y') {
				$ft = explode(',',$font);
				$fontname = $ft[0];
				$attrib = $ft[1];
				$ftsize = $ft[2];
				$pdf->SetXY($xcoord,$ycoord);
				$pdf->SetFont($fontname,$attrib,$ftsize);
				
				$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
			}			
				
			$query = "select * from ".$template." where item = 'remitlabel5'";
			$result = mysql_query($query) or die(mysql_error());
			$row = mysql_fetch_array($result);
			extract($row);
			if ($include == 'Y') {
				$ft = explode(',',$font);
				$fontname = $ft[0];
				$attrib = $ft[1];
				$ftsize = $ft[2];
				$pdf->SetXY($xcoord,$ycoord);
				$pdf->SetFont($fontname,$attrib,$ftsize);
				
				$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
			}

			$query = "select * from ".$template." where item = 'rtotdue'";
			$result = mysql_query($query) or die(mysql_error());
			$row = mysql_fetch_array($result);
			extract($row);
			if ($include == 'Y') {
				$ft = explode(',',$font);
				$fontname = $ft[0];
				$attrib = $ft[1];
				$ftsize = $ft[2];
				$pdf->SetXY($xcoord,$ycoord);
				$pdf->SetFont($fontname,$attrib,$ftsize);
				
				$pdf->Cell($cellwidth,$cellheight,number_format($totaldue,2),$border,$nextpos,$align,$fill);
			}

			$query = "select * from ".$template." where item = 'ramtpaid'";
			$result = mysql_query($query) or die(mysql_error());
			$row = mysql_fetch_array($result);
			extract($row);
			if ($include == 'Y') {
				$ft = explode(',',$font);
				$fontname = $ft[0];
				$attrib = $ft[1];
				$ftsize = $ft[2];
				$pdf->SetXY($xcoord,$ycoord);
				$pdf->SetFont($fontname,$attrib,$ftsize);
				
				$pdf->Cell($cellwidth,$cellheight,'',$border,$nextpos,$align,$fill);
			}

			$query = "select * from ".$template." where item = 'rref1'";
			$result = mysql_query($query) or die(mysql_error());
			$row = mysql_fetch_array($result);
			extract($row);
			if ($include == 'Y') {
				$ft = explode(',',$font);
				$fontname = $ft[0];
				$attrib = $ft[1];
				$ftsize = $ft[2];
				$pdf->SetXY($xcoord,$ycoord);
				$pdf->SetFont($fontname,$attrib,$ftsize);
				
				$pdf->Cell($cellwidth,$cellheight,$reference,$border,$nextpos,$align,$fill);
			}
				
		}
	}		
	
	
} //function footer()


$moduledb = $_SESSION['s_prcdb'];
mysql_select_db($moduledb) or die(mysql_error());

$qf = "select distinct depot_id from ".$detailstable;
$rf = mysql_query($qf) or die(mysql_error().' '.$qf);

$numlines = mysql_num_rows($rf);
$dmeds = array();
if ($numlines > 0) {
	while ($row = mysql_fetch_array($rf)) {
		extract($row);
		$dmeds[] = $depot_id;
	}

	foreach ($dmeds as $value) {
		$q = "select deladdress from ".$detailstable." where depot_id = ".$value;
		$lastpage = 'N';
		$currentline = 0;
		$did = $value; 
		
		$rd = mysql_query($q) or die(mysql_error().' '.$q);
		while ($rowd = mysql_fetch_array($rd)) {
			extract($rowd);	
			$deliveryaddress = $deladdress;
	
			while ($lastpage == 'N') {
				heading();
				details();
			}
			footer();
		}
		$pdf->AddPage();
		$lastpage = 'N';
		$currentline = 0;
	}
}

$fn = "depotmedslist.pdf";
$pdf->Output($fn,'I');
$pdf->close();


?>

