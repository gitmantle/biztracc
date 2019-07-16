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

$type = strtolower($_REQUEST['type']);

switch ($type) {
	case 'qot':
		$tradingref = 'QOT123';
		break;
	case 's_o':
		$tradingref = 'S_O123';
		break;
	case 'p_o':
		$tradingref = 'P_O123';
		break;
	case 'grn':
		$tradingref = 'GRN123';
		break;
	case 'inv':
		$tradingref = 'INV123';
		break;
	case 'c_p':
		$tradingref = 'C_P123';
		break;
	case 'c_s':
		$tradingref = 'C_S123';
		break;
	case 'rec':
		$tradingref = 'REC123';
		break;
	case 'crn':
		$tradingref = 'CRN123';
		break;
	case 'pay':
		$tradingref = 'PAY123';
		break;
	case 'ret':
		$tradingref = 'RET123';
		break;
	case 'req':
		$tradingref = 'REQ123';
		break;
	case 'pkl':
		$tradingref = 'S_O123';
		break;
	case 'd_n':
		$tradingref = 'D_N123-1';
		break;
}




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
if (isset($_REQUEST['bcc'])) {
	$bcc = $_REQUEST['bcc'];
} else {
	$bcc = 'N';	
}

include_once("../includes/DBClass.php");
$dbp = new DBClass();

$findb = $_SESSION['s_findb'];
$cltdb = $_SESSION['s_cltdb'];

$sql = "select * from ".$findb.".globals";
$dbp->query($sql);
$row = $dbp->single();
extract($row);

$fromadd = $ad1." ".$ad2."\n";
$fromadd .= $ad3."\n";
$fromadd .= "Phone: ".$telno."\n";
if (trim($faxno) != '') {
	$fromadd .= "Fax:   ".$faxno."\n";
}
$fromadd .= "email: ".$email;
$email_from = $email;
$coyname = $_SESSION['s_coyname'];
$coyid = $_SESSION['s_coyid'];

$toadd = 'Joe Bloggs'."\n".'67 New Street'."\n".'Green Lane'."\n".'New Town'."\n".'PCODE';
$client = 'Joe Bloggs';

$trd = strtoupper($type);
$refarray = array('CHQ','CRD','EBI','EBO','OTH');

/*
if ($draccno > 30000000 and in_array($trd,$refarray)) {
	$type = 'REC';
}
if (($draccno > 20000000 and $draccno < 30000000) and in_array($trd,$refarray)) {
	$type = 'PAY';
}
*/

if (strtoupper($type) == 'INV') {
	$template = 'invtemplate';
	$reference = '30000001 - 0';
}
if (strtoupper($type) == 'C_S') {
	$template = 'c_stemplate';
	$reference = '30000001 - 0';
}
if (strtoupper($type) == 'C_P') {
	$template = 'c_ptemplate';
	$reference = '20000001 - 0';
}
if (strtoupper($type) == 'PUR') {
	$template = 'c_ptemplate';
	$reference = '20000001 - 0';
}
if (strtoupper($type) == 'CRN') {
	$template = 'crntemplate';
	$reference = '30000001 - 0';
}
if (strtoupper($type) == 'GRN') {
	$template = 'grntemplate';
	$reference = '20000001 - 0';
}
if (strtoupper($type) == 'RET') {
	$template = 'rettemplate';
	$reference = '20000001 - 0';
}
if (strtoupper($type) == 'REC') {
	$template = 'rectemplate';
	$reference = '30000001 - 0';
}
if (strtoupper($type) == 'PAY') {
	$template = 'paytemplate';
	$reference = '30000001 - 0';
}
if (strtoupper($type) == 'REQ') {
	$template = 'reqtemplate';
	$reference = '00000001 - 0';
}
if (strtoupper($type) == 'S_O') {
	$template = 's_otemplate';
	$reference = '20000001 - 0';
}
if (strtoupper($type) == 'P_O') {
	$template = 'p_otemplate';
	$reference = '30000001 - 0';
}
if (strtoupper($type) == 'D_N') {
	$template = 'd_ntemplate';
	$reference = '20000001 - 0';
}
if (strtoupper($type) == 'PKL') {
	$template = 'pkltemplate';
	$reference = '20000001 - 0';
}
if (strtoupper($type) == 'QOT') {
	$template = 'qottemplate';
	$reference = '30000001 - 0';
}

