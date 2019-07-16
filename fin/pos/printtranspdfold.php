<?php
//ini_set('display_errors', true);
session_start();

define('FPDF_FONTPATH','../font/');
require('../includes/fpdf.php');

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


}

$userip=$_SESSION['userip'];
$template = $_SESSION['template'];
$userip = $_SESSION['userip'];
$coyname = $_SESSION['coyname'];
$ddate = $_SESSION['ddate'];
$reference = $_SESSION['reference'];
$xref = $_SESSION['xref'];
$totalline = $_SESSION['totalline'];
$toname = trim($_SESSION['toname']);
$toad1 = trim($_SESSION['toad1']);
$toad2 = trim($_SESSION['toad2']);
$toad3 = trim($_SESSION['toad3']);
$toad4 = trim($_SESSION['toad4']);
$linedetails = $_SESSION['linedetails'];
$totalline = $_SESSION['totalline'];
$totalvalue = $totalline[0];
$totaltax = $totalline[1];
$totaldue = $totalline[2];
$notes = $_SESSION['notes'];
$worknotes = $_SESSION['worknotes'];

$y = substr($ddate,0,4);
$m = substr($ddate,5,2);
$d = substr($ddate,8,2);
$dt = date("d-M-Y",time(0,0,0,$m,$d,$y));



require_once("../includes/db2.php");
mysql_select_db($dbase) or die(mysql_error());

$query = "select postal1, postal2, pcity, pstate,ppcode,trade_name,abn,phone,fax,email from zsys_dealerowner WHERE `office_id` = ".$_SESSION['office_id'];
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);		
$fromadd = $postal1." ".$postal2."\n";
$fromadd .= $pcity." ".$pstate." ".$ppcode."\n";
$fromadd .= "Phone: ".$phone."\n";
$fromadd .= "Fax:   ".$fax."\n";
$fromadd .= "email: ".$email;

$toadd = $toname."\n";
$toadd .= $toad1." ".$toad2."\n";
$toadd .= $toad3."\n";
$toadd .= $toad4;

$query = "select * from $accdbase.".$template." where item = 'page'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);
$st = explode(',',$content);
$orient = $st[0];
$units = $st[1];
$paper = $st[2];
$fpdfstring = "'".$orient."','".$units."','".$paper."'";
$ft = explode(',',$fontfamily);
$fontname = $ft[0];
$attrib = $ft[1];
$ftsize = $ft[2];

$pdf=new PDF($orient,$units,$paper);
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont($fontname,$attrib,$ftsize);
$pdf->SetLeftMargin(0);

$lastpage = 'N';
$currentline = 0;

