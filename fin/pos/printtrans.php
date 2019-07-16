<?php
//echo "dfsdfsd";
//ini_set('display_errors', true);
session_start();

define('FPDF_FONTPATH','../font/');
require('../includes/fpdf.php');

class PDF extends FPDF
{
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

$y = substr($ddate,0,4);
$m = substr($ddate,5,2);
$d = substr($ddate,8,2);
$dt = date("d-M-Y",time(0,0,0,$m,$d,$y));



require_once("../includes/db2.php");
mysql_select_db($dbase) or die(mysql_error());

/*$query = "select boxno,po,ad1,ad2,ad3,telno,gstno from globals";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);		
$fromad1 = "PO Box ".$boxno.", ".$po;
$fromad2 = $ad1.", ".$ad2;
$fromad3 = $ad3;
$fromad4 = $telno;*/

$query = "select postal1, postal2, pcity, pstate,ppcode,trade_name,abn from zsys_dealerowner WHERE `office_id` = ".$_SESSION['office_id'];
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);		
$fromad1 = $postal1." ".$postal2;
$fromad2 = $pcity;
$fromad3 = $pstate;
$fromad4 = $ppcode;

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
	
	$pdf->Cell($cellwidth,$cellheight,$trade_name,$border,$nextpos,$align,$fill);
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

$query = "select * from $accdbase.".$template." where item = 'gst'";
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

$query = "select * from $accdbase.".$template." where item = 'toname'";
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
	
	$pdf->Cell($cellwidth,$cellheight,$toname,$border,$nextpos,$align,$fill);
}

$query = "select * from $accdbase.".$template." where item = 'toad1'";
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
	
	$pdf->Cell($cellwidth,$cellheight,$toad1,$border,$nextpos,$align,$fill);
}

$query = "select * from $accdbase.".$template." where item = 'toad2'";
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
	
	$pdf->Cell($cellwidth,$cellheight,$toad2,$border,$nextpos,$align,$fill);
}

$query = "select * from $accdbase.".$template." where item = 'toad3'";
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
	
	$pdf->Cell($cellwidth,$cellheight,$toad3,$border,$nextpos,$align,$fill);
}

$query = "select * from $accdbase.".$template." where item = 'toad4'";
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
	
	$pdf->Cell($cellwidth,$cellheight,$toad4,$border,$nextpos,$align,$fill);
}

$query = "select * from $accdbase.".$template." where item = 'fromad1'";
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
	
	$pdf->Cell($cellwidth,$cellheight,$fromad1,$border,$nextpos,$align,$fill);
}

$query = "select * from $accdbase.".$template." where item = 'fromad2'";
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
	
	$pdf->Cell($cellwidth,$cellheight,$fromad2,$border,$nextpos,$align,$fill);
}

$query = "select * from $accdbase.".$template." where item = 'fromad3'";
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
	
	$pdf->Cell($cellwidth,$cellheight,$fromad3,$border,$nextpos,$align,$fill);
}

$query = "select * from $accdbase.".$template." where item = 'fromad4'";
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
	
	$pdf->Cell($cellwidth,$cellheight,$fromad4,$border,$nextpos,$align,$fill);
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
if ($include == 'Y') {
	$ft = explode(',',$fontfamily);
	$fontname = $ft[0];
	$attrib = $ft[1];
	$ftsize = $ft[2];
	$pdf->SetXY($xcoord,$ycoord);
	$pdf->SetFont($fontname,$attrib,$ftsize);
	$gtw = explode(',',$gridwidths);
	$galign = explode(',',$align);
	$gcount = count($gtw);
	$gformat = explode(',',$content);

	foreach ($linedetails as $row) {
		for ($n = 0; $n <= $gcount; $n++) {
			switch ($gformat[$n]) {
			
				case 'C':
					$pdf->Cell($gtw[$n],$cellheight,$row[$n],$border,$nextpos,$galign[$n],$fill);
					break;
				case 'N';
					$pdf->Cell($gtw[$n],$cellheight,number_format($row[$n],2),$border,$nextpos,$galign[$n],$fill);
					break;
				case 'M':
					$pdf->Cell($gtw[$n],$cellheight,$row[$n],$border,$nextpos,$galign[$n],$fill);
					break;				
			
			}
		}
		$pdf->Ln();
		$pdf->SetX($xcoord);
	}
}

$query = "select * from $accdbase.".$template." where item = 'totals'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);
if ($include == 'Y') {
	$ft = explode(',',$fontfamily);
	$fontname = $ft[0];
	$attrib = $ft[1];
	$ftsize = $ft[2];
	$pdf->SetFont($fontname,$attrib,$ftsize);
	$gtw = explode(',',$gridwidths);
	$galign = explode(',',$align);
	$gcount = count($gtw);
	$gformat = explode(',',$content);
	$pdf->Ln();
	$pdf->SetX($xcoord);
	for ($n = 0; $n <= $gcount; $n++) {
		switch ($gformat[$n]) {
			case 'C':
				$pdf->Cell($gtw[$n],$cellheight,$totalline[$n],$border,$nextpos,$galign[$n],$fill);
				break;
			case 'N';
				$pdf->Cell($gtw[$n],$cellheight,number_format($totalline[$n],2),$border,$nextpos,$galign[$n],$fill);
				break;
		}
	}
}

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
	
	$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
}


$pdf->Output();
?>