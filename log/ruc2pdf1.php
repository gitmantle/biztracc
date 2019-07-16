<?php
session_start();
$coyname = $_SESSION['s_coyname'];

$usersession = $_SESSION['usersession'];


/*
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
	
	$qm = "select sum(routes.private) as dprivate from routes,dockets where (routes.uid = dockets.routeid) and (dockets.truckbranch = '".$br."' or dockets.trailerbranch = '".$br."')";
	$rm = mysql_query($qm) or die(mysql_error().' '.$qm);
	$row = mysql_fetch_array($rm);
	extract($row);

	$qv = "select sum(ferry.private) as fprivate from ferry where (ferry.truckbranch = '".$br."' or ferry.trailerbranch = '".$br."')";
	$rv = mysql_query($qv) or die(mysql_error().' '.$qv);
	$row = mysql_fetch_array($rv);
	extract($row);
	
	$dist = $dprivate + $fprivate;
	$qx = "update ".$rucfile." set distance = ".$dist." where regno = '".$rno."'";
	$rx = mysql_query($qx) or die(mysql_error().' '.$qx);

}


define('FPDF_FONTPATH','../includes/font/');
require('../includes/fpdf/fpdf.php');

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


$pdf=new PDF("P","mm","A4");
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont("Arial","",10);
$pdf->SetLeftMargin(0);

$hfontname = "Arial";
$hattrib = "";
$hfontsize = 10;



//********************************************************************************************************************
function heading()
//********************************************************************************************************************
{
	$pdf->SetXY(20,13);
	$pdf->SetFont($hfontname,$hattrib,$hfontsize);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->Cell(35,3,"NZ TRANSPORT AGENCY",1,0,"L",1);
	$pdf->SetTextColor(0,0,0);
	
	//$pdf->Rect(10,15,194,35,"D");
	
	//$pdf->SetXY(16,15);
	//$pdf->SetFont($hfontname,$hattrib,7);
	//$pdf->MultiCell(160,4,"Protection in the event of death and/or trauma (Clean-up, Income ,Mortgage, Education, Retirement, Savings, Other) \n".$mriskprot,0,"L",0);
	//$pdf->Rect(10,50,194,35,"D");
	
}

//********************************************************************************************************************
function body()
//********************************************************************************************************************
{
	
	
	
	
	
}

//********************************************************************************************************************
function signing()
//********************************************************************************************************************
{
	
	
}

$lastpage = 'N';
$currentline = 0;

//while ($lastpage == 'N') {
	heading();
//	body();
//	signing();
//}
//footer();

$pdf->Output();

*/
?>