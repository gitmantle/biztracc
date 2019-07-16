<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>

<body>



<?php
error_reporting (E_ALL ^ E_NOTICE ^ E_WARNING);
session_start();


require_once('../includes/tcpdf/config/lang/eng.php');
require_once('../includes/tcpdf/tcpdf.php');

$newpage = 'Y';
$pdf = new TCPDF('P', 'mm', 'A4');

class PDF extends TCPDF
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

$type = strtoupper($_REQUEST['type']);
$tradingref = strtoupper($_REQUEST['tradingref']);
if (isset($_REQUEST['doemail'])) {
	$doemail = $_REQUEST['doemail'];
} else {
	$doemail = 'N';	
}
if (isset($_REQUEST['noscreen'])) {
	$noscreen = $_REQUEST['noscreen'];
} else {
	$noscreen = 'N';	
}

if (strtoupper($type) == 'INV') {
	$template = 'invtemplate';
}
if (strtoupper($type) == 'C_S') {
	$template = 'c_stemplate';
}
if (strtoupper($type) == 'C_P') {
	$template = 'c_ptemplate';
}
if (strtoupper($type) == 'CRN') {
	$template = 'crntemplate';
}
if (strtoupper($type) == 'GRN') {
	$template = 'grntemplate';
}
if (strtoupper($type) == 'RET') {
	$template = 'rettemplate';
}

$_SESSION['watermark'] = 'N'; //remove and ensure it is set in each trading form.

require("../db.php"); 


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
$coyname = $_SESSION['s_coyname'];
$coyid = $_SESSION['s_coyid'];

$q = "select * from invhead where ref_no = '".$tradingref."'";
$r = mysql_query($q) or die (mysql_error().' '.$q);
$row = mysql_fetch_array($r);
extract($row);
$draccno = $accountno;
$drsubno = $sub;
$p = explode(',',$postaladdress);
$tad = "";
foreach($p as $value) {
	$tad .= $value."\n";
}

$toadd = $client."\n".$tad;
$footmessage = $gldesc;

if ($client == "") {
	$moduledb = $_SESSION['s_cltdb'];
	mysql_select_db($moduledb) or die(mysql_error());
	
	
		$query = "select concat(members.lastname,' ',members.firstname) as account from members left join client_company_xref on members.member_id = client_company_xref.client_id where client_company_xref.drno = ".$draccno." and client_company_xref.drsub = ".$drsubno;		
	$result = mysql_query($query) or die(mysql_error().' '.$query);
	$row = mysql_fetch_array($result);
	extract($row);
	$client = $account;
}

$moduledb = $_SESSION['s_findb'];
mysql_select_db($moduledb) or die(mysql_error());


if ($postaladdress == "") {
	$moduledb = $_SESSION['s_cltdb'];
	mysql_select_db($moduledb) or die(mysql_error());

	$qd = "select addresses.street_no,addresses.ad1,addresses.ad2,addresses.suburb,addresses.town,addresses.postcode,addresses.country from addresses,client_company_xref where addresses.member_id = client_company_xref.client_id and addresses.address_type_id = 4 and client_company_xref.drno = ".$draccno." and client_company_xref.drsub = ".$drsubno;
	$rd = mysql_query($qd) or die (mysql_error().' '.$qd);
	$row = mysql_fetch_array($rd);
	extract($row);
	$toadd = trim($client)."\n";
	if ($street_no.$ad1 <> '') {
		$toadd .= trim($street_no." ".$ad1)."\n";
	}
	if ($ad2 <> '') {
		$toadd .= trim($ad2)."\n";
	}
	if ($suburb <> '') {
		$toadd .= trim($suburb)."\n";
	}
	if ($town <> '') {
		$toadd .= trim($town)."\n";
	}
	if ($postcode <> '') {
		$toadd .= trim($postcode)."\n";
	}
	if ($country <> '') {
		$toadd .= trim($country)."\n";
	}
	
}


$moduledb = $_SESSION['s_findb'];
mysql_select_db($moduledb) or die(mysql_error());


$d = explode(',',$deliveryaddress);
$dad = "";
foreach($d as $value) {
	$dad .= $value."\n";
}
$deliveryaddress = $dad;
$gstabn = 'GST No. '.$gstno;
$reference = $accountno.' - '.$sub;


if ($type == 'INV') {
	
	$moduledb = $_SESSION['s_cltdb'];
	mysql_select_db($moduledb) or die(mysql_error());

	$qd = "select comms.comm from comms,client_company_xref where comms.member_id = client_company_xref.client_id and comms.comms_type_id = 4 and client_company_xref.drno = ".$draccno." and client_company_xref.drsub = ".$drsubno;
	$rd = mysql_query($qd) or die (mysql_error().' '.$qd);
	$row = mysql_fetch_array($rd);
	extract($row);
	$email_to = $comm;
} else {
	$email_to = "";
}


$moduledb = $_SESSION['s_findb'];
mysql_select_db($moduledb) or die(mysql_error());


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

$newpage = 'Y';
$pdf = new TCPDF($orient,$units,$paper);

$pdf->AddPage();
$pdf->SetFont('times','',10);
$pdf->SetLeftMargin(0);