while ($lastpage == 'N') {

	$query = "select * from $accdbase.".$template." where item = 'watermark'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$fontfamily);
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

	$query = "select * from $accdbase.".$template." where item = 'image'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$pdf->Image($content,$xcoord,$ycoord,$cellwidth,$cellheight,'jpg');
	}	
	
	$query = "select * from $accdbase.".$template." where item = 'box1'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->Cell($cellwidth,$cellheight,'',$border,$nextpos,$align,$fill);
	}
	
	$query = "select * from $accdbase.".$template." where item = 'box2'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->Cell($cellwidth,$cellheight,'',$border,$nextpos,$align,$fill);
	}
	
	$query = "select * from $accdbase.".$template." where item = 'box3'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->Cell($cellwidth,$cellheight,'',$border,$nextpos,$align,$fill);
	}
	
	$query = "select * from $accdbase.".$template." where item = 'box4'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->Cell($cellwidth,$cellheight,'',$border,$nextpos,$align,$fill);
	}
	
	$query = "select * from $accdbase.".$template." where item = 'box5'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->Cell($cellwidth,$cellheight,'',$border,$nextpos,$align,$fill);
	}
	
	$query = "select * from $accdbase.".$template." where item = 'box6'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->Cell($cellwidth,$cellheight,'',$border,$nextpos,$align,$fill);
	}
	
	$query = "select * from $accdbase.".$template." where item = 'box7'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->Cell($cellwidth,$cellheight,'',$border,$nextpos,$align,$fill);
	}
	
	$query = "select * from $accdbase.".$template." where item = 'box8'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->Cell($cellwidth,$cellheight,'',$border,$nextpos,$align,$fill);
	}
	
	$query = "select * from $accdbase.".$template." where item = 'box9'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->Cell($cellwidth,$cellheight,'',$border,$nextpos,$align,$fill);
	}
	
	
	$query = "select * from $accdbase.".$template." where item = 'rbox1'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->RoundedRect($xcoord, $ycoord, $cellwidth, $cellheight, 2.5, 'D');
	}
	
	$query = "select * from $accdbase.".$template." where item = 'rbox2'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->RoundedRect($xcoord, $ycoord, $cellwidth, $cellheight, 2.5, 'D');
	}
	
	$query = "select * from $accdbase.".$template." where item = 'rbox3'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->RoundedRect($xcoord, $ycoord, $cellwidth, $cellheight, 2.5, 'D');
	}
	
	$query = "select * from $accdbase.".$template." where item = 'rbox4'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->RoundedRect($xcoord, $ycoord, $cellwidth, $cellheight, 2.5, 'D');
	}
	
	$query = "select * from $accdbase.".$template." where item = 'rbox5'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->RoundedRect($xcoord, $ycoord, $cellwidth, $cellheight, 2.5, 'D');
	}
	
	$query = "select * from $accdbase.".$template." where item = 'rbox6'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->RoundedRect($xcoord, $ycoord, $cellwidth, $cellheight, 2.5, 'D');
	}
	
	$query = "select * from $accdbase.".$template." where item = 'rbox7'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->RoundedRect($xcoord, $ycoord, $cellwidth, $cellheight, 2.5, 'D');
	}
	
	$query = "select * from $accdbase.".$template." where item = 'rbox8'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->RoundedRect($xcoord, $ycoord, $cellwidth, $cellheight, 2.5, 'D');
	}
	
	$query = "select * from $accdbase.".$template." where item = 'rbox9'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->RoundedRect($xcoord, $ycoord, $cellwidth, $cellheight, 2.5, 'D');
	}
	
	$query = "select * from $accdbase.".$template." where item = 'fromname'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$fontfamily);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->Cell($cellwidth,$cellheight,$trade_name,$border,$nextpos,$align,$fill);
	}
	
	$query = "select * from $accdbase.".$template." where item = 'fromaddress'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$fontfamily);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->MultiCell($cellwidth,$cellheight,$fromadd);
	}
	
	$query = "select * from $accdbase.".$template." where item = 'toaddress'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$fontfamily);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->MultiCell($cellwidth,$cellheight,$toadd);
	}
	

	
	$query = "select * from $accdbase.".$template." where item = 'header1'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$fontfamily);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
	}
	
	$query = "select * from $accdbase.".$template." where item = 'header2'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$fontfamily);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
	}
	
	$query = "select * from $accdbase.".$template." where item = 'ref1'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$fontfamily);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->Cell($cellwidth,$cellheight,$reference,$border,$nextpos,$align,$fill);
	}

	$query = "select * from $accdbase.".$template." where item = 'notes'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$fontfamily);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->MultiCell($cellwidth,$cellheight,$notes);
	}
	
	$query = "select * from $accdbase.".$template." where item = 'label1'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$fontfamily);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
	}
	
	$query = "select * from $accdbase.".$template." where item = 'label2'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$fontfamily);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
	}
	
	$query = "select * from $accdbase.".$template." where item = 'label3'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$fontfamily);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
	}
	
	$query = "select * from $accdbase.".$template." where item = 'label4'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$fontfamily);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
	}
	
	$query = "select * from $accdbase.".$template." where item = 'label5'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$fontfamily);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
	}
	
	$query = "select * from $accdbase.".$template." where item = 'label6'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$fontfamily);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
	}
	
	$query = "select * from $accdbase.".$template." where item = 'label7'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$fontfamily);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
	}
	
	$query = "select * from $accdbase.".$template." where item = 'label8'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$fontfamily);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
	}
	
	$query = "select * from $accdbase.".$template." where item = 'label9'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$fontfamily);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
	}
	
	$query = "select * from $accdbase.".$template." where item = 'label10'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$fontfamily);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
	}
	
	$query = "select * from $accdbase.".$template." where item = 'label11'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$fontfamily);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
	}
	
	$query = "select * from $accdbase.".$template." where item = 'label12'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$fontfamily);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
	}
	
	$query = "select * from $accdbase.".$template." where item = 'label13'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$fontfamily);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
	}
	
	$query = "select * from $accdbase.".$template." where item = 'label14'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$fontfamily);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
	}
	
	$query = "select * from $accdbase.".$template." where item = 'label15'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$fontfamily);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
	}
	
	$query = "select * from $accdbase.".$template." where item = 'label16'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$fontfamily);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
	}
	
	$query = "select * from $accdbase.".$template." where item = 'label17'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$fontfamily);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
	}
	
	$query = "select * from $accdbase.".$template." where item = 'label18'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$fontfamily);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
	}
	
	$query = "select * from $accdbase.".$template." where item = 'label19'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$fontfamily);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
	}
	
	$query = "select * from $accdbase.".$template." where item = 'label20'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$fontfamily);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
	}
	
	
	
	$query = "select * from $accdbase.".$template." where item = 'docdate'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$fontfamily);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->Cell($cellwidth,$cellheight,$dt,$border,$nextpos,$align,$fill);
	}
	$query = "select * from $accdbase.".$template." where item = 'ref2'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$fontfamily);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->Cell($cellwidth,$cellheight,$xref,$border,$nextpos,$align,$fill);
	}
	
	$query = "select * from $accdbase.".$template." where item = 'gstabn'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$fontfamily);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->Cell($cellwidth,$cellheight,$abn,$border,$nextpos,$align,$fill);
	}
	

	$query = "select * from $accdbase.".$template." where item = 'gridtitle'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$fontfamily);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		$gtw = explode(',',$gridwidths);
		$galign = explode(',',$align);
		$gt = explode(',',$content);
		$gcount = count($gt);
		
		for ($n = 0; $n <= $gcount; $n++) {
			$pdf->Cell($gtw[$n],$cellheight,$gt[$n],$border,$nextpos,$galign[$n],$fill);
		}
	}
	
	
	$query = "select * from $accdbase.".$template." where item = 'griddetail'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	$gtw = explode(',',$gridwidths);
	$gcount = count($gtw);
	$xpos = $xcoord;
	$ypos = $ycoord;
	if ($include == 'Y') {
		$ft = explode(',',$fontfamily);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		$galign = explode(',',$align);
		$gformat = explode(',',$content);
		
		$maxypos = $ycoord;
	
		for ($i = $currentline; $i < count($linedetails); $i++) {
			$row = $linedetails[$i];
			for ($n = 0; $n <= $gcount; $n++) {
				
				if ($gformat[$n] == 'N') {
					$texte = number_format($row[$n],2);
				} else {
					$texte = $row[$n];
				}
					
				$length    = $pdf->GetStringWidth( $texte );
				$tailleTexte = $pdf->sizeOfText( $texte, $length );
				$pdf->SetXY( $xpos, $ypos-1);
				$pdf->MultiCell( $gtw[$n]-2, $cellheight , $texte, $border, $galign[$n]);
				if ( $maxypos < ($pdf->GetY()  ) ) {
					$maxypos = $pdf->GetY() ;
				}
				$xpos += $gtw[$n];		
				
			} //for
			$xpos = $xcoord;
			$ypos = $maxypos+ 2;
			
			if ($ypos > 220) {
				$lastpage = 'N';
				$currentline = $i;
				// add new page
				break;
			} else {
				$lastpage = 'Y';
			} //if $ypos
		} // foreach
	} //if included
	
	// draw boxes around grid details
	$nextx = $xcoord;
	for ($b = 0; $b < $gcount; $b++) {
		if ($ypos < 220) {
			$boxlength = 124;
		} else {
			$boxlength = $ypos-$ycoord-1;
		}
		$pdf->SetXY($nextx,$ycoord-1);
		$pdf->Cell($gtw[$b],$boxlength,'',1);	
		$nextx += $gtw[$b];
	}	// for
		
	if ($lastpage == 'N') {
		$pdf->AddPage();	
	}		

} // while	



