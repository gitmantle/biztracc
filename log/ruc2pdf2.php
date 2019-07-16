<?php
session_start();
$coyname = $_SESSION['s_coyname'];

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

$rucfile = 'ztmp'.$user_id.'_ruc';

$moduledb = $_SESSION['s_logdb'];
mysql_select_db($moduledb) or die(mysql_error());

$query = "drop table if exists ".$rucfile;
$result = mysql_query($query) or die(mysql_error());

$query = "create table ".$rucfile." (regno varchar(25) default '', branch char(4) default '', ruclicence varchar(25) default '',distance decimal(10,2),reason int default 0,method varchar(25) default '',type varchar(25) default '')  engine myisam";
$calc = mysql_query($query) or die(mysql_error().' '.$query);

$q = "select * from params";
$r = mysql_query($q) or die(mysql_error().' '.$q);
$row = mysql_fetch_array($r);
extract($row);
$custno = $offroad_custno;
$reason = $reason_code;
$mtd = $method;
$typ = $type;
$desc = $description;
$recs = $records;

$q = "select regno,cost_centre,ruclicence from vehicles";
$r = mysql_query($q) or die(mysql_error().' '.$q);
while ($row = mysql_fetch_array($r)) {
	extract($row);
	$qi = "insert into ".$rucfile." (regno,branch,ruclicence,reason,method,type) values (";
	$qi .= "'".$regno."',";
	$qi .= "'".substr($cost_centre,0,2)."',";
	$qi .= "'".$ruclicence."',";
	$qi .= $reason.",";
	$qi .= "'".$mtd."',";
	$qi .= "'".$typ."')";
	
	$ri = mysql_query($qi) or die(mysql_error().' '.$qi);
}

$q = "select regno,branch from ".$rucfile;
$r = mysql_query($q) or die(mysql_error().' '.$q);
while ($row = mysql_fetch_array($r)) {
	extract($row);
	$br = $branch;
	$rno = $regno;
	
	$qm = "select sum(routes.private) as dprivate from routes,dockets where (routes.uid = dockets.routeid) and (dockets.refund = 'N') and (dockets.truckbranch = '".$br."' or dockets.trailerbranch = '".$br."')";
	$rm = mysql_query($qm) or die(mysql_error().' '.$qm);
	$row = mysql_fetch_array($rm);
	extract($row);

	$qv = "select sum(ferry.private) as fprivate from ferry where (ferry.refund = 'N') and (ferry.truckbranch = '".$br."' or ferry.trailerbranch = '".$br."')";
	$rv = mysql_query($qv) or die(mysql_error().' '.$qv);
	$row = mysql_fetch_array($rv);
	extract($row);
	
	$dist = $dprivate + $fprivate;
	$qx = "update ".$rucfile." set distance = ".$dist." where regno = '".$rno."'";
	$rx = mysql_query($qx) or die(mysql_error().' '.$qx);

}

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

$newpage = 'Y';
$offset = 0;