$_SESSION['watermark'] = 'N'; //remove and ensure it is set in each trading form.

switch ($type) {
	case 'qot':
		$dbp->query("select * from templatequotes where ref_no = :tradingref");
		$dbp->bind(':tradingref', $tradingref);
		break;
	case 'p_o':	
		$dbp->query("select * from templatep_ohead where ref_no = :tradingref");
		$dbp->bind(':tradingref', $tradingref);
		
	default:
		$dbp->query("select * from templateinvhead where ref_no = :tradingref");
		$dbp->bind(':tradingref', $tradingref);
		break;
	
}

$row = $dbp->single();
extract($row);

/*
if ($client == "" && ($type != 'C_P' && $type != 'C_S' && $type != 'REQ' && $type != 'P_O')) {
	if ($type == 'INV' || $type == 'REC' || $type == 'CRN' || $type == 'D_N' || $type == 'S_O') {
		$sql = "select concat(".$cltdb.".members.firstname,' ',".$cltdb.".members.lastname) as account from ".$cltdb.".members left join ".$cltdb.".client_company_xref on ".$cltdb.".members.member_id = ".$cltdb.".client_company_xref.client_id where ".$cltdb.".client_company_xref.drno = :draccno and ".$cltdb.".client_company_xref.drsub = :drsubno";
		$dbp->query($sql);
		$dbp->bind(':draccno', $draccno);
		$dbp->bind(':drsubno', $drsubno);
	} else {
		$sql = "select concat(".$cltdb.".members.firstname,' ',".$cltdb.".members.lastname) as account from ".$cltdb.".members left join ".$cltdb.".client_company_xref on ".$cltdb.".members.member_id = ".$cltdb.".client_company_xref.client_id where ".$cltdb.".client_company_xref.crno = :draccno and ".$cltdb.".client_company_xref.crsub = :drsubno";
		$dbp->query($sql);
		$dbp->bind(':draccno', $draccno);
		$dbp->bind(':drsubno', $drsubno);
	}
	
	$row = $dbp->single();
	extract($row);
	$aclient = trim($account);
}




if ($postaladdress == "" && ($type != 'C_P' && $type != 'C_S')) {
	if ($type == 'INV' || $type == 'REC' || $type == 'CRN' || $type == 'D_N' || $type == 'S_O') {
		$dbp->query("select addresses.street_no,addresses.ad1,addresses.ad2,addresses.suburb,addresses.town,addresses.postcode,addresses.country from ".$cltdb.".addresses,".$cltdb.".client_company_xref where addresses.member_id = client_company_xref.client_id and addresses.billing = 'Y' and client_company_xref.drno = :draccno and client_company_xref.drsub = :drsubno");
		$dbp->bind(':draccno', $draccno);
		$dbp->bind(':drsubno', $drsubno);
} else {
		$dbp->query("select ".$cltdb.".addresses.street_no,".$cltdb.".addresses.ad1,".$cltdb.".addresses.ad2,".$cltdb.".addresses.suburb,".$cltdb.".addresses.town,".$cltdb.".addresses.postcode,".$cltdb.".addresses.country from ".$cltdb.".addresses,".$cltdb.".client_company_xref where ".$cltdb.".addresses.member_id = ".$cltdb.".client_company_xref.client_id and ".$cltdb.".addresses.billing = 'Y' and ".$cltdb.".client_company_xref.crno = :draccno and ".$cltdb.".client_company_xref.crsub = :drsubno");
		$dbp->bind(':draccno', $draccno);
		$dbp->bind(':drsubno', $drsubno);
}


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
	//if ($postcode <> '') {
		$toadd .= trim($postcode)."\n";
	//}
	if ($country <> '') {
		$toadd .= trim($country)."\n";
	}
	
}
*/