$query = "select * from $accdbase.".$template." where item = 'footmessage'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);
if ($include == 'Y') {
	$ft = explode(',',$fontfamily);
	$fontname = $ft[0];
	$attrib = $ft[1];
	$ftsize = $ft[2];
	$pdf->SetXY($xcoord,$ycoord);
	$pdf->SetFont($fontname,$attrib,$ftsize);
	
	$pdf->MultiCell($cellwidth,$cellheight,$content);
}

$query = "select * from $accdbase.".$template." where item = 'footbox1'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);
if ($include == 'Y') {
	$pdf->SetXY($xcoord,$ycoord);
	$pdf->Cell($cellwidth,$cellheight,'',$border,$nextpos,$align,$fill);
}

$query = "select * from $accdbase.".$template." where item = 'footlabel1'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);
if ($include == 'Y') {
	$ft = explode(',',$fontfamily);
	$fontname = $ft[0];
	$attrib = $ft[1];
	$ftsize = $ft[2];
	$pdf->SetXY($xcoord,$ycoord);
	$pdf->SetFont($fontname,$attrib,$ftsize);
	
	$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
}

$query = "select * from $accdbase.".$template." where item = 'footlabel2'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);
if ($include == 'Y') {
	$ft = explode(',',$fontfamily);
	$fontname = $ft[0];
	$attrib = $ft[1];
	$ftsize = $ft[2];
	$pdf->SetXY($xcoord,$ycoord);
	$pdf->SetFont($fontname,$attrib,$ftsize);
	
	$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
}