echo 'got here';


//***************************************************************************
function heading() 
//***************************************************************************
{
	global $template, $pdf, $fromadd, $toadd, $gstabn, $reference, $orient, $paper, $ddate, $tradingref, $postaladdress, $deliveryaddress, $signature;

	//$ymd = explode('-',$ddate);
	//$dt = $ymd[2].'/'.$ymd[1].'/'.$ymd[0];	
	$dt = date("j F,Y",strtotime($ddate));
	
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
	if ($orient == 'P' && $paper = 'A4') {
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
		$pdf->RoundedRect($xcoord, $ycoord, $cellwidth, $cellheight, '1111');
	}
	
	$query = "select * from ".$template." where item = 'rbox2'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->RoundedRect($xcoord, $ycoord, $cellwidth, $cellheight, '1111');
	}
	
	$query = "select * from ".$template." where item = 'rbox3'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->RoundedRect($xcoord, $ycoord, $cellwidth, $cellheight, '1111');
	}
	
	$query = "select * from ".$template." where item = 'rbox4'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->RoundedRect($xcoord, $ycoord, $cellwidth, $cellheight, '1111');
	}
	
	$query = "select * from ".$template." where item = 'rbox5'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->RoundedRect($xcoord, $ycoord, $cellwidth, $cellheight, '1111');
	}
	
	$query = "select * from ".$template." where item = 'rbox6'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->RoundedRect($xcoord, $ycoord, $cellwidth, $cellheight, '1111');
	}
	
	$query = "select * from ".$template." where item = 'rbox7'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->RoundedRect($xcoord, $ycoord, $cellwidth, $cellheight, '1111');
	}
	
	$query = "select * from ".$template." where item = 'rbox8'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->RoundedRect($xcoord, $ycoord, $cellwidth, $cellheight, '1111');
	}
	
	$query = "select * from ".$template." where item = 'rbox9'";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	extract($row);
	if ($include == 'Y') {
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->RoundedRect($xcoord, $ycoord, $cellwidth, $cellheight, '1111');
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
		
		$pdf->Cell($cellwidth,$cellheight,$trade_name,$border,$nextpos,$align,$fill);
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
		
		$pdf->MultiCell($cellwidth,$cellheight,$toadd);
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
		
		$pdf->MultiCell($cellwidth,$cellheight,$deliveryaddress);
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
		
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
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
		
		$pdf->Cell($cellwidth,$cellheight,$tradingref,$border,$nextpos,$align,$fill);
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
	global $template, $pdf, $lastpage, $currentline, $tradingref;
	
	$q = "select itemcode,item,quantity,price,value,discount,tax,(value+tax) as total from invtrans where ref_no = '".$tradingref."'";
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
	if ($include == 'Y') {
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
			
			if ($pdf->PageNo() == 1) {
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
		
		} //while
		
	} //if included
	

} // function details()

//********************************************************************************
function footer()
//********************************************************************************
{
	global $template, $pdf, $reference, $tradingref, $remmitance, $footmessage;
	
	$q = "select totvalue as totalvalue, tax as totaltax, totvalue+tax as totaldue from invhead where ref_no = '".$tradingref."'";
	$r = mysql_query($q) or die (mysql_error().' '.$q);
	$row = mysql_fetch_array($r);
	extract($row);

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
	if ($include == 'Y') {
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


$lastpage = 'N';
$currentline = 0;

while ($lastpage == 'N') {
	heading();
	details();
}
footer();

$fname = 'trading_docs/'.$coyid.'/'.$tradingref.".pdf";
$fn = $tradingref.".pdf";
$pdf->Output($fname,'F');
if ($noscreen = 'N') {
	$pdf->Output($fn,'I');
}
$pdf->close();

if($doemail == 'Y') {
	
//******************************************************************************************************
// email output
//******************************************************************************************************
	$ok = 'Y';
	if (trim($email_from) == "") {
		$ok = 'N';
	}
	if (trim($email_to) == "") {
		$ok = 'N';
	}
			
	if ($ok == 'Y') {
		
		$subject = ('Invoice '.$fname);
		$mess = "Invoice ".$fname." attached\r\n\r\n"."Regards\r\n".$coyname;
		$to = trim($email_to);
		$from = $email_from; 
		$attfile = $fname;

		require_once '../includes/swift_email/swift_required.php';
							
		$transport = Swift_SmtpTransport::newInstance('smtp.webhost.co.nz', 25);
		$mailer = Swift_Mailer::newInstance($transport);
		$message = Swift_Message::newInstance();
		$message->setSubject($subject);
		$message->setFrom(array($from));
		$message->setTo(array($to));
		$mstring = "Dear Sir/Madam\r\n\r\n".$mess."\r\n\r\n";
		$message->setBody($mstring,'text/plain');

		$message->attach(Swift_Attachment::fromPath($attfile));

		$result = $mailer->send($message);

		if ($result > 0) {
			$ret = "Email sent";
		} else {
			$ret = $result;
		}
		echo '<script>';
		echo 'alert("'.$ret.'");';
		echo 'this.close();';
		echo '</script>';
		
	}

}


?>

</body>
</html>