$deliveryaddress = '67 New Street'."\n".'Green Lane'."\n".'New Town'."\n".'PCODE';

$gstabn =  $_SESSION['s_tradtax'].' No. 76-67-09';


$email_to = "";

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
	global $template, $pdf, $fromadd, $toadd, $gstabn, $gldesc, $reference, $orient, $paper, $ddate, $tradingref, $postaladdress, $deliveryaddress, $signature, $lastpage, $findb, $dbp, $type, $your_ref;

	//$ymd = explode('-',$ddate);
	//$dt = $ymd[2].'/'.$ymd[1].'/'.$ymd[0];
	if ($type == 'PKL') {
		$dt = date("j F, Y");
	} else {
		$dt = date("j F, Y",strtotime($ddate));
	}

	$sql = "select * from ".$findb.".".$template." where item = 'watermark'";
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
		$pdf->Rotate(45,55,190);
		$pdf->Text($xcoord,$ycoord,$content);
		$pdf->Rotate(0);
		$pdf->SetDrawColor(0,0,0);
		$pdf->SetFillColor(0);$pdf->SetTextColor(0,0,0);
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
		$pdf->SetFillColor(0);$pdf->SetTextColor(0,0,0);
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
		$pdf->SetFillColor(0);$pdf->SetTextColor(0,0,0);
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
		$pdf->SetFillColor(0);$pdf->SetTextColor(0,0,0);
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
	
	$sql = "select * from ".$findb.".".$template." where item = 'header3'";
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
		
		$pdf->Cell($cellwidth,$cellheight,$your_ref,$border,$nextpos,$align,$fill);
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
		$pdf->SetFillColor(0);$pdf->SetTextColor(0,0,0);
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
		$pdf->SetFillColor(0);$pdf->SetTextColor(0,0,0);
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
		$pdf->SetFillColor(0);$pdf->SetTextColor(0,0,0);
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
		$pdf->SetFillColor(0);$pdf->SetTextColor(0,0,0);
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
		$pdf->SetFillColor(0);$pdf->SetTextColor(0,0,0);
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
		$pdf->SetFillColor(0);$pdf->SetTextColor(0,0,0);
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
		$pdf->SetFillColor(0);$pdf->SetTextColor(0,0,0);
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
		$pdf->SetFillColor(0);$pdf->SetTextColor(0,0,0);
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
		$pdf->SetFillColor(0);$pdf->SetTextColor(0,0,0);
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
		$pdf->SetFillColor(0);$pdf->SetTextColor(0,0,0);
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
		$pdf->SetFillColor(0);$pdf->SetTextColor(0,0,0);
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
		$pdf->SetFillColor(0);$pdf->SetTextColor(0,0,0);
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
		$pdf->SetFillColor(0);$pdf->SetTextColor(0,0,0);
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
		$pdf->SetFillColor(0);$pdf->SetTextColor(0,0,0);
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
		$pdf->SetFillColor(0);$pdf->SetTextColor(0,0,0);
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
		$pdf->SetFillColor(0);$pdf->SetTextColor(0,0,0);
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
		$pdf->SetFillColor(0);$pdf->SetTextColor(0,0,0);
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
		$pdf->SetFillColor(0);$pdf->SetTextColor(0,0,0);
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
		$pdf->SetFillColor(0);$pdf->SetTextColor(0,0,0);
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
		$pdf->SetFillColor(0);$pdf->SetTextColor(0,0,0);
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
		$pdf->SetFillColor(0);$pdf->SetTextColor(0,0,0);
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
		$pdf->Cell($cellwidth,$cellheight,$dt,$border,$nextpos,$align,$df);
		$pdf->SetDrawColor(0,0,0);
		$pdf->SetFillColor(0);$pdf->SetTextColor(0,0,0);
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
		$pdf->SetFillColor(0);$pdf->SetTextColor(0,0,0);
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
		$pdf->Cell($cellwidth,$cellheight,$gstabn,$border,$nextpos,$align,$df);
		$pdf->SetDrawColor(0,0,0);
		$pdf->SetFillColor(0);$pdf->SetTextColor(0,0,0);
	}	
	
	$sql = "select * from ".$findb.".".$template." where item = 'gldesc'";
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
		$pdf->Cell($cellwidth,$cellheight,$gldesc,$border,$nextpos,$align,$df);
		$pdf->SetDrawColor(0,0,0);
		$pdf->SetFillColor(0);$pdf->SetTextColor(0,0,0);
	}	
	
} // function heading()