$query = "select * from $accdbase.".$template." where item = 'footlabel3'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);
if ($include == 'Y') {
	$ft = explode(',',$fontfamily);
	$fontname = $ft[0];
	$attrib = $ft[1];
	$ftsize = $ft[2];
	$pdf->SetXY($xcoord,$ycoord);
	$pdf->SetFont($fontname,$attrib,$ftsize);
	
	$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
}

$query = "select * from $accdbase.".$template." where item = 'footlabel4'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);
if ($include == 'Y') {
	$ft = explode(',',$fontfamily);
	$fontname = $ft[0];
	$attrib = $ft[1];
	$ftsize = $ft[2];
	$pdf->SetXY($xcoord,$ycoord);
	$pdf->SetFont($fontname,$attrib,$ftsize);
	
	$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
}

$query = "select * from $accdbase.".$template." where item = 'footlabel5'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);
if ($include == 'Y') {
	$ft = explode(',',$fontfamily);
	$fontname = $ft[0];
	$attrib = $ft[1];
	$ftsize = $ft[2];
	$pdf->SetXY($xcoord,$ycoord);
	$pdf->SetFont($fontname,$attrib,$ftsize);
	
	$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
}

$query = "select * from $accdbase.".$template." where item = 'footlabel6'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);
if ($include == 'Y') {
	$ft = explode(',',$fontfamily);
	$fontname = $ft[0];
	$attrib = $ft[1];
	$ftsize = $ft[2];
	$pdf->SetXY($xcoord,$ycoord);
	$pdf->SetFont($fontname,$attrib,$ftsize);
	
	$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
}

$query = "select * from $accdbase.".$template." where item = 'footlabel7'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);
if ($include == 'Y') {
	$ft = explode(',',$fontfamily);
	$fontname = $ft[0];
	$attrib = $ft[1];
	$ftsize = $ft[2];
	$pdf->SetXY($xcoord,$ycoord);
	$pdf->SetFont($fontname,$attrib,$ftsize);
	
	$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
}

$query = "select * from $accdbase.".$template." where item = 'footlabel8'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);
if ($include == 'Y') {
	$ft = explode(',',$fontfamily);
	$fontname = $ft[0];
	$attrib = $ft[1];
	$ftsize = $ft[2];
	$pdf->SetXY($xcoord,$ycoord);
	$pdf->SetFont($fontname,$attrib,$ftsize);
	
	$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
}

$query = "select * from $accdbase.".$template." where item = 'footlabel9'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);
if ($include == 'Y') {
	$ft = explode(',',$fontfamily);
	$fontname = $ft[0];
	$attrib = $ft[1];
	$ftsize = $ft[2];
	$pdf->SetXY($xcoord,$ycoord);
	$pdf->SetFont($fontname,$attrib,$ftsize);
	
	$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
}



