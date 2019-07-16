<?php

error_reporting (E_ALL ^ E_NOTICE ^ E_WARNING);
session_start();

define('FPDF_FONTPATH','../includes/font/');
require('../includes/fpdf/fpdf.php');

class AlphaPDF extends FPDF
{
    var $extgstates = array();

    // alpha: real value from 0 (transparent) to 1 (opaque)
    // bm:    blend mode, one of the following:
    //          Normal, Multiply, Screen, Overlay, Darken, Lighten, ColorDodge, ColorBurn,
    //          HardLight, SoftLight, Difference, Exclusion, Hue, Saturation, Color, Luminosity
    function SetAlpha($alpha, $bm='Normal')
    {
        // set alpha for stroking (CA) and non-stroking (ca) operations
        $gs = $this->AddExtGState(array('ca'=>$alpha, 'CA'=>$alpha, 'BM'=>'/'.$bm));
        $this->SetExtGState($gs);
    }

    function AddExtGState($parms)
    {
        $n = count($this->extgstates)+1;
        $this->extgstates[$n]['parms'] = $parms;
        return $n;
    }

    function SetExtGState($gs)
    {
        $this->_out(sprintf('/GS%d gs', $gs));
    }

    function _enddoc()
    {
        if(!empty($this->extgstates) && $this->PDFVersion<'1.4')
            $this->PDFVersion='1.4';
        parent::_enddoc();
    }

    function _putextgstates()
    {
        for ($i = 1; $i <= count($this->extgstates); $i++)
        {
            $this->_newobj();
            $this->extgstates[$i]['n'] = $this->n;
            $this->_out('<</Type /ExtGState');
            $parms = $this->extgstates[$i]['parms'];
            $this->_out(sprintf('/ca %.3F', $parms['ca']));
            $this->_out(sprintf('/CA %.3F', $parms['CA']));
            $this->_out('/BM '.$parms['BM']);
            $this->_out('>>');
            $this->_out('endobj');
        }
    }

    function _putresourcedict()
    {
        parent::_putresourcedict();
        $this->_out('/ExtGState <<');
        foreach($this->extgstates as $k=>$extgstate)
            $this->_out('/GS'.$k.' '.$extgstate['n'].' 0 R');
        $this->_out('>>');
    }

    function _putresources()
    {
        $this->_putextgstates();
        parent::_putresources();
    }
}


class PDF extends AlphaPDF
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

$type = $_REQUEST['type'];
$rf = $_REQUEST['rf'];


if (isset($_REQUEST['doemail'])) {
	$doemail = $_REQUEST['doemail'];
} else {
	$doemail = 'N';	
}

if (strtoupper($type) == 'QOT') {
	$template = 'qottemplate';
}
if (strtoupper($type) == 'S_O') {
	$template = 's_otemplate';
}
if (strtoupper($type) == 'D_N') {
	$template = 'd_ntemplate';
}

$_SESSION['watermark'] = 'N'; //remove and ensure it is set in each trading form.

$usersession = $_SESSION['usersession'];

include_once("../includes/DBClass.php");
$dbp = new DBClass();

$dbp->query("select * from sessions where session = :vusersession");
$dbp->bind(':vusersession', $usersession);
$row = $dbp->single();
$subid = $row['subid'];
$user_id = $row['user_id'];
$subscriber = $subid;
$sname = $row['uname'];

$cltdb = $_SESSION['s_cltdb'];
$findb = $_SESSION['s_findb'];

// if rf is QOT, get quotes.uid
if (substr($rf,0,3) == 'QOT') {
	$dbp->query("select uid from ".$cltdb.".quotes where ref_no = '".$rf."'");
	$row = $dbp->single();
	extract($row);
	$rf = $uid;
}

$dbp->query("select * from ".$cltdb.".quotes where uid = ".$rf);
$row = $dbp->single();
extract($row);
$footmessage = $note;
$quoteid = $uid;
$tradingref = $ref_no;

$draccno = $accountno;
$drsubno = $sub;
$p = explode(',',$postaladdress);
$tad = "";
foreach($p as $value) {
	$tad .= $value."\n";
}

$toadd = $client."\n".$tad;

if ($client == "") {
	
	$dbp->query("select concat(members.firstname,' ',members.lastname) as account from ".$cltdb.".members left join ".$cltdb.".client_company_xref on members.member_id = client_company_xref.client_id where client_company_xref.drno = ".$draccno." and client_company_xref.drsub = ".$drsubno);		
	$row = $dbp->single();
	extract($row);
	$client = $account;
}