//******************************************************************************
function details()
//******************************************************************************
{
	global $template, $pdf, $lastpage, $currentline, $tradingref, $findb, $dbp, $type;
	
	switch ($type) {
		case 'qot':
			$dbp->query("select itemcode,item,quantity,price,value,discount,tax,(value+tax) as total, note from templatequotelines where quote_id = 123");
			$linedetails = $dbp->resultsetNum();
			break;
		case 'pkl':
			$dbp->query("select itemcode,item,unit,(quantity - returns) as required,' ' from templateinvtrans where ref_no = :tradingref");
			$dbp->bind(':tradingref', $tradingref);
			$linedetails = $dbp->resultsetNum();
			break;
		case 'd_n':
			$dbp->query("select itemcode,item,quantity,unit from templateinvtrans where ref_no = :tradingref");
			$dbp->bind(':tradingref', $tradingref);
			$linedetails = $dbp->resultsetNum();
			break;
		case 'p_o':
			$dbp->query("select itemcode,item,quantity,unit from templatep_olines where ref_no = :tradingref");
			$dbp->bind(':tradingref', $tradingref);
			$linedetails = $dbp->resultsetNum();
			break;
		
		default:
			$dbp->query("select itemcode,item,quantity,price,value,discount,tax,(value+tax) as total from templateinvtrans where ref_no = :tradingref");
			$dbp->bind(':tradingref', $tradingref);
			$linedetails = $dbp->resultsetNum();
			break;
		
	}


		$pdf->SetFillColor(0);

	
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
					
					$dbp->query("select itemcode,serialno from ".$findb.".stkserials where sold = '".$tradingref."'");
					
					$rows = $dbp->resultset();
					if (count($rows) > 0) {
						$sn = 'Serial Nos: ';
						foreach ($rows as $row) {
							extract($row);
							$sn = $sn.$serialno.", ";
						}
						$sn = substr($sn,0,-2);
						
						//if this item has serial numbers , print them
						if ($itemcode == $rowl[0]) {
							$currentline = $i + 1;
							$pdf->SetXY( 15, $ypos + 4);
							$pdf->MultiCell( 140, $cellheight , $sn, $border, 'L');
							$pdf->Ln();
							if ( $maxypos < ($pdf->GetY()  ) ) {
								$maxypos = $pdf->GetY() ;
							}
						}
					}
					
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
	global $template, $pdf, $reference, $tradingref, $remmitance, $signature, $findb, $dbp, $type;
	
	if ($type == 'qot') {
		$dbp->query("select totvalue, tax, currency from templatequotes where ref_no = :tradingref");
		$dbp->bind(':tradingref', $tradingref);
		$row = $dbp->single();
		extract($row);
		$totalvalue = $totvalue;
		$totaltax = $tax;
		$totaldue = $totalvalue + $tax;
		$fxcode = $currency;
		
	} else {
	
		$dbp->query("select totvalue, tax, currency from templateinvhead where ref_no = :tradingref");
		$dbp->bind(':tradingref', $tradingref);
		$row = $dbp->single();
		extract($row);
		$totalvalue = $totvalue;
		$totaltax = $tax;
		$totaldue = $totalvalue + $tax;
		$fxcode = $currency;
	}
	
	// get currency
	$dbp->query("select descript,symbol from ".$findb.".forex where currency = :currency");
	$dbp->bind(':currency', $fxcode);
	$row = $dbp->single();
	extract($row);
	$fxdescript = $descript;
	$fxsymbol = $symbol;
	

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
		$pdf->MultiCell($cellwidth,$cellheight,$content,$border,'J',$df);
		$pdf->SetDrawColor(0,0,0);
		$pdf->SetFillColor(0);$pdf->SetTextColor(0,0,0);
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
		$pdf->SetFillColor(0);$pdf->SetTextColor(0,0,0);
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
		$pdf->SetFillColor(0);$pdf->SetTextColor(0,0,0);
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
		$pdf->SetFillColor(0);$pdf->SetTextColor(0,0,0);
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
		$pdf->SetFillColor(0);$pdf->SetTextColor(0,0,0);
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
		$pdf->SetFillColor(0);$pdf->SetTextColor(0,0,0);
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
		$pdf->SetFillColor(0);$pdf->SetTextColor(0,0,0);
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
		$pdf->SetFillColor(0);$pdf->SetTextColor(0,0,0);
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
		$pdf->SetFillColor(0);$pdf->SetTextColor(0,0,0);
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
		$pdf->SetFillColor(0);$pdf->SetTextColor(0,0,0);
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
		//$fxval = $fxcode.'  '.number_format($totalvalue,2);
		$pdf->Cell($cellwidth,$cellheight,$fxsymbol.' '.number_format($totalvalue,2),$border,$nextpos,$align,$df);
		$pdf->SetDrawColor(0,0,0);
		$pdf->SetFillColor(0);$pdf->SetTextColor(0,0,0);
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
		$pdf->SetFillColor(0);$pdf->SetTextColor(0,0,0);
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
		$pdf->SetFillColor(0);$pdf->SetTextColor(0,0,0);
	}		

	$sql = "select * from ".$findb.".".$template." where item = 'signature'";
	$dbp->query($sql);
	$row = $dbp->single();
	extract($row);
	if ($include == 'Y' && $signature <> '') {
		$pdf->Image($signature,$xcoord,$ycoord,$cellwidth,$cellheight,'png');
	}	
	

	//Remittance Advice  slip
	
	if ($remmitance == 'Y') {
		$pdf->Line(20,252,200,252);
		$sql = "select * from ".$findb.".".$template." where item = 'remmitance'";
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
			
			$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);		
			
			$sql = "select * from ".$findb.".".$template." where item = 'remitbox'";
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
			
			$sql = "select * from ".$findb.".".$template." where item = 'remitlabel1'";
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
				
				$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
			}			
				
			$sql = "select * from ".$findb.".".$template." where item = 'remitlabel2'";
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
				
				$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
			}			
				
			$sql = "select * from ".$findb.".".$template." where item = 'remitlabel3'";
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
				
				$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
			}			
				
			$sql = "select * from ".$findb.".".$template." where item = 'remitlabel4'";
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
				
				$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
			}			
				
			$sql = "select * from ".$findb.".".$template." where item = 'remitlabel5'";
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
				
				$pdf->Cell($cellwidth,$cellheight,$content,$border,$nextpos,$align,$fill);
			}

			$sql = "select * from ".$findb.".".$template." where item = 'rtotdue'";
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
				
				$pdf->Cell($cellwidth,$cellheight,number_format($totaldue,2),$border,$nextpos,$align,$fill);
			}

			$sql = "select * from ".$findb.".".$template." where item = 'ramtpaid'";
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
				
				$pdf->Cell($cellwidth,$cellheight,'',$border,$nextpos,$align,$fill);
			}

			$sql = "select * from ".$findb.".".$template." where item = 'rref1'";
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
	
	$lastpage = 'Y';
	
}
footer();

$fname = 'trading_docs/'.$coyid.'/'.$tradingref.".pdf";
$fn = $tradingref.".pdf";
$pdf->Output($fname,'F');
if ($noscreen = 'N') {
	$pdf->Output($fn,'I');
}
$pdf->close();

$dbp->closeDB();


?>