while ($newpage == 'Y') {
	
	$pdf = new FPDF("L","mm","A4");
	$pdf->AliasNbPages();
	$pdf->AddPage();
	$pdf->SetFont('Arial','',10);
	$pdf->SetLeftMargin(0);
	
	$qruc = "select regno,ruclicence,distance,reason,method,type from ".$rucfile." limit ".$offset.",10";
	$rruc = mysql_query($qruc) or die(mysql_error().' '.$qruc);
	
	if (mysql_num_rows($rruc) > 0) {
		$img = "../images/NZTAlogo.jpg";
		$pdf->Image($img,10,7,52,0,'jpg');
			
		$pdf->SetXY(86,9);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','B',16);
		$pdf->Cell(160,9,"Road user charges application for refund for off-road travel",0,0,"L",1);
		$pdf->SetTextColor(0,0,0);
			
		$pdf->SetXY(269,9);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','B',16);
		$pdf->Cell(21,9,"RUCOR",0,0,"L",1);
		$pdf->SetTextColor(0,0,0);
		
		$pdf->SetLineWidth(0.7);
		$pdf->SetDrawcolor(175,189,33);
		$pdf->Line(10,20,286,20);
		
		$pdf->SetXY(10,23);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','B',11);
		$pdf->Cell(48,5,"Off-Road customer number",0,0,"L",1);
		$pdf->SetTextColor(0,0,0);
		
		$pdf->SetXY(62,24);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','',8);
		$pdf->Cell(14,3,"(see note 3)",0,0,"L",1);
		$pdf->SetTextColor(0,0,0);
		
		$pdf->SetXY(207,24);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(71,3,"Please tick if this is your first off-road refund claim",0,0,"L",1);
		$pdf->SetTextColor(0,0,0);
		
		$pdf->SetLineWidth(0.2);
		$pdf->SetDrawcolor(0,0,0);
		$pdf->Rect(280,22,6,6);
		
		$pdf->SetLineWidth(0.2);
		$pdf->SetDrawcolor(0,0,0);
		$pdf->Rect(10,27,70,10);
		
		$pdf->SetXY(12,29);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','B',13);
		$pdf->Cell(65,6,$custno,0,0,"L",1);
		$pdf->SetTextColor(0,0,0);
		
		$pdf->SetXY(83,27);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(65,3,"Company name/ Surname/ Customer name",0,0,"L",1);
		$pdf->SetTextColor(0,0,0);
		
		$pdf->SetLineWidth(0.2);
		$pdf->SetDrawcolor(0,0,0);
		$pdf->Rect(83,30,203,7);
		
		$pdf->SetLineWidth(0.7);
		$pdf->SetDrawcolor(175,189,33);
		$pdf->Line(10,39,286,39);
		
		$pdf->SetXY(85,30.5);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','B',13);
		$pdf->Cell(195,6,$coyname,0,0,"L",1);
		$pdf->SetTextColor(0,0,0);
		
		$pdf->SetXY(10,41);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell(56,3,"Please ensure all fields are completed.",0,0,"L",1);
		$pdf->SetTextColor(0,0,0);
		
		$pdf->SetXY(77,41);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','',10);
		$pdf->Cell(57,3,"Incomplete forms will not be processed.",0,0,"L",1);
		$pdf->SetTextColor(0,0,0);
		
		$pdf->SetLineWidth(0.2);
		$pdf->SetDrawcolor(0,0,0);
		$pdf->SetFillColor(216,224,242);
		$pdf->Rect(207,43,79,45,'DF');
		
		$pdf->SetXY(209,45);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','',10);
		$pdf->Cell(50,3,"Brief description of off-road travel.",0,0,"L",0);
		$pdf->SetTextColor(0,0,0);
		
		$pdf->SetXY(209,50);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','',10);
		$pdf->MultiCell(72,3,$desc,0,"L",0);
		$pdf->SetTextColor(0,0,0);
		
		$pdf->SetLineWidth(0.2);
		$pdf->SetDrawcolor(0,0,0);
		$pdf->SetFillColor(216,224,242);
		$pdf->Rect(207,89.5,79,46,'DF');
		
		$pdf->SetXY(209,91.5);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','',10);
		$pdf->MultiCell(72,3,"What records are you able to supply to validate this claim?",0,"L",0);
		$pdf->SetTextColor(0,0,0);
		
		$pdf->SetXY(209,99);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','',10);
		$pdf->MultiCell(72,3,$recs,0,"L",0);
		$pdf->SetTextColor(0,0,0);
		
		$pdf->SetXY(10,45);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(83,3,"Please read notes on the back of the form and print clearly.",0,0,"L",1);
		$pdf->SetTextColor(0,0,0);
		
		$pdf->SetXY(10,50);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell(29,3,"Registration plate",0,0,"L",1);
		$pdf->SetTextColor(0,0,0);
		
		$pdf->SetXY(43.5,50);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell(44,3,"RUC licence number",0,0,"L",1);
		$pdf->SetTextColor(0,0,0);
		
		$pdf->SetXY(89,50);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell(29,3,"Distance claimed",0,0,"L",1);
		$pdf->SetTextColor(0,0,0);
		
		$pdf->SetXY(121,50);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell(11,3,"Reason",0,0,"L",1);
		$pdf->SetTextColor(0,0,0);
		
		$pdf->SetXY(136,50);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell(23,3,"Method used to",0,0,"L",1);
		$pdf->SetTextColor(0,0,0);
		
		$pdf->SetXY(175,50);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell(18,3,"Specify type",0,0,"L",1);
		$pdf->SetTextColor(0,0,0);
		
		$pdf->SetXY(89,54);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','',8);
		$pdf->Cell(15,2,"(see note 4)",0,0,"L",1);
		$pdf->SetTextColor(0,0,0);
		
		$pdf->SetXY(121,54);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell(11,3,"code",0,0,"L",1);
		$pdf->SetTextColor(0,0,0);
		
		$pdf->SetXY(121,58);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','',8);
		$pdf->Cell(11,3,"(note 5)",0,0,"L",1);
		$pdf->SetTextColor(0,0,0);
		
		$pdf->SetXY(136,54);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell(23,3,"record distance",0,0,"L",1);
		$pdf->SetTextColor(0,0,0);
		
		$pdf->SetXY(136,58);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell(23,3,"claimed",0,0,"L",1);
		$pdf->SetTextColor(0,0,0);
		
		$pdf->SetXY(175,54);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','',8);
		$pdf->Cell(18,3,"(see note 6)",0,0,"L",1);
		$pdf->SetTextColor(0,0,0);
		
		$x = 10;
		$y = 62;
		
		while ($row = mysql_fetch_array($rruc)) {
			extract($row);
			
			$pdf->SetLineWidth(0.2);
			$pdf->SetDrawcolor(0,0,0);
			$pdf->SetFillColor(216,224,242);
			$pdf->Rect($x,$y,30,6,'DF');
			
			$pdf->SetXY($x+2,$y+1.5);
			$pdf->SetTextColor(0,0,0);
			$pdf->SetFillColor(255,255,255);
			$pdf->SetFont('Arial','',10);
			$pdf->Cell(26,3,$regno,0,0,"L",0);
			$pdf->SetTextColor(0,0,0);
			
			$pdf->SetLineWidth(0.2);
			$pdf->SetDrawcolor(0,0,0);
			$pdf->SetFillColor(216,224,242);
			$x = $x + 33;
			$pdf->Rect($x,$y,44,6,'DF');
			
			$pdf->SetXY($x+2,$y+1.5);
			$pdf->SetTextColor(0,0,0);
			$pdf->SetFillColor(255,255,255);
			$pdf->SetFont('Arial','',10);
			$pdf->Cell(40,3,$ruclicence,0,0,"L",0);
			$pdf->SetTextColor(0,0,0);
		
			$pdf->SetLineWidth(0.2);
			$pdf->SetDrawcolor(0,0,0);
			$pdf->SetFillColor(216,224,242);
			$x = $x + 47;
			$pdf->Rect($x,$y,29,6,'DF');
		
			$pdf->SetXY($x+2,$y+1.5);
			$pdf->SetTextColor(0,0,0);
			$pdf->SetFillColor(255,255,255);
			$pdf->SetFont('Arial','',10);
			$pdf->Cell(29,3,$distance,0,0,"L",0);
			$pdf->SetTextColor(0,0,0);
		
			$pdf->SetLineWidth(0.2);
			$pdf->SetDrawcolor(0,0,0);
			$pdf->SetFillColor(216,224,242);
			$x = $x + 33;
			$pdf->Rect($x,$y,7,6,'DF');
		
			$pdf->SetXY($x+1,$y+1.5);
			$pdf->SetTextColor(0,0,0);
			$pdf->SetFillColor(255,255,255);
			$pdf->SetFont('Arial','',10);
			$pdf->Cell(4,3,$reason,0,0,"L",0);
			$pdf->SetTextColor(0,0,0);
		
			$pdf->SetLineWidth(0.2);
			$pdf->SetDrawcolor(0,0,0);
			$pdf->SetFillColor(216,224,242);
			$x = $x +10;
			$pdf->Rect($x,$y,32,6,'DF');
		
			$pdf->SetXY($x+2,$y+1.5);
			$pdf->SetTextColor(0,0,0);
			$pdf->SetFillColor(255,255,255);
			$pdf->SetFont('Arial','',10);
			$pdf->Cell(28,3,$method,0,0,"L",0);
			$pdf->SetTextColor(0,0,0);
		
			$pdf->SetLineWidth(0.2);
			$pdf->SetDrawcolor(0,0,0);
			$pdf->SetFillColor(216,224,242);
			$x = $x +34;
			$pdf->Rect($x,$y,38,6,'DF');
			
			$pdf->SetXY($x+2,$y+1.5);
			$pdf->SetTextColor(0,0,0);
			$pdf->SetFillColor(255,255,255);
			$pdf->SetFont('Arial','',10);
			$pdf->Cell(34,3,$type,0,0,"L",0);
			$pdf->SetTextColor(0,0,0);
		
			$x = 10;
			$y = $y + 7.5;
		
		}
		$offset = $offset + 10;
	} else {
		$newpage = 'Y';
	}
}


$pdf->Output();
?>