if ($postaladdress == "" && $draccno > 0) {

	$dbp->query("select addresses.street_no,addresses.ad1,addresses.ad2,addresses.suburb,addresses.town,addresses.postcode,addresses.country from ".$cltdb.".addresses,".$cltdb.".client_company_xref where addresses.member_id = client_company_xref.client_id and addresses.address_type_id = 2 and client_company_xref.drno = ".$draccno." and client_company_xref.drsub = ".$drsubno);
	$row = $dbp->single();
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

$d = explode(',',$deliveryaddress);
$dad = "";
foreach($d as $value) {
	$dad .= $value."\n";
}

$coyid = $coy_id;
$_SESSION['s_coyid'] = $coyid;

$_SESSION['s_findb'] = 'infinint_fin'.$subid.'_'.$coyid;

$findb = $_SESSION['s_findb'];

$dbp->query("select * from ".$findb.".globals");
$row = $dbp->single();
extract($row);

$fromadd = $ad1." ".$ad2."\n";
$fromadd .= $ad3."\n";
$fromadd .= "Phone: ".$telno."\n";
$fromadd .= "Fax:   ".$faxno."\n";
$fromadd .= "email: ".$email;
$email_from = $email;
$coyid = $_SESSION['s_coyid'];


$deliveryaddress = $dad;
$gstabn = 'GST No. '.$gstno;
$reference = $accountno.' - '.$sub;

$dbp->query("select comms.comm from ".$cltdb.".comms,".$cltdb.".client_company_xref where comms.member_id = client_company_xref.client_id and comms.comms_type_id = 4 and client_company_xref.drno = ".$draccno." and client_company_xref.drsub = ".$drsubno." limit 1");
$row = $dbp->single();
extract($row);
$email_to = $comm;

$sql = "select * from ".$findb.".".$template." where item = 'page'";
$dbp->query($sql);
$row = $dbp->single();
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
	global $template, $pdf, $fromadd, $toadd, $gstabn, $reference, $orient, $paper, $ddate, $tradingref, $postaladdress, $deliveryaddress, $signature, $lastpage, $findb, $cltdb, $dbp;
	
	//$ymd = explode('-',$ddate);
	//$dt = $ymd[2].'/'.$ymd[1].'/'.$ymd[0];	
	$dt = date("j F,Y",strtotime($ddate));

	$sql = "select * from ".$findb.".".$template." where item = 'watermark'";
	$dbp->query($sql);
	$row = $dbp->single();
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
	
	$sql = "select * from ".$findb.".".$template." where item = 'image'";
	$dbp->query($sql);
	$row = $dbp->single();
	extract($row);
	if ($include == 'Y') {
		$pdf->Image($content,$xcoord,$ycoord,$cellwidth,$cellheight,'jpg');
	}	

	$sql = "select * from ".$findb.".".$template." where item = 'box1'";
	$dbp->query($sql);
	$row = $dbp->single();
	extract($row);
	if ($include == 'Y') {
		if ($drawcolor != '0,0,0') {
			$tc = explode(',',$drawcolor);
			$pdf->SetDrawColor(intval($tc[0]),intval($tc[1]),intval($tc[2]));
		}
		if ($fill == 1) {
			$df = 'DF';
		} else {
			$df = 'D';
		}
		$fc = explode(',',$fillcolor);
		$pdf->SetFillColor(intval($fc[0]),intval($fc[1]),intval($fc[2]));
		$pdf->SetAlpha($alpha);		
		$pdf->Rect($xcoord,$ycoord,$cellwidth,$cellheight,$df);
		$pdf->SetAlpha(1);		
		$pdf->SetDrawColor(0,0,0);
		$pdf->SetFillColor(0);
	}
	
	$sql = "select * from ".$findb.".".$template." where item = 'box2'";
	$dbp->query($sql);
	$row = $dbp->single();
	extract($row);
	if ($include == 'Y') {
		if ($drawcolor != '0,0,0') {
			$tc = explode(',',$drawcolor);
			$pdf->SetDrawColor(intval($tc[0]),intval($tc[1]),intval($tc[2]));
		}
		if ($fill == 1) {
			$df = 'DF';
		} else {
			$df = 'D';
		}
		$fc = explode(',',$fillcolor);
		$pdf->SetFillColor(intval($fc[0]),intval($fc[1]),intval($fc[2]));
		$pdf->SetAlpha($alpha);		
		$pdf->Rect($xcoord,$ycoord,$cellwidth,$cellheight,$df);
		$pdf->SetAlpha(1);		
		$pdf->SetDrawColor(0,0,0);
		$pdf->SetFillColor(0);
	}
	
	$sql = "select * from ".$findb.".".$template." where item = 'box3'";
	$dbp->query($sql);
	$row = $dbp->single();
	extract($row);
	if ($include == 'Y') {
		if ($drawcolor != '0,0,0') {
			$tc = explode(',',$drawcolor);
			$pdf->SetDrawColor(intval($tc[0]),intval($tc[1]),intval($tc[2]));
		}
		if ($fill == 1) {
			$df = 'DF';
		} else {
			$df = 'D';
		}
		$fc = explode(',',$fillcolor);
		$pdf->SetFillColor(intval($fc[0]),intval($fc[1]),intval($fc[2]));
		$pdf->SetAlpha($alpha);		
		$pdf->Rect($xcoord,$ycoord,$cellwidth,$cellheight,$df);
		$pdf->SetAlpha(1);		
		$pdf->SetDrawColor(0,0,0);
		$pdf->SetFillColor(0);
	}
	
	$sql = "select * from ".$findb.".".$template." where item = 'box4'";
	$dbp->query($sql);
	$row = $dbp->single();
	extract($row);
	if ($include == 'Y') {
		if ($drawcolor != '0,0,0') {
			$tc = explode(',',$drawcolor);
			$pdf->SetDrawColor(intval($tc[0]),intval($tc[1]),intval($tc[2]));
		}
		if ($fill == 1) {
			$df = 'DF';
		} else {
			$df = 'D';
		}
		$fc = explode(',',$fillcolor);
		$pdf->SetFillColor(intval($fc[0]),intval($fc[1]),intval($fc[2]));
		$pdf->SetAlpha($alpha);		
		$pdf->Rect($xcoord,$ycoord,$cellwidth,$cellheight,$df);
		$pdf->SetAlpha(1);		
		$pdf->SetDrawColor(0,0,0);
		$pdf->SetFillColor(0);
	}
	
	$sql = "select * from ".$findb.".".$template." where item = 'box5'";
	$dbp->query($sql);
	$row = $dbp->single();
	extract($row);
	if ($include == 'Y') {
		if ($drawcolor != '0,0,0') {
			$tc = explode(',',$drawcolor);
			$pdf->SetDrawColor(intval($tc[0]),intval($tc[1]),intval($tc[2]));
		}
		if ($fill == 1) {
			$df = 'DF';
		} else {
			$df = 'D';
		}
		$fc = explode(',',$fillcolor);
		$pdf->SetFillColor(intval($fc[0]),intval($fc[1]),intval($fc[2]));
		$pdf->SetAlpha($alpha);		
		$pdf->Rect($xcoord,$ycoord,$cellwidth,$cellheight,$df);
		$pdf->SetAlpha(1);		
		$pdf->SetDrawColor(0,0,0);
		$pdf->SetFillColor(0);
	}
	
	$sql = "select * from ".$findb.".".$template." where item = 'box6'";
	$dbp->query($sql);
	$row = $dbp->single();
	extract($row);
	if ($include == 'Y') {
		if ($drawcolor != '0,0,0') {
			$tc = explode(',',$drawcolor);
			$pdf->SetDrawColor(intval($tc[0]),intval($tc[1]),intval($tc[2]));
		}
		if ($fill == 1) {
			$df = 'DF';
		} else {
			$df = 'D';
		}
		$fc = explode(',',$fillcolor);
		$pdf->SetFillColor(intval($fc[0]),intval($fc[1]),intval($fc[2]));
		$pdf->SetAlpha($alpha);		
		$pdf->Rect($xcoord,$ycoord,$cellwidth,$cellheight,$df);
		$pdf->SetAlpha(1);		
		$pdf->SetDrawColor(0,0,0);
		$pdf->SetFillColor(0);
	}
	
	$sql = "select * from ".$findb.".".$template." where item = 'box7'";
	$dbp->query($sql);
	$row = $dbp->single();
	extract($row);
	if ($include == 'Y') {
		if ($drawcolor != '0,0,0') {
			$tc = explode(',',$drawcolor);
			$pdf->SetDrawColor(intval($tc[0]),intval($tc[1]),intval($tc[2]));
		}
		if ($fill == 1) {
			$df = 'DF';
		} else {
			$df = 'D';
		}
		$fc = explode(',',$fillcolor);
		$pdf->SetFillColor(intval($fc[0]),intval($fc[1]),intval($fc[2]));
		$pdf->SetAlpha($alpha);		
		$pdf->Rect($xcoord,$ycoord,$cellwidth,$cellheight,$df);
		$pdf->SetAlpha(1);		
		$pdf->SetDrawColor(0,0,0);
		$pdf->SetFillColor(0);
	}
	
	$sql = "select * from ".$findb.".".$template." where item = 'box8'";
	$dbp->query($sql);
	$row = $dbp->single();
	extract($row);
	if ($include == 'Y') {
		if ($drawcolor != '0,0,0') {
			$tc = explode(',',$drawcolor);
			$pdf->SetDrawColor(intval($tc[0]),intval($tc[1]),intval($tc[2]));
		}
		if ($fill == 1) {
			$df = 'DF';
		} else {
			$df = 'D';
		}
		$fc = explode(',',$fillcolor);
		$pdf->SetFillColor(intval($fc[0]),intval($fc[1]),intval($fc[2]));
		$pdf->SetAlpha($alpha);		
		$pdf->Rect($xcoord,$ycoord,$cellwidth,$cellheight,$df);
		$pdf->SetAlpha(1);		
		$pdf->SetDrawColor(0,0,0);
		$pdf->SetFillColor(0);
	}
	
	$sql = "select * from ".$findb.".".$template." where item = 'box9'";
	$dbp->query($sql);
	$row = $dbp->single();
	extract($row);
	if ($include == 'Y') {
		if ($drawcolor != '0,0,0') {
			$tc = explode(',',$drawcolor);
			$pdf->SetDrawColor(intval($tc[0]),intval($tc[1]),intval($tc[2]));
		}
		if ($fill == 1) {
			$df = 'DF';
		} else {
			$df = 'D';
		}
		$fc = explode(',',$fillcolor);
		$pdf->SetFillColor(intval($fc[0]),intval($fc[1]),intval($fc[2]));
		$pdf->SetAlpha($alpha);		
		$pdf->Rect($xcoord,$ycoord,$cellwidth,$cellheight,$df);
		$pdf->SetAlpha(1);		
		$pdf->SetDrawColor(0,0,0);
		$pdf->SetFillColor(0);
	}	
	
	$sql = "select * from ".$findb.".".$template." where item = 'rbox1'";
	$dbp->query($sql);
	$row = $dbp->single();
	extract($row);
	if ($include == 'Y') {
		$pdf->SetXY($xcoord,$ycoord);
		if ($drawcolor != '0,0,0') {
			$tc = explode(',',$drawcolor);
			$pdf->SetDrawColor(intval($tc[0]),intval($tc[1]),intval($tc[2]));
		}
		if ($fill == 1) {
			$df = 'DF';
		} else {
			$df = 'D';
		}
		$fc = explode(',',$fillcolor);
		$pdf->SetFillColor(intval($fc[0]),intval($fc[1]),intval($fc[2]));
		$pdf->SetAlpha($alpha);		
		$pdf->RoundedRect($xcoord, $ycoord, $cellwidth, $cellheight, 2.5, $df);
		$pdf->SetAlpha(1);		
		$pdf->SetDrawColor(0,0,0);
		$pdf->SetFillColor(0);
	}
	
	$sql = "select * from ".$findb.".".$template." where item = 'rbox2'";
	$dbp->query($sql);
	$row = $dbp->single();
	extract($row);
	if ($include == 'Y') {
		$pdf->SetXY($xcoord,$ycoord);
		if ($drawcolor != '0,0,0') {
			$tc = explode(',',$drawcolor);
			$pdf->SetDrawColor(intval($tc[0]),intval($tc[1]),intval($tc[2]));
		}
		if ($fill == 1) {
			$df = 'DF';
		} else {
			$df = 'D';
		}
		$fc = explode(',',$fillcolor);
		$pdf->SetFillColor(intval($fc[0]),intval($fc[1]),intval($fc[2]));
		$pdf->SetAlpha($alpha);		
		$pdf->RoundedRect($xcoord, $ycoord, $cellwidth, $cellheight, 2.5, $df);
		$pdf->SetAlpha(1);		
		$pdf->SetDrawColor(0,0,0);
		$pdf->SetFillColor(0);
	}
	
	$sql = "select * from ".$findb.".".$template." where item = 'rbox3'";
	$dbp->query($sql);
	$row = $dbp->single();
	extract($row);
	if ($include == 'Y') {
		$pdf->SetXY($xcoord,$ycoord);
		if ($drawcolor != '0,0,0') {
			$tc = explode(',',$drawcolor);
			$pdf->SetDrawColor(intval($tc[0]),intval($tc[1]),intval($tc[2]));
		}
		if ($fill == 1) {
			$df = 'DF';
		} else {
			$df = 'D';
		}
		$fc = explode(',',$fillcolor);
		$pdf->SetFillColor(intval($fc[0]),intval($fc[1]),intval($fc[2]));
		$pdf->SetAlpha($alpha);		
		$pdf->RoundedRect($xcoord, $ycoord, $cellwidth, $cellheight, 2.5, $df);
		$pdf->SetAlpha(1);		
		$pdf->SetDrawColor(0,0,0);
		$pdf->SetFillColor(0);
	}
	
	$sql = "select * from ".$findb.".".$template." where item = 'rbox4'";
	$dbp->query($sql);
	$row = $dbp->single();
	extract($row);
	if ($include == 'Y') {
		$pdf->SetXY($xcoord,$ycoord);
		if ($drawcolor != '0,0,0') {
			$tc = explode(',',$drawcolor);
			$pdf->SetDrawColor(intval($tc[0]),intval($tc[1]),intval($tc[2]));
		}
		if ($fill == 1) {
			$df = 'DF';
		} else {
			$df = 'D';
		}
		$fc = explode(',',$fillcolor);
		$pdf->SetFillColor(intval($fc[0]),intval($fc[1]),intval($fc[2]));
		$pdf->SetAlpha($alpha);		
		$pdf->RoundedRect($xcoord, $ycoord, $cellwidth, $cellheight, 2.5, $df);
		$pdf->SetAlpha(1);		
		$pdf->SetDrawColor(0,0,0);
		$pdf->SetFillColor(0);
	}
	
	$sql = "select * from ".$findb.".".$template." where item = 'rbox5'";
	$dbp->query($sql);
	$row = $dbp->single();
	extract($row);
	if ($include == 'Y') {
		$pdf->SetXY($xcoord,$ycoord);
		if ($drawcolor != '0,0,0') {
			$tc = explode(',',$drawcolor);
			$pdf->SetDrawColor(intval($tc[0]),intval($tc[1]),intval($tc[2]));
		}
		if ($fill == 1) {
			$df = 'DF';
		} else {
			$df = 'D';
		}
		$fc = explode(',',$fillcolor);
		$pdf->SetFillColor(intval($fc[0]),intval($fc[1]),intval($fc[2]));
		$pdf->SetAlpha($alpha);		
		$pdf->RoundedRect($xcoord, $ycoord, $cellwidth, $cellheight, 2.5, $df);
		$pdf->SetAlpha(1);		
		$pdf->SetDrawColor(0,0,0);
		$pdf->SetFillColor(0);
	}
	
	$sql = "select * from ".$findb.".".$template." where item = 'rbox6'";
	$dbp->query($sql);
	$row = $dbp->single();
	extract($row);
	if ($include == 'Y') {
		$pdf->SetXY($xcoord,$ycoord);
		if ($drawcolor != '0,0,0') {
			$tc = explode(',',$drawcolor);
			$pdf->SetDrawColor(intval($tc[0]),intval($tc[1]),intval($tc[2]));
		}
		if ($fill == 1) {
			$df = 'DF';
		} else {
			$df = 'D';
		}
		$fc = explode(',',$fillcolor);
		$pdf->SetFillColor(intval($fc[0]),intval($fc[1]),intval($fc[2]));
		$pdf->SetAlpha($alpha);		
		$pdf->RoundedRect($xcoord, $ycoord, $cellwidth, $cellheight, 2.5, $df);
		$pdf->SetAlpha(1);		
		$pdf->SetDrawColor(0,0,0);
		$pdf->SetFillColor(0);
	}
	
	$sql = "select * from ".$findb.".".$template." where item = 'rbox7'";
	$dbp->query($sql);
	$row = $dbp->single();
	extract($row);
	if ($include == 'Y') {
		$pdf->SetXY($xcoord,$ycoord);
		if ($drawcolor != '0,0,0') {
			$tc = explode(',',$drawcolor);
			$pdf->SetDrawColor(intval($tc[0]),intval($tc[1]),intval($tc[2]));
		}
		if ($fill == 1) {
			$df = 'DF';
		} else {
			$df = 'D';
		}
		$fc = explode(',',$fillcolor);
		$pdf->SetFillColor(intval($fc[0]),intval($fc[1]),intval($fc[2]));
		$pdf->SetAlpha($alpha);		
		$pdf->RoundedRect($xcoord, $ycoord, $cellwidth, $cellheight, 2.5, $df);
		$pdf->SetAlpha(1);		
		$pdf->SetDrawColor(0,0,0);
		$pdf->SetFillColor(0);
	}
	
	$sql = "select * from ".$findb.".".$template." where item = 'rbox8'";
	$dbp->query($sql);
	$row = $dbp->single();
	extract($row);
	if ($include == 'Y') {
		$pdf->SetXY($xcoord,$ycoord);
		if ($drawcolor != '0,0,0') {
			$tc = explode(',',$drawcolor);
			$pdf->SetDrawColor(intval($tc[0]),intval($tc[1]),intval($tc[2]));
		}
		if ($fill == 1) {
			$df = 'DF';
		} else {
			$df = 'D';
		}
		$fc = explode(',',$fillcolor);
		$pdf->SetFillColor(intval($fc[0]),intval($fc[1]),intval($fc[2]));
		$pdf->SetAlpha($alpha);		
		$pdf->RoundedRect($xcoord, $ycoord, $cellwidth, $cellheight, 2.5, $df);
		$pdf->SetAlpha(1);		
		$pdf->SetDrawColor(0,0,0);
		$pdf->SetFillColor(0);
	}
	
	$sql = "select * from ".$findb.".".$template." where item = 'rbox9'";
	$dbp->query($sql);
	$row = $dbp->single();
	extract($row);
	if ($include == 'Y') {
		$pdf->SetXY($xcoord,$ycoord);
		if ($drawcolor != '0,0,0') {
			$tc = explode(',',$drawcolor);
			$pdf->SetDrawColor(intval($tc[0]),intval($tc[1]),intval($tc[2]));
		}
		if ($fill == 1) {
			$df = 'DF';
		} else {
			$df = 'D';
		}
		$fc = explode(',',$fillcolor);
		$pdf->SetFillColor(intval($fc[0]),intval($fc[1]),intval($fc[2]));
		$pdf->SetAlpha($alpha);		
		$pdf->RoundedRect($xcoord, $ycoord, $cellwidth, $cellheight, 2.5, $df);
		$pdf->SetAlpha(1);		
		$pdf->SetDrawColor(0,0,0);
		$pdf->SetFillColor(0);
	}
	
	$sql = "select * from ".$findb.".".$template." where item = 'fromname'";
	$dbp->query($sql);
	$row = $dbp->single();
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
	
	$sql = "select * from ".$findb.".".$template." where item = 'fromaddress'";
	$dbp->query($sql);
	$row = $dbp->single();
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$font);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		if ($drawcolor != '0,0,0') {
			$tc = explode(',',$drawcolor);
			$pdf->SetDrawColor(intval($tc[0]),intval($tc[1]),intval($tc[2]));
		}
		if ($fill == 1) {
			$df = true;
		} else {
			$df = false;
		}
		$tc = explode(',',$textcolor);
		$pdf->SetTextColor(intval($tc[0]),intval($tc[1]),intval($tc[2]));
		$fc = explode(',',$fillcolor);
		$pdf->SetFillColor(intval($fc[0]),intval($fc[1]),intval($fc[2]));
		$pdf->MultiCell($cellwidth,$cellheight,$fromadd,$border,'J',$df);
		$pdf->SetDrawColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetTextColor(0,0,0);
	}
	
	$sql = "select * from ".$findb.".".$template." where item = 'toaddress'";
	$dbp->query($sql);
	$row = $dbp->single();
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$font);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		if ($drawcolor != '0,0,0') {
			$tc = explode(',',$drawcolor);
			$pdf->SetDrawColor(intval($tc[0]),intval($tc[1]),intval($tc[2]));
		}
		if ($fill == 1) {
			$df = true;
		} else {
			$df = false;
		}
		$tc = explode(',',$textcolor);
		$pdf->SetTextColor(intval($tc[0]),intval($tc[1]),intval($tc[2]));
		$fc = explode(',',$fillcolor);
		$pdf->SetFillColor(intval($fc[0]),intval($fc[1]),intval($fc[2]));
		$pdf->MultiCell($cellwidth,$cellheight,$toadd,$border,'J',$df);
		$pdf->SetDrawColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetTextColor(0,0,0);
	}
	
	$sql = "select * from ".$findb.".".$template." where item = 'delivery'";
	$dbp->query($sql);
	$row = $dbp->single();
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$font);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		if ($drawcolor != '0,0,0') {
			$tc = explode(',',$drawcolor);
			$pdf->SetDrawColor(intval($tc[0]),intval($tc[1]),intval($tc[2]));
		}
		if ($fill == 1) {
			$df = true;
		} else {
			$df = false;
		}
		$tc = explode(',',$textcolor);
		$pdf->SetTextColor(intval($tc[0]),intval($tc[1]),intval($tc[2]));
		$fc = explode(',',$fillcolor);
		$pdf->SetFillColor(intval($fc[0]),intval($fc[1]),intval($fc[2]));
		$pdf->MultiCell($cellwidth,$cellheight,$deliveryaddress,$border,'J',$df);
		$pdf->SetDrawColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetTextColor(0,0,0);
	}

	
	$sql = "select * from ".$findb.".".$template." where item = 'header1'";
	$dbp->query($sql);
	$row = $dbp->single();
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$font);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		if ($textcolor != '0,0,0') {
			$tc = explode(',',$textcolor);
			$pdf->SetTextColor(intval($tc[0]),intval($tc[1]),intval($tc[2]));
		}
		
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
		$pdf->SetTextColor(0,0,0);
	}
	
	$sql = "select * from ".$findb.".".$template." where item = 'header2'";
	$dbp->query($sql);
	$row = $dbp->single();
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$font);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		if ($textcolor != '0,0,0') {
			$tc = explode(',',$textcolor);
			$pdf->SetTextColor(intval($tc[0]),intval($tc[1]),intval($tc[2]));
		}
		
		$pdf->Cell($cellwidth,$cellheight,$tradingref,$border,$nextpos,$align,$fill);
		$pdf->SetTextColor(0,0,0);
	}
	
	$sql = "select * from ".$findb.".".$template." where item = 'ref1'";
	$dbp->query($sql);
	$row = $dbp->single();
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$font);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		if ($textcolor != '0,0,0') {
			$tc = explode(',',$textcolor);
			$pdf->SetTextColor(intval($tc[0]),intval($tc[1]),intval($tc[2]));
		}
		
		$pdf->Cell($cellwidth,$cellheight,$reference,$border,$nextpos,$align,$fill);
		$pdf->SetTextColor(0,0,0);
	}

	$sql = "select * from ".$findb.".".$template." where item = 'notes'";
	$dbp->query($sql);
	$row = $dbp->single();
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$font);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		if ($drawcolor != '0,0,0') {
			$tc = explode(',',$drawcolor);
			$pdf->SetDrawColor(intval($tc[0]),intval($tc[1]),intval($tc[2]));
		}
		if ($fill == 1) {
			$df = true;
		} else {
			$df = false;
		}
		$tc = explode(',',$textcolor);
		$pdf->SetTextColor(intval($tc[0]),intval($tc[1]),intval($tc[2]));
		$fc = explode(',',$fillcolor);
		$pdf->SetFillColor(intval($fc[0]),intval($fc[1]),intval($fc[2]));
		$pdf->MultiCell($cellwidth,$cellheight,$notes,$border,'J',$df);
		$pdf->SetDrawColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetTextColor(0,0,0);
	}
	
	$sql = "select * from ".$findb.".".$template." where item = 'label1'";
	$dbp->query($sql);
	$row = $dbp->single();
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$font);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		if ($drawcolor != '0,0,0') {
			$tc = explode(',',$drawcolor);
			$pdf->SetDrawColor(intval($tc[0]),intval($tc[1]),intval($tc[2]));
		}
		if ($fill == 1) {
			$df = true;
		} else {
			$df = false;
		}
		$tc = explode(',',$textcolor);
		$pdf->SetTextColor(intval($tc[0]),intval($tc[1]),intval($tc[2]));
		$fc = explode(',',$fillcolor);
		$pdf->SetFillColor(intval($fc[0]),intval($fc[1]),intval($fc[2]));
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$df);
		$pdf->SetDrawColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetTextColor(0,0,0);
	}		

	
	$sql = "select * from ".$findb.".".$template." where item = 'label2'";
	$dbp->query($sql);
	$row = $dbp->single();
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$font);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		if ($drawcolor != '0,0,0') {
			$tc = explode(',',$drawcolor);
			$pdf->SetDrawColor(intval($tc[0]),intval($tc[1]),intval($tc[2]));
		}
		if ($fill == 1) {
			$df = true;
		} else {
			$df = false;
		}
		$tc = explode(',',$textcolor);
		$pdf->SetTextColor(intval($tc[0]),intval($tc[1]),intval($tc[2]));
		$fc = explode(',',$fillcolor);
		$pdf->SetFillColor(intval($fc[0]),intval($fc[1]),intval($fc[2]));
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$df);
		$pdf->SetDrawColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetTextColor(0,0,0);
	}	
	
	$sql = "select * from ".$findb.".".$template." where item = 'label3'";
	$dbp->query($sql);
	$row = $dbp->single();
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$font);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		if ($drawcolor != '0,0,0') {
			$tc = explode(',',$drawcolor);
			$pdf->SetDrawColor(intval($tc[0]),intval($tc[1]),intval($tc[2]));
		}
		if ($fill == 1) {
			$df = true;
		} else {
			$df = false;
		}
		$tc = explode(',',$textcolor);
		$pdf->SetTextColor(intval($tc[0]),intval($tc[1]),intval($tc[2]));
		$fc = explode(',',$fillcolor);
		$pdf->SetFillColor(intval($fc[0]),intval($fc[1]),intval($fc[2]));
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$df);
		$pdf->SetDrawColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetTextColor(0,0,0);
	}	
	
	$sql = "select * from ".$findb.".".$template." where item = 'label4'";
	$dbp->query($sql);
	$row = $dbp->single();
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$font);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		if ($drawcolor != '0,0,0') {
			$tc = explode(',',$drawcolor);
			$pdf->SetDrawColor(intval($tc[0]),intval($tc[1]),intval($tc[2]));
		}
		if ($fill == 1) {
			$df = true;
		} else {
			$df = false;
		}
		$tc = explode(',',$textcolor);
		$pdf->SetTextColor(intval($tc[0]),intval($tc[1]),intval($tc[2]));
		$fc = explode(',',$fillcolor);
		$pdf->SetFillColor(intval($fc[0]),intval($fc[1]),intval($fc[2]));
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$df);
		$pdf->SetDrawColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetTextColor(0,0,0);
	}	
	
	$sql = "select * from ".$findb.".".$template." where item = 'label5'";
	$dbp->query($sql);
	$row = $dbp->single();
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$font);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		if ($drawcolor != '0,0,0') {
			$tc = explode(',',$drawcolor);
			$pdf->SetDrawColor(intval($tc[0]),intval($tc[1]),intval($tc[2]));
		}
		if ($fill == 1) {
			$df = true;
		} else {
			$df = false;
		}
		$tc = explode(',',$textcolor);
		$pdf->SetTextColor(intval($tc[0]),intval($tc[1]),intval($tc[2]));
		$fc = explode(',',$fillcolor);
		$pdf->SetFillColor(intval($fc[0]),intval($fc[1]),intval($fc[2]));
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$df);
		$pdf->SetDrawColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetTextColor(0,0,0);
	}	
	
	$sql = "select * from ".$findb.".".$template." where item = 'label6'";
	$dbp->query($sql);
	$row = $dbp->single();
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$font);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		if ($drawcolor != '0,0,0') {
			$tc = explode(',',$drawcolor);
			$pdf->SetDrawColor(intval($tc[0]),intval($tc[1]),intval($tc[2]));
		}
		if ($fill == 1) {
			$df = true;
		} else {
			$df = false;
		}
		$tc = explode(',',$textcolor);
		$pdf->SetTextColor(intval($tc[0]),intval($tc[1]),intval($tc[2]));
		$fc = explode(',',$fillcolor);
		$pdf->SetFillColor(intval($fc[0]),intval($fc[1]),intval($fc[2]));
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$df);
		$pdf->SetDrawColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetTextColor(0,0,0);
	}	
	
	$sql = "select * from ".$findb.".".$template." where item = 'label7'";
	$dbp->query($sql);
	$row = $dbp->single();
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$font);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		if ($drawcolor != '0,0,0') {
			$tc = explode(',',$drawcolor);
			$pdf->SetDrawColor(intval($tc[0]),intval($tc[1]),intval($tc[2]));
		}
		if ($fill == 1) {
			$df = true;
		} else {
			$df = false;
		}
		$tc = explode(',',$textcolor);
		$pdf->SetTextColor(intval($tc[0]),intval($tc[1]),intval($tc[2]));
		$fc = explode(',',$fillcolor);
		$pdf->SetFillColor(intval($fc[0]),intval($fc[1]),intval($fc[2]));
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$df);
		$pdf->SetDrawColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetTextColor(0,0,0);
	}	
	
	$sql = "select * from ".$findb.".".$template." where item = 'label8'";
	$dbp->query($sql);
	$row = $dbp->single();
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$font);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		if ($drawcolor != '0,0,0') {
			$tc = explode(',',$drawcolor);
			$pdf->SetDrawColor(intval($tc[0]),intval($tc[1]),intval($tc[2]));
		}
		if ($fill == 1) {
			$df = true;
		} else {
			$df = false;
		}
		$tc = explode(',',$textcolor);
		$pdf->SetTextColor(intval($tc[0]),intval($tc[1]),intval($tc[2]));
		$fc = explode(',',$fillcolor);
		$pdf->SetFillColor(intval($fc[0]),intval($fc[1]),intval($fc[2]));
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$df);
		$pdf->SetDrawColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetTextColor(0,0,0);
	}	
	
	$sql = "select * from ".$findb.".".$template." where item = 'label9'";
	$dbp->query($sql);
	$row = $dbp->single();
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$font);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		if ($drawcolor != '0,0,0') {
			$tc = explode(',',$drawcolor);
			$pdf->SetDrawColor(intval($tc[0]),intval($tc[1]),intval($tc[2]));
		}
		if ($fill == 1) {
			$df = true;
		} else {
			$df = false;
		}
		$tc = explode(',',$textcolor);
		$pdf->SetTextColor(intval($tc[0]),intval($tc[1]),intval($tc[2]));
		$fc = explode(',',$fillcolor);
		$pdf->SetFillColor(intval($fc[0]),intval($fc[1]),intval($fc[2]));
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$df);
		$pdf->SetDrawColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetTextColor(0,0,0);
	}	
	
	$sql = "select * from ".$findb.".".$template." where item = 'label10'";
	$dbp->query($sql);
	$row = $dbp->single();
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$font);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		if ($drawcolor != '0,0,0') {
			$tc = explode(',',$drawcolor);
			$pdf->SetDrawColor(intval($tc[0]),intval($tc[1]),intval($tc[2]));
		}
		if ($fill == 1) {
			$df = true;
		} else {
			$df = false;
		}
		$tc = explode(',',$textcolor);
		$pdf->SetTextColor(intval($tc[0]),intval($tc[1]),intval($tc[2]));
		$fc = explode(',',$fillcolor);
		$pdf->SetFillColor(intval($fc[0]),intval($fc[1]),intval($fc[2]));
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$df);
		$pdf->SetDrawColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetTextColor(0,0,0);
	}	
	
	$sql = "select * from ".$findb.".".$template." where item = 'label11'";
	$dbp->query($sql);
	$row = $dbp->single();
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$font);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		if ($drawcolor != '0,0,0') {
			$tc = explode(',',$drawcolor);
			$pdf->SetDrawColor(intval($tc[0]),intval($tc[1]),intval($tc[2]));
		}
		if ($fill == 1) {
			$df = true;
		} else {
			$df = false;
		}
		$tc = explode(',',$textcolor);
		$pdf->SetTextColor(intval($tc[0]),intval($tc[1]),intval($tc[2]));
		$fc = explode(',',$fillcolor);
		$pdf->SetFillColor(intval($fc[0]),intval($fc[1]),intval($fc[2]));
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$df);
		$pdf->SetDrawColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetTextColor(0,0,0);
	}	
	
	$sql = "select * from ".$findb.".".$template." where item = 'label12'";
	$dbp->query($sql);
	$row = $dbp->single();
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$font);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		if ($drawcolor != '0,0,0') {
			$tc = explode(',',$drawcolor);
			$pdf->SetDrawColor(intval($tc[0]),intval($tc[1]),intval($tc[2]));
		}
		if ($fill == 1) {
			$df = true;
		} else {
			$df = false;
		}
		$tc = explode(',',$textcolor);
		$pdf->SetTextColor(intval($tc[0]),intval($tc[1]),intval($tc[2]));
		$fc = explode(',',$fillcolor);
		$pdf->SetFillColor(intval($fc[0]),intval($fc[1]),intval($fc[2]));
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$df);
		$pdf->SetDrawColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetTextColor(0,0,0);
	}	
	
	$sql = "select * from ".$findb.".".$template." where item = 'label13'";
	$dbp->query($sql);
	$row = $dbp->single();
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$font);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		if ($drawcolor != '0,0,0') {
			$tc = explode(',',$drawcolor);
			$pdf->SetDrawColor(intval($tc[0]),intval($tc[1]),intval($tc[2]));
		}
		if ($fill == 1) {
			$df = true;
		} else {
			$df = false;
		}
		$tc = explode(',',$textcolor);
		$pdf->SetTextColor(intval($tc[0]),intval($tc[1]),intval($tc[2]));
		$fc = explode(',',$fillcolor);
		$pdf->SetFillColor(intval($fc[0]),intval($fc[1]),intval($fc[2]));
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$df);
		$pdf->SetDrawColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetTextColor(0,0,0);
	}	
	
	$sql = "select * from ".$findb.".".$template." where item = 'label14'";
	$dbp->query($sql);
	$row = $dbp->single();
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$font);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		if ($drawcolor != '0,0,0') {
			$tc = explode(',',$drawcolor);
			$pdf->SetDrawColor(intval($tc[0]),intval($tc[1]),intval($tc[2]));
		}
		if ($fill == 1) {
			$df = true;
		} else {
			$df = false;
		}
		$tc = explode(',',$textcolor);
		$pdf->SetTextColor(intval($tc[0]),intval($tc[1]),intval($tc[2]));
		$fc = explode(',',$fillcolor);
		$pdf->SetFillColor(intval($fc[0]),intval($fc[1]),intval($fc[2]));
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$df);
		$pdf->SetDrawColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetTextColor(0,0,0);
	}	
	
	$sql = "select * from ".$findb.".".$template." where item = 'label15'";
	$dbp->query($sql);
	$row = $dbp->single();
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$font);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		if ($drawcolor != '0,0,0') {
			$tc = explode(',',$drawcolor);
			$pdf->SetDrawColor(intval($tc[0]),intval($tc[1]),intval($tc[2]));
		}
		if ($fill == 1) {
			$df = true;
		} else {
			$df = false;
		}
		$tc = explode(',',$textcolor);
		$pdf->SetTextColor(intval($tc[0]),intval($tc[1]),intval($tc[2]));
		$fc = explode(',',$fillcolor);
		$pdf->SetFillColor(intval($fc[0]),intval($fc[1]),intval($fc[2]));
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$df);
		$pdf->SetDrawColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetTextColor(0,0,0);
	}	
	
	$sql = "select * from ".$findb.".".$template." where item = 'label16'";
	$dbp->query($sql);
	$row = $dbp->single();
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$font);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		if ($drawcolor != '0,0,0') {
			$tc = explode(',',$drawcolor);
			$pdf->SetDrawColor(intval($tc[0]),intval($tc[1]),intval($tc[2]));
		}
		if ($fill == 1) {
			$df = true;
		} else {
			$df = false;
		}
		$tc = explode(',',$textcolor);
		$pdf->SetTextColor(intval($tc[0]),intval($tc[1]),intval($tc[2]));
		$fc = explode(',',$fillcolor);
		$pdf->SetFillColor(intval($fc[0]),intval($fc[1]),intval($fc[2]));
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$df);
		$pdf->SetDrawColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetTextColor(0,0,0);
	}	
	
	$sql = "select * from ".$findb.".".$template." where item = 'label17'";
	$dbp->query($sql);
	$row = $dbp->single();
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$font);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		if ($drawcolor != '0,0,0') {
			$tc = explode(',',$drawcolor);
			$pdf->SetDrawColor(intval($tc[0]),intval($tc[1]),intval($tc[2]));
		}
		if ($fill == 1) {
			$df = true;
		} else {
			$df = false;
		}
		$tc = explode(',',$textcolor);
		$pdf->SetTextColor(intval($tc[0]),intval($tc[1]),intval($tc[2]));
		$fc = explode(',',$fillcolor);
		$pdf->SetFillColor(intval($fc[0]),intval($fc[1]),intval($fc[2]));
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$df);
		$pdf->SetDrawColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetTextColor(0,0,0);
	}	
	
	$sql = "select * from ".$findb.".".$template." where item = 'label18'";
	$dbp->query($sql);
	$row = $dbp->single();
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$font);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		if ($drawcolor != '0,0,0') {
			$tc = explode(',',$drawcolor);
			$pdf->SetDrawColor(intval($tc[0]),intval($tc[1]),intval($tc[2]));
		}
		if ($fill == 1) {
			$df = true;
		} else {
			$df = false;
		}
		$tc = explode(',',$textcolor);
		$pdf->SetTextColor(intval($tc[0]),intval($tc[1]),intval($tc[2]));
		$fc = explode(',',$fillcolor);
		$pdf->SetFillColor(intval($fc[0]),intval($fc[1]),intval($fc[2]));
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$df);
		$pdf->SetDrawColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetTextColor(0,0,0);
	}	
	
	$sql = "select * from ".$findb.".".$template." where item = 'label19'";
	$dbp->query($sql);
	$row = $dbp->single();
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$font);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		if ($drawcolor != '0,0,0') {
			$tc = explode(',',$drawcolor);
			$pdf->SetDrawColor(intval($tc[0]),intval($tc[1]),intval($tc[2]));
		}
		if ($fill == 1) {
			$df = true;
		} else {
			$df = false;
		}
		$tc = explode(',',$textcolor);
		$pdf->SetTextColor(intval($tc[0]),intval($tc[1]),intval($tc[2]));
		$fc = explode(',',$fillcolor);
		$pdf->SetFillColor(intval($fc[0]),intval($fc[1]),intval($fc[2]));
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$df);
		$pdf->SetDrawColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetTextColor(0,0,0);
	}	
	
	$sql = "select * from ".$findb.".".$template." where item = 'label20'";
	$dbp->query($sql);
	$row = $dbp->single();
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$font);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		if ($drawcolor != '0,0,0') {
			$tc = explode(',',$drawcolor);
			$pdf->SetDrawColor(intval($tc[0]),intval($tc[1]),intval($tc[2]));
		}
		if ($fill == 1) {
			$df = true;
		} else {
			$df = false;
		}
		$tc = explode(',',$textcolor);
		$pdf->SetTextColor(intval($tc[0]),intval($tc[1]),intval($tc[2]));
		$fc = explode(',',$fillcolor);
		$pdf->SetFillColor(intval($fc[0]),intval($fc[1]),intval($fc[2]));
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$df);
		$pdf->SetDrawColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetTextColor(0,0,0);
	}	
	
	
	$sql = "select * from ".$findb.".".$template." where item = 'docdate'";
	$dbp->query($sql);
	$row = $dbp->single();
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
	$sql = "select * from ".$findb.".".$template." where item = 'ref2'";
	$dbp->query($sql);
	$row = $dbp->single();
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
	
	$sql = "select * from ".$findb.".".$template." where item = 'gst'";
	$dbp->query($sql);
	$row = $dbp->single();
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
	global $template, $pdf, $lastpage, $currentline, $tradingref, $findb, $cltdb, $dbp, $quoteid;

	$dbp->query("select itemcode,item,quantity,(price*rate) as price,(value*rate) as value,(discount*rate) as discount,(tax*rate) as tax,(value+tax)*rate as total, note from ".$cltdb.".quotelines where quote_id = ".$quoteid);
	$linedetails = $dbp->resultsetNum();

	$sql = "select * from ".$findb.".".$template." where item = 'gridtitle'";
	$dbp->query($sql);
	$row = $dbp->single();
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
	
	$sql = "select * from ".$findb.".".$template." where item = 'griddetail'";
	$dbp->query($sql);
	$row = $dbp->single();
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
				$ypos = $ycoord + 2;
				$maxypos = $ycoord;
				
				for ($i = $currentline; $i < $numlines; $i++) {
					$rowl = $linedetails[$i];
					
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
							$texte = number_format($rowl[$n],2);
						} else {
							$texte = $rowl[$n];
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
					
					if (!empty($rowl[$gcount + 1])) {
						$texte = $rowl[$gcount + 1];
						$currentline = $i + 1;
						$pdf->SetXY( 15, $ypos + 4);
						$pdf->MultiCell( 140, $cellheight , $texte, $border, 'L');
						$pdf->Ln();
						if ( $maxypos < ($pdf->GetY()  ) ) {
							$maxypos = $pdf->GetY() ;
						}
					}
					
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
					$rowl = $linedetails[$i];
					
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
							$texte = number_format($rowl[$n],2);
						} else {
							$texte = $rowl[$n];
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
					
					$dbp->query("select note from ".$cltdb.".quotelines where ref_no = '".$tradingref."'");
					$row = $dbp->single();
					if (!empty($row)) {
						extract($row);
						$currentline = $i + 1;
						$pdf->SetXY( 15, $ypos + 4);
						$pdf->MultiCell( 140, $cellheight , $note, $border, 'L');
						$pdf->Ln();
						if ( $maxypos < ($pdf->GetY()  ) ) {
							$maxypos = $pdf->GetY() ;
						}
					}

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
	global $template, $pdf, $reference, $tradingref, $remmitance, $footmessage, $signature, $findb, $cltdb, $dbp, $quoteid;
	
	$dbp->query("select (totvalue*rate) as totalvalue, (tax*rate) as totaltax, (totvalue+tax)*rate as totaldue, currency from ".$cltdb.".quotes where uid = ".$quoteid);
	$row = $dbp->single();
	extract($row);
	$fxcode = $currency;
	
	// get currency
	$dbp->query("select descript,symbol from ".$findb.".forex where currency = :currency");
	$dbp->bind(':currency', $fxcode);
	$row = $dbp->single();
	extract($row);
	$fxdescript = $descript;
	$fxsymbol = iconv('UTF-8', 'windows-1252', $symbol);

	$sql = "select * from ".$findb.".".$template." where item = 'footmessage'";
	$dbp->query($sql);
	$row = $dbp->single();
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
	
	$sql = "select * from ".$findb.".".$template." where item = 'currency'";
	$dbp->query($sql);
	$row = $dbp->single();
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$font);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		if ($drawcolor != '0,0,0') {
			$tc = explode(',',$drawcolor);
			$pdf->SetDrawColor(intval($tc[0]),intval($tc[1]),intval($tc[2]));
		}
		if ($fill == 1) {
			$df = true;
		} else {
			$df = false;
		}
		$tc = explode(',',$textcolor);
		$pdf->SetTextColor(intval($tc[0]),intval($tc[1]),intval($tc[2]));
		$fc = explode(',',$fillcolor);
		$pdf->SetFillColor(intval($fc[0]),intval($fc[1]),intval($fc[2]));
		$pdf->MultiCell($cellwidth,$cellheight,$fxdescript,$border,'J',$df);
		$pdf->SetDrawColor(0,0,0);
		$pdf->SetFillColor(0);$pdf->SetTextColor(0,0,0);
	}
	
	$sql = "select * from ".$findb.".".$template." where item = 'footbox1'";
	$dbp->query($sql);
	$row = $dbp->single();
	extract($row);
	if ($include == 'Y') {
		if ($drawcolor != '0,0,0') {
			$tc = explode(',',$drawcolor);
			$pdf->SetDrawColor(intval($tc[0]),intval($tc[1]),intval($tc[2]));
		}
		if ($fill == 1) {
			$df = 'DF';
		} else {
			$df = 'D';
		}
		$fc = explode(',',$fillcolor);
		$pdf->SetFillColor(intval($fc[0]),intval($fc[1]),intval($fc[2]));
		$pdf->SetAlpha($alpha);		
		$pdf->Rect($xcoord,$ycoord,$cellwidth,$cellheight,$df);
		$pdf->SetAlpha(1);		
		$pdf->SetDrawColor(0,0,0);
		$pdf->SetFillColor(0);
	}
	
	$sql = "select * from ".$findb.".".$template." where item = 'footlabel1'";
	$dbp->query($sql);
	$row = $dbp->single();
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$font);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		if ($drawcolor != '0,0,0') {
			$tc = explode(',',$drawcolor);
			$pdf->SetDrawColor(intval($tc[0]),intval($tc[1]),intval($tc[2]));
		}
		if ($fill == 1) {
			$df = true;
		} else {
			$df = false;
		}
		$tc = explode(',',$textcolor);
		$pdf->SetTextColor(intval($tc[0]),intval($tc[1]),intval($tc[2]));
		$fc = explode(',',$fillcolor);
		$pdf->SetFillColor(intval($fc[0]),intval($fc[1]),intval($fc[2]));
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$df);
		$pdf->SetDrawColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetTextColor(0,0,0);
	}	
	
	$sql = "select * from ".$findb.".".$template." where item = 'footlabel2'";
	$dbp->query($sql);
	$row = $dbp->single();
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$font);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		if ($drawcolor != '0,0,0') {
			$tc = explode(',',$drawcolor);
			$pdf->SetDrawColor(intval($tc[0]),intval($tc[1]),intval($tc[2]));
		}
		if ($fill == 1) {
			$df = true;
		} else {
			$df = false;
		}
		$tc = explode(',',$textcolor);
		$pdf->SetTextColor(intval($tc[0]),intval($tc[1]),intval($tc[2]));
		$fc = explode(',',$fillcolor);
		$pdf->SetFillColor(intval($fc[0]),intval($fc[1]),intval($fc[2]));
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$df);
		$pdf->SetDrawColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetTextColor(0,0,0);
	}	
	
	$sql = "select * from ".$findb.".".$template." where item = 'footlabel3'";
	$dbp->query($sql);
	$row = $dbp->single();
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$font);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		if ($drawcolor != '0,0,0') {
			$tc = explode(',',$drawcolor);
			$pdf->SetDrawColor(intval($tc[0]),intval($tc[1]),intval($tc[2]));
		}
		if ($fill == 1) {
			$df = true;
		} else {
			$df = false;
		}
		$tc = explode(',',$textcolor);
		$pdf->SetTextColor(intval($tc[0]),intval($tc[1]),intval($tc[2]));
		$fc = explode(',',$fillcolor);
		$pdf->SetFillColor(intval($fc[0]),intval($fc[1]),intval($fc[2]));
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$df);
		$pdf->SetDrawColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetTextColor(0,0,0);
	}	


	$sql = "select * from ".$findb.".".$template." where item = 'footlabel4'";
	$dbp->query($sql);
	$row = $dbp->single();
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$font);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		if ($drawcolor != '0,0,0') {
			$tc = explode(',',$drawcolor);
			$pdf->SetDrawColor(intval($tc[0]),intval($tc[1]),intval($tc[2]));
		}
		if ($fill == 1) {
			$df = true;
		} else {
			$df = false;
		}
		$tc = explode(',',$textcolor);
		$pdf->SetTextColor(intval($tc[0]),intval($tc[1]),intval($tc[2]));
		$fc = explode(',',$fillcolor);
		$pdf->SetFillColor(intval($fc[0]),intval($fc[1]),intval($fc[2]));
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$df);
		$pdf->SetDrawColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetTextColor(0,0,0);
	}	
	
	$sql = "select * from ".$findb.".".$template." where item = 'footlabel5'";
	$dbp->query($sql);
	$row = $dbp->single();
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$font);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		if ($drawcolor != '0,0,0') {
			$tc = explode(',',$drawcolor);
			$pdf->SetDrawColor(intval($tc[0]),intval($tc[1]),intval($tc[2]));
		}
		if ($fill == 1) {
			$df = true;
		} else {
			$df = false;
		}
		$tc = explode(',',$textcolor);
		$pdf->SetTextColor(intval($tc[0]),intval($tc[1]),intval($tc[2]));
		$fc = explode(',',$fillcolor);
		$pdf->SetFillColor(intval($fc[0]),intval($fc[1]),intval($fc[2]));
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$df);
		$pdf->SetDrawColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetTextColor(0,0,0);
	}	
	
	$sql = "select * from ".$findb.".".$template." where item = 'footlabel6'";
	$dbp->query($sql);
	$row = $dbp->single();
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$font);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		if ($drawcolor != '0,0,0') {
			$tc = explode(',',$drawcolor);
			$pdf->SetDrawColor(intval($tc[0]),intval($tc[1]),intval($tc[2]));
		}
		if ($fill == 1) {
			$df = true;
		} else {
			$df = false;
		}
		$tc = explode(',',$textcolor);
		$pdf->SetTextColor(intval($tc[0]),intval($tc[1]),intval($tc[2]));
		$fc = explode(',',$fillcolor);
		$pdf->SetFillColor(intval($fc[0]),intval($fc[1]),intval($fc[2]));
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$df);
		$pdf->SetDrawColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetTextColor(0,0,0);
	}	
	
	$sql = "select * from ".$findb.".".$template." where item = 'footlabel7'";
	$dbp->query($sql);
	$row = $dbp->single();
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$font);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		if ($drawcolor != '0,0,0') {
			$tc = explode(',',$drawcolor);
			$pdf->SetDrawColor(intval($tc[0]),intval($tc[1]),intval($tc[2]));
		}
		if ($fill == 1) {
			$df = true;
		} else {
			$df = false;
		}
		$tc = explode(',',$textcolor);
		$pdf->SetTextColor(intval($tc[0]),intval($tc[1]),intval($tc[2]));
		$fc = explode(',',$fillcolor);
		$pdf->SetFillColor(intval($fc[0]),intval($fc[1]),intval($fc[2]));
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$df);
		$pdf->SetDrawColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetTextColor(0,0,0);
	}	
	
	$sql = "select * from ".$findb.".".$template." where item = 'footlabel8'";
	$dbp->query($sql);
	$row = $dbp->single();
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$font);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		if ($drawcolor != '0,0,0') {
			$tc = explode(',',$drawcolor);
			$pdf->SetDrawColor(intval($tc[0]),intval($tc[1]),intval($tc[2]));
		}
		if ($fill == 1) {
			$df = true;
		} else {
			$df = false;
		}
		$tc = explode(',',$textcolor);
		$pdf->SetTextColor(intval($tc[0]),intval($tc[1]),intval($tc[2]));
		$fc = explode(',',$fillcolor);
		$pdf->SetFillColor(intval($fc[0]),intval($fc[1]),intval($fc[2]));
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$df);
		$pdf->SetDrawColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetTextColor(0,0,0);
	}	
	
	$sql = "select * from ".$findb.".".$template." where item = 'footlabel9'";
	$dbp->query($sql);
	$row = $dbp->single();
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$font);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		if ($drawcolor != '0,0,0') {
			$tc = explode(',',$drawcolor);
			$pdf->SetDrawColor(intval($tc[0]),intval($tc[1]),intval($tc[2]));
		}
		if ($fill == 1) {
			$df = true;
		} else {
			$df = false;
		}
		$tc = explode(',',$textcolor);
		$pdf->SetTextColor(intval($tc[0]),intval($tc[1]),intval($tc[2]));
		$fc = explode(',',$fillcolor);
		$pdf->SetFillColor(intval($fc[0]),intval($fc[1]),intval($fc[2]));
		$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$df);
		$pdf->SetDrawColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetTextColor(0,0,0);
	}	
	
	
	
	$sql = "select * from ".$findb.".".$template." where item = 'totval'";
	$dbp->query($sql);
	$row = $dbp->single();
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$font);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		if ($drawcolor != '0,0,0') {
			$tc = explode(',',$drawcolor);
			$pdf->SetDrawColor(intval($tc[0]),intval($tc[1]),intval($tc[2]));
		}
		if ($fill == 1) {
			$df = true;
		} else {
			$df = false;
		}
		$tc = explode(',',$textcolor);
		$pdf->SetTextColor(intval($tc[0]),intval($tc[1]),intval($tc[2]));
		$fc = explode(',',$fillcolor);
		$pdf->SetFillColor(intval($fc[0]),intval($fc[1]),intval($fc[2]));
		$pdf->Cell($cellwidth,$cellheight,$fxsymbol.' '.number_format($totalvalue,2),$border,$nextpos,$align,$df);
		$pdf->SetDrawColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetTextColor(0,0,0);
	}		

	
	$sql = "select * from ".$findb.".".$template." where item = 'tottax'";
	$dbp->query($sql);
	$row = $dbp->single();
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$font);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		if ($drawcolor != '0,0,0') {
			$tc = explode(',',$drawcolor);
			$pdf->SetDrawColor(intval($tc[0]),intval($tc[1]),intval($tc[2]));
		}
		if ($fill == 1) {
			$df = true;
		} else {
			$df = false;
		}
		$tc = explode(',',$textcolor);
		$pdf->SetTextColor(intval($tc[0]),intval($tc[1]),intval($tc[2]));
		$fc = explode(',',$fillcolor);
		$pdf->SetFillColor(intval($fc[0]),intval($fc[1]),intval($fc[2]));
		$pdf->Cell($cellwidth,$cellheight,$fxsymbol.' '.number_format($totaltax,2),$border,$nextpos,$align,$df);
		$pdf->SetDrawColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetTextColor(0,0,0);
	}		

	
	$sql = "select * from ".$findb.".".$template." where item = 'totdue'";
	$dbp->query($sql);
	$row = $dbp->single();
	extract($row);
	if ($include == 'Y') {
		$ft = explode(',',$font);
		$fontname = $ft[0];
		$attrib = $ft[1];
		$ftsize = $ft[2];
		$pdf->SetXY($xcoord,$ycoord);
		$pdf->SetFont($fontname,$attrib,$ftsize);
		if ($drawcolor != '0,0,0') {
			$tc = explode(',',$drawcolor);
			$pdf->SetDrawColor(intval($tc[0]),intval($tc[1]),intval($tc[2]));
		}
		if ($fill == 1) {
			$df = true;
		} else {
			$df = false;
		}
		$tc = explode(',',$textcolor);
		$pdf->SetTextColor(intval($tc[0]),intval($tc[1]),intval($tc[2]));
		$fc = explode(',',$fillcolor);
		$pdf->SetFillColor(intval($fc[0]),intval($fc[1]),intval($fc[2]));
		$pdf->Cell($cellwidth,$cellheight,$fxsymbol.' '.number_format($totaldue,2),$border,$nextpos,$align,$df);
		$pdf->SetDrawColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetTextColor(0,0,0);
	}		


	$sql = "select * from ".$findb.".".$template." where item = 'signature'";
	$dbp->query($sql);
	$row = $dbp->single();
	extract($row);
	if ($include == 'Y' && $signature <> '') {
		$pdf->Image($signature,$xcoord,$ycoord,$cellwidth,$cellheight,'png');
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
$pdf->Output($fn,'I');
$pdf->close();

$dbp->closeDB();


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
	
		require_once '../includes/swift_email/swift_required.php';
		
		$t = $_SESSION['s_transport'];
		$te = explode('~',$t);
					
		$transport = Swift_SmtpTransport::newInstance($te[0], $te[1], $te[2])
		  ->setUsername($te[3])
		  ->setPassword($te[4])
		  ;
		
		// Create the Mailer using your created Transport
		$mailer = Swift_Mailer::newInstance($transport);
		

		$message = Swift_Message::newInstance();
		$message->setSubject('Quote '.$fname);
		$message->setFrom(array($email_from => $coyname));
		$message->setTo(array($email_to => $client));
		$mstring = "Dear Sir/Madam\r\n\r\n"."Invoice ".$fname." attached\r\n\r\n"."Regards\r\n".$coyname;
		$message->setBody($mstring);
		
		$attachment = Swift_Attachment::fromPath($fname, 'application/pdf');	
		$message->attach($attachment);
		
		$result = $mailer->send($message);
	}

}

?>
