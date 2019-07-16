<?php
session_start();
$icid = $_REQUEST['uid'];

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

$incfile = 'ztmp'.$user_id.'_hs';

$moduledb = $_SESSION['h_sdb'];
mysql_select_db($moduledb) or die(mysql_error());

$q = "select incid,subid,coyid from ".$incfile." where uid = ".$icid;
$r = mysql_query($q) or die(mysql_error());
$row = mysql_fetch_array($r);
extract($row);

$admindb = $_SESSION['s_admindb'];
mysql_select_db($admindb) or die(mysql_error());

$q = "select coyemail,coyphone,coyname from companies where coyid = ".$coyid;
$r = mysql_query($q) or die(mysql_error());
$row = mysql_fetch_array($r);
extract($row);
$cphone = $coyphone;
$cemail = $coyemail;

$moduledb = 'log'.$subid.'_'.$coyid;
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

	
	$qinc = "select * from incidents where uid = ".$incid;
	$rinc = mysql_query($qinc) or die(mysql_error().' '.$qinc);
	$row = mysql_fetch_array($rinc);
	extract($row);
	$dt = explode('-',$date_entered);
	$y = $dt[0];
	$m = $dt[1];
	$d = $dt[2];
	$dt_entered = $d.'/'.$m.'/'.$y;
	$dt = explode('-',$date_incident);
	$y = $dt[0];
	$m = $dt[1];
	$d = $dt[2];
	$dt_incident = $d.'/'.$m.'/'.$y;
	
	$risk = 0;
	$h = 0;
	$d = 0;
	$c = 0;
	
	switch ($harm_people) {
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
	
	switch ($damage_property) {
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
	
	switch ($reocurr) {
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
	}
	
	
	$hd = max($h,$d);
	$risk = $hd * $c;
	
	
		$pdf->AddPage();
		$pdf->SetFont('Arial','',10);
		$pdf->SetLeftMargin(0);
			
		$pdf->SetXY(20,9);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','B',16);
		$pdf->Cell(280,9,$coyname,0,0,"L",1);
		$pdf->SetTextColor(0,0,0);
			
		$pdf->SetXY(220,9);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','',16);
		$pdf->Cell(20,9,"Risk Score",0,0,"L",1);
		$pdf->SetTextColor(0,0,0);

		$pdf->SetXY(260,9);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','',48);
		$pdf->Cell(15,20,$risk,0,0,"L",1);
		$pdf->SetTextColor(0,0,0);

		$pdf->SetXY(20,19);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','B',16);
		$pdf->Cell(230,9,"INCIDENT/NEAR MISS REPORT FORM",0,0,"L",1);
		$pdf->SetTextColor(0,0,0);
			
		$pdf->SetXY(20,30);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','B',16);
		$pdf->Cell(21,4,"Incident # ".$incid.' - '.$ref,0,0,"L",1);
		$pdf->SetTextColor(0,0,0);
		
		$pdf->SetXY(120,30);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','',12);
		$pdf->Cell(21,4,"Date form recieved: ..........................",0,0,"L",1);
		$pdf->SetTextColor(0,0,0);

		$pdf->SetXY(220,30);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','',12);
		$pdf->Cell(21,4,"Date form entered: ".$dt_entered,0,0,"L",1);
		$pdf->SetTextColor(0,0,0);

		$pdf->SetLineWidth(0.7);
		$pdf->SetDrawcolor(175,189,33);
		$pdf->Line(20,35,280,35);
		
		$x = 20;
		$y = 40;
		
		$pdf->SetXY($x,$y);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','',12);
		$pdf->Cell(40,4,"Date of Incident: ".$dt_incident,0,0,"L",1);
		$pdf->SetTextColor(0,0,0);
		
		$pdf->SetXY($x+90,$y);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','',12);
		$pdf->Cell(40,4,"Time of Incident: ".$time_incident,0,0,"L",1);
		$pdf->SetTextColor(0,0,0);
		
		$pdf->SetXY($x+190,$y);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','',12);
		$pdf->Cell(60,4,"Lost Time Incident: ".$LTI,0,0,"L",1);
		$pdf->SetTextColor(0,0,0);
		
		$y = $y + 7;
		
		$pdf->SetXY($x,$y);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','',12);
		$pdf->Cell(80,4,"Client: ".$client,0,0,"L",1);
		$pdf->SetTextColor(0,0,0);
		
		$pdf->SetXY($x+90,$y);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','',12);
		$pdf->Cell(80,4,"Sub Contractor: ".$sub_contractor,0,0,"L",1);
		$pdf->SetTextColor(0,0,0);
		
		$pdf->SetXY($x+190,$y);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','',12);
		$pdf->Cell(60,4,"Crew: ".$crew,0,0,"L",1);
		$pdf->SetTextColor(0,0,0);
		
		$y = $y + 7;
		
		$pdf->SetXY($x,$y);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','',12);
		$pdf->Cell(80,4,"Truck: ".$truckno,0,0,"L",1);
		$pdf->SetTextColor(0,0,0);
		
		$pdf->SetXY($x+90,$y);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','',12);
		$pdf->Cell(80,4,"Trailer: ".$trailerno,0,0,"L",1);
		$pdf->SetTextColor(0,0,0);
		
		$pdf->SetXY($x+190,$y);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','',12);
		$pdf->Cell(60,4,"Compiled by: ".$compiler,0,0,"L",1);
		$pdf->SetTextColor(0,0,0);
		
		$y = $y + 7;
		
		$pdf->SetXY($x,$y);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','',12);
		$pdf->Cell(80,4,"Incident Type: ".$incident_type,0,0,"L",1);
		$pdf->SetTextColor(0,0,0);
		
		$pdf->SetXY($x+90,$y);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','',12);
		$pdf->Cell(80,4,"Forest/Compartment: ".$forest.'/'.$compartment,0,0,"L",1);
		$pdf->SetTextColor(0,0,0);
		
		
		$y = $y + 7;
		
		$pdf->SetXY($x,$y);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','',12);
		$pdf->Cell(80,4,"Details of Incident:",0,0,"L",1);
		$pdf->SetTextColor(0,0,0);
		
		$pdf->SetXY($x+90,$y);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','',12);
		$pdf->Cell(90,4,"Road: ".$road,0,0,"L",1);
		$pdf->SetTextColor(0,0,0);
		
		$y = $y + 6;
				
		$pdf->SetLineWidth(0.2);
		$pdf->SetDrawcolor(0,0,0);
		$pdf->Rect($x,$y,260,38);
		
		$y = $y + 2;

		$pdf->SetXY($x+5,$y);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','',12);
		$pdf->MultiCell(250,4,$details,0,"L",0);
		$pdf->SetTextColor(0,0,0);
		
		$y = $y + 40;

		$pdf->SetXY($x,$y);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','',12);
		$pdf->Cell(80,4,"What happened or could have happened:",0,0,"L",1);
		$pdf->SetTextColor(0,0,0);
		
		$y = $y + 7;
		
		$pdf->SetXY($x,$y);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','',12);
		$pdf->Cell(80,4,"Harm to People: ".$harm_people,0,0,"L",1);
		$pdf->SetTextColor(0,0,0);
		
		$pdf->SetXY($x+90,$y);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','',12);
		$pdf->Cell(80,4,"Damage to Property: ".$damage_property,0,0,"L",1);
		$pdf->SetTextColor(0,0,0);
		
		$pdf->SetXY($x+190,$y);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','',12);
		$pdf->Cell(60,4,"Likelyhood of re-occurence: ".$reocurr,0,0,"L",1);
		$pdf->SetTextColor(0,0,0);
		
		$y = $y + 7;
		
		$pdf->SetLineWidth(0.7);
		$pdf->SetDrawcolor(175,189,33);
		$pdf->Line($x,$y,280,$y);
		
		$y = $y + 3;

		$pdf->SetXY($x,$y);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','B',12);
		$pdf->Cell(80,4,"Environmental conditions (if applicable):",0,0,"L",1);
		$pdf->SetTextColor(0,0,0);
		
		$y = $y + 7;
		
		$pdf->SetXY($x,$y);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','',12);
		$pdf->Cell(80,4,"Terrain: ".$terrain,0,0,"L",1);
		$pdf->SetTextColor(0,0,0);
		
		$pdf->SetXY($x+70,$y);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','',12);
		$pdf->Cell(80,4,"Weather: ".$weather,0,0,"L",1);
		$pdf->SetTextColor(0,0,0);
		
		$pdf->SetXY($x+130,$y);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','',12);
		$pdf->Cell(60,4,"Temperature: ".$temperature,0,0,"L",1);
		$pdf->SetTextColor(0,0,0);
		
		$pdf->SetXY($x+190,$y);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','',12);
		$pdf->Cell(60,4,"Wind: ".$wind,0,0,"L",1);
		$pdf->SetTextColor(0,0,0);
		
		$y = $y + 7;
		
		$pdf->SetLineWidth(0.7);
		$pdf->SetDrawcolor(175,189,33);
		$pdf->Line($x,$y,280,$y);
		
		$y = $y + 3;

		$pdf->SetXY($x,$y);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','B',12);
		$pdf->Cell(80,4,"Causes of Incident:",0,0,"L",1);
		$pdf->SetTextColor(0,0,0);
		
		$y = $y + 7;
				
		$pdf->SetXY($x,$y);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','B',12);
		$pdf->Cell(80,4,"Immediate Causes:",0,0,"L",1);
		$pdf->SetTextColor(0,0,0);
		
		$y = $y + 6;
		
		$pdf->SetLineWidth(0.2);
		$pdf->SetDrawcolor(0,0,0);
		$pdf->Rect($x,$y,260,20);
		
		$y = $y + 2;
		
		$pdf->SetXY($x+5,$y);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','',12);
		$pdf->MultiCell(250,4,$immediate,0,"L",0);
		$pdf->SetTextColor(0,0,0);
		
		$pdf->AddPage();
		
		$pdf->SetXY(20,9);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','B',16);
		$pdf->Cell(280,9,$coyname.' - Incident # '.$incid.' - '.$ref,0,0,"C",1);
		$pdf->SetTextColor(0,0,0);
			
		
		$x = 20;
		$y = 20;
		
		$pdf->SetXY($x,$y);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','B',12);
		$pdf->Cell(80,4,"Basic Causes:",0,0,"L",1);
		$pdf->SetTextColor(0,0,0);
		
		$y = $y + 7;
		
		$pdf->SetXY($x,$y);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','',12);
		$pdf->Cell(180,4,$basic1,0,0,"L",1);
		$pdf->SetTextColor(0,0,0);
		
		$y = $y + 6;
		
		$pdf->SetXY($x,$y);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','',12);
		$pdf->Cell(180,4,$basic2,0,0,"L",1);
		$pdf->SetTextColor(0,0,0);
		
		$y = $y + 6;
		
		$pdf->SetXY($x,$y);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','',12);
		$pdf->Cell(180,4,$basic3,0,0,"L",1);
		$pdf->SetTextColor(0,0,0);
		
		$y = $y + 6;
		
		$pdf->SetXY($x,$y);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','',12);
		$pdf->Cell(180,4,$basic4,0,0,"L",1);
		$pdf->SetTextColor(0,0,0);
		
		$y = $y + 6;
		
		$pdf->SetXY($x,$y);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','',12);
		$pdf->Cell(180,4,$basic5,0,0,"L",1);
		$pdf->SetTextColor(0,0,0);
		
		$y = $y + 6;
		
		$pdf->SetXY($x,$y);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','',12);
		$pdf->Cell(180,4,$basic6,0,0,"L",1);
		$pdf->SetTextColor(0,0,0);
		
		$y = $y + 6;
		
		$pdf->SetXY($x,$y);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','',12);
		$pdf->Cell(180,4,$basic7,0,0,"L",1);
		$pdf->SetTextColor(0,0,0);
		
		$y = $y + 6;
		
		$pdf->SetXY($x,$y);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','',12);
		$pdf->Cell(180,4,$basic8,0,0,"L",1);
		$pdf->SetTextColor(0,0,0);
		
		$y = $y + 7;
		
		$pdf->SetXY($x,$y);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','B',12);
		$pdf->Cell(80,4,"Hazards that contributed towards the incident:",0,0,"L",1);
		$pdf->SetTextColor(0,0,0);
		
		$y = $y + 6;
		
		$pdf->SetLineWidth(0.2);
		$pdf->SetDrawcolor(0,0,0);
		$pdf->Rect($x,$y,260,20);
		
		$y = $y + 2;
		
		$pdf->SetXY($x+5,$y);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','',12);
		$pdf->MultiCell(250,4,$hazards,0,"L",0);
		$pdf->SetTextColor(0,0,0);
		
		$y = $y + 23;
		
		$pdf->SetLineWidth(0.7);
		$pdf->SetDrawcolor(175,189,33);
		$pdf->Line($x,$y,280,$y);
		
		$y = $y + 3;
		
		$pdf->SetXY($x,$y);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','B',12);
		$pdf->Cell(80,4,"Damage to Property:",0,0,"L",1);
		$pdf->SetTextColor(0,0,0);
		
		$y = $y + 7;
		
		$pdf->SetXY($x,$y);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','B',12);
		$pdf->Cell(90,4,"Property:",0,0,"L",1);
		$pdf->SetTextColor(0,0,0);
			
		$pdf->SetXY($x+110,$y);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','B',12);
		$pdf->Cell(90,4,"Damage:",0,0,"L",1);
		$pdf->SetTextColor(0,0,0);
		
		$qdam = "select property,damage from incdamage where incident_id = ".$incid;
		$rdam = mysql_query($qdam) or die (mysql_error());
		while ($row = mysql_fetch_array($rdam)) {
			extract($row);
			
			$y = $y + 6;
			
			$pdf->SetXY($x,$y);
			$pdf->SetTextColor(0,0,0);
			$pdf->SetFillColor(255,255,255);
			$pdf->SetFont('Arial','',12);
			$pdf->Cell(90,4,$property,0,0,"L",1);
			$pdf->SetTextColor(0,0,0);
			
			$pdf->SetXY($x+110,$y);
			$pdf->SetTextColor(0,0,0);
			$pdf->SetFillColor(255,255,255);
			$pdf->SetFont('Arial','',12);
			$pdf->Cell(90,4,$damage,0,0,"L",1);
			$pdf->SetTextColor(0,0,0);
		}
		
		$pdf->AddPage();
		
		$pdf->SetXY(20,9);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','B',16);
		$pdf->Cell(280,9,$coyname.' - Incident # '.$incid.' - '.$ref,0,0,"C",1);
		$pdf->SetTextColor(0,0,0);
			
		
		$x = 20;
		$y = 20;
		
		$pdf->SetXY($x,$y);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','B',12);
		$pdf->Cell(80,4,"People Involved in Incident:",0,0,"L",1);
		$pdf->SetTextColor(0,0,0);
		
		$y = $y + 7;
		
		$pdf->SetXY($x,$y);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell(50,4,"Name:",0,0,"L",1);
		$pdf->SetTextColor(0,0,0);
			
		$pdf->SetXY($x+50,$y);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell(30,4,"Involvment:",0,0,"L",1);
		$pdf->SetTextColor(0,0,0);
		
		$pdf->SetXY($x+80,$y);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell(25,4,"Shift:",0,0,"L",1);
		$pdf->SetTextColor(0,0,0);
			
		$pdf->SetXY($x+105,$y);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell(30,4,"Start Time:",0,0,"L",1);
		$pdf->SetTextColor(0,0,0);
		
		$pdf->SetXY($x+135,$y);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell(50,4,"Operation:",0,0,"L",1);
		$pdf->SetTextColor(0,0,0);
		
		$pdf->SetXY($x+175,$y);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell(50,4,"Industry Experience:",0,0,"L",1);
		$pdf->SetTextColor(0,0,0);
			
		$pdf->SetXY($x+215,$y);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell(40,4,"Job Experience:",0,0,"L",1);
		$pdf->SetTextColor(0,0,0);
		
		$qdam = "select name,involvment,shift,starttime,operation,qualifications,indexpy,indexpm,indexpd,jobexpy,jobexpm,jobexpd from incpeople where incident_id = ".$incid;
		$rdam = mysql_query($qdam) or die (mysql_error());
		while ($row = mysql_fetch_array($rdam)) {
			extract($row);

			$y = $y + 5;
			
			$pdf->SetXY($x,$y);
			$pdf->SetTextColor(0,0,0);
			$pdf->SetFillColor(255,255,255);
			$pdf->SetFont('Arial','',10);
			$pdf->Cell(50,4,$name,0,0,"L",1);
			$pdf->SetTextColor(0,0,0);
				
			$pdf->SetXY($x+50,$y);
			$pdf->SetTextColor(0,0,0);
			$pdf->SetFillColor(255,255,255);
			$pdf->SetFont('Arial','',10);
			$pdf->Cell(30,4,$involvment,0,0,"L",1);
			$pdf->SetTextColor(0,0,0);
			
			$pdf->SetXY($x+80,$y);
			$pdf->SetTextColor(0,0,0);
			$pdf->SetFillColor(255,255,255);
			$pdf->SetFont('Arial','',10);
			$pdf->Cell(25,4,$shift,0,0,"L",1);
			$pdf->SetTextColor(0,0,0);
				
			$pdf->SetXY($x+105,$y);
			$pdf->SetTextColor(0,0,0);
			$pdf->SetFillColor(255,255,255);
			$pdf->SetFont('Arial','',10);
			$pdf->Cell(30,4,$starttime,0,0,"L",1);
			$pdf->SetTextColor(0,0,0);
			
			$pdf->SetXY($x+135,$y);
			$pdf->SetTextColor(0,0,0);
			$pdf->SetFillColor(255,255,255);
			$pdf->SetFont('Arial','',10);
			$pdf->Cell(50,4,$operation,0,0,"L",1);
			$pdf->SetTextColor(0,0,0);
			
			$pdf->SetXY($x+175,$y);
			$pdf->SetTextColor(0,0,0);
			$pdf->SetFillColor(255,255,255);
			$pdf->SetFont('Arial','',10);
			$pdf->Cell(50,4,$indexpy.' yrs '.$indexpm.' mths '.$indexpd.' days',0,0,"L",1);
			$pdf->SetTextColor(0,0,0);
				
			$pdf->SetXY($x+215,$y);
			$pdf->SetTextColor(0,0,0);
			$pdf->SetFillColor(255,255,255);
			$pdf->SetFont('Arial','',10);
			$pdf->Cell(40,4,$jobexpy.' yrs '.$jobexpm.' mths '.$jobexpd.' days',0,0,"L",1);
			$pdf->SetTextColor(0,0,0);
			
			$y = $y + 5;
			
			$pdf->SetXY($x+5,$y);
			$pdf->SetTextColor(0,0,0);
			$pdf->SetFillColor(255,255,255);
			$pdf->SetFont('Arial','B',10);
			$pdf->Cell(50,4,"Qualifications:",0,0,"L",1);
			$pdf->SetTextColor(0,0,0);
			
			$pdf->SetLineWidth(0.2);
			$pdf->SetDrawcolor(0,0,0);
			$pdf->Rect($x+40,$y,220,10);
			
			$y = $y + 1;
			
			$pdf->SetXY($x+42,$y);
			$pdf->SetTextColor(0,0,0);
			$pdf->SetFillColor(255,255,255);
			$pdf->SetFont('Arial','',10);
			$pdf->MultiCell(210,4,$qualifications,0,"L",0);
			$pdf->SetTextColor(0,0,0);
			
			$y = $y + 13;

		}

		$pdf->AddPage();
		
		$pdf->SetXY(20,9);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','B',16);
		$pdf->Cell(280,9,$coyname.' - Incident # '.$incid.' - '.$ref,0,0,"C",1);
		$pdf->SetTextColor(0,0,0);
			
		
		$x = 20;
		$y = 20;
		
		$pdf->SetXY($x,$y);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','B',12);
		$pdf->Cell(80,4,"People Injured in Incident:",0,0,"L",1);
		$pdf->SetTextColor(0,0,0);
		
		$y = $y + 7;

		$pdf->SetXY($x,$y);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell(50,4,"Name:",0,0,"L",1);
		$pdf->SetTextColor(0,0,0);
			
		$pdf->SetXY($x+50,$y);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell(30,4,"Severity:",0,0,"L",1);
		$pdf->SetTextColor(0,0,0);
		
		$pdf->SetXY($x+90,$y);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell(25,4,"Legal:",0,0,"L",1);
		$pdf->SetTextColor(0,0,0);
			
		$pdf->SetXY($x+125,$y);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell(30,4,"Days Lost:",0,0,"L",1);
		$pdf->SetTextColor(0,0,0);
		
		$pdf->SetXY($x+160,$y);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell(50,4,"Injury:",0,0,"L",1);
		$pdf->SetTextColor(0,0,0);
		
		$pdf->SetXY($x+220,$y);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell(50,4,"Part of Body:",0,0,"L",1);
		$pdf->SetTextColor(0,0,0);
			
	
		$qdam = "select name,severity,legal,dayslost,injury1,body1,injury2,body2,injury3,body3,injury4,body4,injury5,body5,injury6,body6,treatment from incinjuries where incident_id = ".$incid;
		$rdam = mysql_query($qdam) or die (mysql_error());
		while ($row = mysql_fetch_array($rdam)) {
			extract($row);

			$y = $y + 5;

			$pdf->SetXY($x,$y);
			$pdf->SetTextColor(0,0,0);
			$pdf->SetFillColor(255,255,255);
			$pdf->SetFont('Arial','',10);
			$pdf->Cell(50,4,$name,0,0,"L",1);
			$pdf->SetTextColor(0,0,0);
				
			$pdf->SetXY($x+50,$y);
			$pdf->SetTextColor(0,0,0);
			$pdf->SetFillColor(255,255,255);
			$pdf->SetFont('Arial','',10);
			$pdf->Cell(30,4,$severity,0,0,"L",1);
			$pdf->SetTextColor(0,0,0);
			
			$pdf->SetXY($x+90,$y);
			$pdf->SetTextColor(0,0,0);
			$pdf->SetFillColor(255,255,255);
			$pdf->SetFont('Arial','',10);
			$pdf->Cell(25,4,$legal,0,0,"L",1);
			$pdf->SetTextColor(0,0,0);
				
			$pdf->SetXY($x+125,$y);
			$pdf->SetTextColor(0,0,0);
			$pdf->SetFillColor(255,255,255);
			$pdf->SetFont('Arial','',10);
			$pdf->Cell(30,4,$dayslost,0,0,"L",1);
			$pdf->SetTextColor(0,0,0);
			
			$pdf->SetXY($x+160,$y);
			$pdf->SetTextColor(0,0,0);
			$pdf->SetFillColor(255,255,255);
			$pdf->SetFont('Arial','',10);
			$pdf->Cell(50,4,$injury1,0,0,"L",1);
			$pdf->SetTextColor(0,0,0);
			
			$pdf->SetXY($x+220,$y);
			$pdf->SetTextColor(0,0,0);
			$pdf->SetFillColor(255,255,255);
			$pdf->SetFont('Arial','',10);
			$pdf->Cell(50,4,$body1,0,0,"L",1);
			$pdf->SetTextColor(0,0,0);
			
			if (trim($injury2) <> '') {
			$y = $y + 5;
			
			$pdf->SetXY($x+160,$y);
			$pdf->SetTextColor(0,0,0);
			$pdf->SetFillColor(255,255,255);
			$pdf->SetFont('Arial','',10);
			$pdf->Cell(50,4,$injury2,0,0,"L",1);
			$pdf->SetTextColor(0,0,0);
			
			$pdf->SetXY($x+220,$y);
			$pdf->SetTextColor(0,0,0);
			$pdf->SetFillColor(255,255,255);
			$pdf->SetFont('Arial','',10);
			$pdf->Cell(50,4,$body2,0,0,"L",1);
			$pdf->SetTextColor(0,0,0);
			}
			
			if (trim($injury3) <> '') {
			$y = $y + 5;
			
			$pdf->SetXY($x+160,$y);
			$pdf->SetTextColor(0,0,0);
			$pdf->SetFillColor(255,255,255);
			$pdf->SetFont('Arial','',10);
			$pdf->Cell(50,4,$injury3,0,0,"L",1);
			$pdf->SetTextColor(0,0,0);
			
			$pdf->SetXY($x+220,$y);
			$pdf->SetTextColor(0,0,0);
			$pdf->SetFillColor(255,255,255);
			$pdf->SetFont('Arial','',10);
			$pdf->Cell(50,4,$body3,0,0,"L",1);
			$pdf->SetTextColor(0,0,0);
			}
			
			if (trim($injury4) <> '') {
			$y = $y + 5;
			
			$pdf->SetXY($x+160,$y);
			$pdf->SetTextColor(0,0,0);
			$pdf->SetFillColor(255,255,255);
			$pdf->SetFont('Arial','',10);
			$pdf->Cell(50,4,$injury4,0,0,"L",1);
			$pdf->SetTextColor(0,0,0);
			
			$pdf->SetXY($x+220,$y);
			$pdf->SetTextColor(0,0,0);
			$pdf->SetFillColor(255,255,255);
			$pdf->SetFont('Arial','',10);
			$pdf->Cell(50,4,$body4,0,0,"L",1);
			$pdf->SetTextColor(0,0,0);
			}
			
			if (trim($injury5) <> '') {
			$y = $y + 5;
			
			$pdf->SetXY($x+160,$y);
			$pdf->SetTextColor(0,0,0);
			$pdf->SetFillColor(255,255,255);
			$pdf->SetFont('Arial','',10);
			$pdf->Cell(50,4,$injury5,0,0,"L",1);
			$pdf->SetTextColor(0,0,0);
			
			$pdf->SetXY($x+220,$y);
			$pdf->SetTextColor(0,0,0);
			$pdf->SetFillColor(255,255,255);
			$pdf->SetFont('Arial','',10);
			$pdf->Cell(50,4,$body5,0,0,"L",1);
			$pdf->SetTextColor(0,0,0);
			}
			
			if (trim($injury6) <> '') {
			$y = $y + 5;
			
			$pdf->SetXY($x+160,$y);
			$pdf->SetTextColor(0,0,0);
			$pdf->SetFillColor(255,255,255);
			$pdf->SetFont('Arial','',10);
			$pdf->Cell(50,4,$injury6,0,0,"L",1);
			$pdf->SetTextColor(0,0,0);
			
			$pdf->SetXY($x+220,$y);
			$pdf->SetTextColor(0,0,0);
			$pdf->SetFillColor(255,255,255);
			$pdf->SetFont('Arial','',10);
			$pdf->Cell(50,4,$body6,0,0,"L",1);
			$pdf->SetTextColor(0,0,0);
			}
			
			$y = $y + 5;
			
			$pdf->SetXY($x,$y);
			$pdf->SetTextColor(0,0,0);
			$pdf->SetFillColor(255,255,255);
			$pdf->SetFont('Arial','B',10);
			$pdf->Cell(50,4,"Treatment:",0,0,"L",1);
			$pdf->SetTextColor(0,0,0);
			
			$pdf->SetLineWidth(0.2);
			$pdf->SetDrawcolor(0,0,0);
			$pdf->Rect($x+40,$y,220,10);
			
			$y = $y + 1;
			
			$pdf->SetXY($x+42,$y);
			$pdf->SetTextColor(0,0,0);
			$pdf->SetFillColor(255,255,255);
			$pdf->SetFont('Arial','',10);
			$pdf->MultiCell(210,4,$treatment,0,"L",0);
			$pdf->SetTextColor(0,0,0);
			
			$y = $y + 13;
		}

		$pdf->AddPage();
		
		$pdf->SetXY(20,9);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','B',16);
		$pdf->Cell(280,9,$coyname.' - Incident # '.$incid.' - '.$ref,0,0,"C",1);
		$pdf->SetTextColor(0,0,0);
			
		
		$x = 20;
		$y = 20;
		
		$pdf->SetXY($x,$y);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','B',12);
		$pdf->Cell(80,4,"Action taken to ensure it does not happen again:",0,0,"L",1);
		$pdf->SetTextColor(0,0,0);
		
		$y = $y + 7;
		
		$pdf->SetXY($x,$y);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell(50,4,"Action taken:",0,0,"L",1);
		$pdf->SetTextColor(0,0,0);
			
		$pdf->SetXY($x+150,$y);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell(30,4,"By whom:",0,0,"L",1);
		$pdf->SetTextColor(0,0,0);
		
		$pdf->SetXY($x+210,$y);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell(25,4,"Date Completed:",0,0,"L",1);
		$pdf->SetTextColor(0,0,0);
			
		$qact = "select action,bywhom,date_format(date_done,'%d %M %Y') as dt_done from incactions where incident_id = ".$incid;
		$ract = mysql_query($qact) or die (mysql_error());
		while ($row = mysql_fetch_array($ract)) {
			extract($row);

			$y = $y + 5;

			$pdf->SetXY($x,$y);
			$pdf->SetTextColor(0,0,0);
			$pdf->SetFillColor(255,255,255);
			$pdf->SetFont('Arial','',10);
			$pdf->MultiCell(250,4,$action,0,"L",0);
			$pdf->SetTextColor(0,0,0);
				
			$pdf->SetXY($x+150,$y);
			$pdf->SetTextColor(0,0,0);
			$pdf->SetFillColor(255,255,255);
			$pdf->SetFont('Arial','',10);
			$pdf->Cell(60,4,$bywhom,0,0,"L",1);
			$pdf->SetTextColor(0,0,0);
			
			$pdf->SetXY($x+210,$y);
			$pdf->SetTextColor(0,0,0);
			$pdf->SetFillColor(255,255,255);
			$pdf->SetFont('Arial','',10);
			$pdf->Cell(25,4,$dt_done,0,0,"L",1);
			$pdf->SetTextColor(0,0,0);
		
		}
		
		$new_width  = 200;
		$new_height = 100;
		
		
		$qp = "select picture from incpictures where incident_id = ".$incid;
		$rp = mysql_query($qp) or die (mysql_error());
		while ($row = mysql_fetch_array($rp)) {
			extract($row);
		
			$pdf->AddPage();
	
			$pdf->SetXY(20,9);
			$pdf->SetTextColor(0,0,0);
			$pdf->SetFillColor(255,255,255);
			$pdf->SetFont('Arial','B',16);
			$pdf->Cell(280,9,$coyname.' - Incident # '.$incid.' - '.$ref,0,0,"C",1);
			$pdf->SetTextColor(0,0,0);
			
			$this_image = "../log/ws/incidents/".$picture;
			
			list($width, $height, $type, $attr) = getimagesize("$this_image");
			
			if ($width > $height) {
			  $image_height = floor(($height/$width)*$new_width);
			  $image_width  = $new_width;
			} else {
			  $image_width  = floor(($width/$height)*$new_height);
			  $image_height = $new_height;
			}		
			
			$pdf->Image($this_image,20,20,$image_width,$image_height,'JPG');
		}
		
		

$fname = 'Incident_'.$incid.'.pdf';
$pdf->Output($fname,'I');


?>