$query = "select * from $accdbase.".$template." where item = 'totval'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);
if ($include == 'Y') {
	$ft = explode(',',$fontfamily);
	$fontname = $ft[0];
	$attrib = $ft[1];
	$ftsize = $ft[2];
	$pdf->SetXY($xcoord,$ycoord);
	$pdf->SetFont($fontname,$attrib,$ftsize);
	
	$pdf->Cell($cellwidth,$cellheight,number_format($totalvalue,2),$border,$nextpos,$align,$fill);
}

$query = "select * from $accdbase.".$template." where item = 'tottax'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);
if ($include == 'Y') {
	$ft = explode(',',$fontfamily);
	$fontname = $ft[0];
	$attrib = $ft[1];
	$ftsize = $ft[2];
	$pdf->SetXY($xcoord,$ycoord);
	$pdf->SetFont($fontname,$attrib,$ftsize);
	
	$pdf->Cell($cellwidth,$cellheight,number_format($totaltax,2),$border,$nextpos,$align,$fill);
}

$query = "select * from $accdbase.".$template." where item = 'totdue'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);
if ($include == 'Y') {
	$ft = explode(',',$fontfamily);
	$fontname = $ft[0];
	$attrib = $ft[1];
	$ftsize = $ft[2];
	$pdf->SetXY($xcoord,$ycoord);
	$pdf->SetFont($fontname,$attrib,$ftsize);
	
	$pdf->Cell($cellwidth,$cellheight,number_format($totaldue,2),$border,$nextpos,$align,$fill);
}


if ($worknotes !="") {
	$pdf->AddPage();	

	$query = "select * from $accdbase.".$template." where item = 'watermark'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$fontfamily);
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

	$query = "select * from $accdbase.".$template." where item = 'image'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$pdf->Image($content,$xcoord,$ycoord,$cellwidth,$cellheight,'jpg');
	}	
	
	$query = "select * from $accdbase.".$template." where item = 'box1'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->Cell($cellwidth,$cellheight,'',$border,$nextpos,$align,$fill);
	}
	
	
	$query = "select * from $accdbase.".$template." where item = 'rbox1'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->RoundedRect($xcoord, $ycoord, $cellwidth, $cellheight, 2.5, 'D');
	}
	
	$query = "select * from $accdbase.".$template." where item = 'rbox2'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->RoundedRect($xcoord, $ycoord, $cellwidth, $cellheight, 2.5, 'D');
	}
	
	$query = "select * from $accdbase.".$template." where item = 'rbox3'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->RoundedRect($xcoord, $ycoord, $cellwidth, $cellheight, 2.5, 'D');
	}
	
	$query = "select * from $accdbase.".$template." where item = 'rbox4'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->RoundedRect($xcoord, $ycoord, $cellwidth, $cellheight, 2.5, 'D');
	}
	
	$query = "select * from $accdbase.".$template." where item = 'rbox5'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->RoundedRect($xcoord, $ycoord, $cellwidth, $cellheight, 2.5, 'D');
	}
	
	$query = "select * from $accdbase.".$template." where item = 'rbox6'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->RoundedRect($xcoord, $ycoord, $cellwidth, $cellheight, 2.5, 'D');
	}
	
	$query = "select * from $accdbase.".$template." where item = 'rbox7'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->RoundedRect($xcoord, $ycoord, $cellwidth, $cellheight, 2.5, 'D');
	}
	
	$query = "select * from $accdbase.".$template." where item = 'rbox8'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->RoundedRect($xcoord, $ycoord, $cellwidth, $cellheight, 2.5, 'D');
	}
	
	$query = "select * from $accdbase.".$template." where item = 'rbox9'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->RoundedRect($xcoord, $ycoord, $cellwidth, $cellheight, 2.5, 'D');
	}
	
	$query = "select * from $accdbase.".$template." where item = 'fromname'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$fontfamily);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->Cell($cellwidth,$cellheight,$trade_name,$border,$nextpos,$align,$fill);
	}
	
	$query = "select * from $accdbase.".$template." where item = 'fromaddress'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$fontfamily);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->MultiCell($cellwidth,$cellheight,$fromadd);
	}
	
	$query = "select * from $accdbase.".$template." where item = 'toaddress'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$fontfamily);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->MultiCell($cellwidth,$cellheight,$toadd);
	}
	

	
	$query = "select * from $accdbase.".$template." where item = 'header1'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$fontfamily);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
	}
	
	$query = "select * from $accdbase.".$template." where item = 'header2'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$fontfamily);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
	}
	
	$query = "select * from $accdbase.".$template." where item = 'ref1'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$fontfamily);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->Cell($cellwidth,$cellheight,$reference,$border,$nextpos,$align,$fill);
	}

	$query = "select * from $accdbase.".$template." where item = 'notes'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$fontfamily);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->MultiCell($cellwidth,$cellheight,$notes);
	}
	
	$query = "select * from $accdbase.".$template." where item = 'label1'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$fontfamily);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
	}
	
	$query = "select * from $accdbase.".$template." where item = 'label2'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$fontfamily);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
	}
	
	$query = "select * from $accdbase.".$template." where item = 'label3'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$fontfamily);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
	}
	
	$query = "select * from $accdbase.".$template." where item = 'label4'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$fontfamily);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
	}
	
	$query = "select * from $accdbase.".$template." where item = 'label5'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$fontfamily);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
	}
	
	$query = "select * from $accdbase.".$template." where item = 'label6'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$fontfamily);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
	}
	
	$query = "select * from $accdbase.".$template." where item = 'label7'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$fontfamily);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
	}
	
	$query = "select * from $accdbase.".$template." where item = 'label8'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$fontfamily);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
	}
	
	$query = "select * from $accdbase.".$template." where item = 'label9'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$fontfamily);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
	}
	
	$query = "select * from $accdbase.".$template." where item = 'label10'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$fontfamily);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
	}
	
	$query = "select * from $accdbase.".$template." where item = 'label11'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$fontfamily);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
	}
	
	$query = "select * from $accdbase.".$template." where item = 'label12'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$fontfamily);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
	}
	
	$query = "select * from $accdbase.".$template." where item = 'label13'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$fontfamily);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
	}
	
	$query = "select * from $accdbase.".$template." where item = 'label14'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$fontfamily);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
	}
	
	$query = "select * from $accdbase.".$template." where item = 'label15'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$fontfamily);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
	}
	
	$query = "select * from $accdbase.".$template." where item = 'label16'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$fontfamily);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
	}
	
	$query = "select * from $accdbase.".$template." where item = 'label17'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$fontfamily);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
	}
	
	$query = "select * from $accdbase.".$template." where item = 'label18'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$fontfamily);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
	}
	
	$query = "select * from $accdbase.".$template." where item = 'label19'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$fontfamily);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
	}
	
	$query = "select * from $accdbase.".$template." where item = 'label20'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$fontfamily);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
	}
	
	
	
	$query = "select * from $accdbase.".$template." where item = 'docdate'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$fontfamily);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->Cell($cellwidth,$cellheight,$dt,$border,$nextpos,$align,$fill);
	}
	$query = "select * from $accdbase.".$template." where item = 'ref2'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$fontfamily);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->Cell($cellwidth,$cellheight,$xref,$border,$nextpos,$align,$fill);
	}
	
	$query = "select * from $accdbase.".$template." where item = 'gstabn'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$fontfamily);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->Cell($cellwidth,$cellheight,$abn,$border,$nextpos,$align,$fill);
	}
	

	
	$query = "select * from $accdbase.".$template." where item = 'worknotesbox'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->Cell($cellwidth,$cellheight,'',$border,$nextpos,$align,$fill);
	}
	
	
	$query = "select * from $accdbase.".$template." where item = 'worknoteshead'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$fontfamily);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		for ($n = 0; $n <= $gcount; $n++) {
			$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
		}
	}	
	
	$query = "select * from $accdbase.".$template." where item = 'worknotes'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$fontfamily);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		
		$pdf->MultiCell($cellwidth,$cellheight,$worknotes);
	}
}
	

$pdf->Output();
?>