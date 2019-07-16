<?php
ini_set('display_errors', true);

session_start();
$coyid = $_SESSION['s_coyid'];
$comment = $_REQUEST['comment'];
$footnote = substr($comment,0,200);
$overdue = $_REQUEST['overdue'];
$fromdate = $_REQUEST['fromdate'];
$todate = $_REQUEST['todate'];
$period = $_REQUEST['period'];

$usersession = $_SESSION['usersession'];

$admindb = $_SESSION['s_admindb'];
require("../db.php");
mysql_select_db($admindb) or die(mysql_error());
$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);

$drfile = 'ztmp'.$user_id.'_statements';
$subdb = 'sub'.$subid.'.';

$moduledb = $_SESSION['s_findb'];
mysql_select_db($moduledb) or die(mysql_error());

define('FPDF_FONTPATH','../includes/font/');
require('../includes/fpdf/fpdf.php');

class PDF extends FPDF
{
	// private variables
	var $colonnes;
	var $format;
	var $angle=0;


    var $NewPageGroup;   // variable indicating whether a new group was requested
    var $PageGroups;     // variable containing the number of pages of the groups
    var $CurrPageGroup;  // variable containing the alias of the current page group

    // create a new page group; call this before calling AddPage()
    function StartPageGroup()
    {
        $this->NewPageGroup=true;
    }

    // current page in the group
    function GroupPageNo()
    {
        return $this->PageGroups[$this->CurrPageGroup];
    }

    // alias of the current page group -- will be replaced by the total number of pages in this group
    function PageGroupAlias()
    {
        return $this->CurrPageGroup;
    }

    function _beginpage($orientation)
    {
        parent::_beginpage($orientation);
        if($this->NewPageGroup)
        {
            // start a new group
            $n = sizeof($this->PageGroups)+1;
            $alias = "{nb$n}";
            $this->PageGroups[$alias] = 1;
            $this->CurrPageGroup = $alias;
            $this->NewPageGroup=false;
        }
        elseif($this->CurrPageGroup)
            $this->PageGroups[$this->CurrPageGroup]++;
    }

    function _putpages()
    {
        $nb = $this->page;
        if (!empty($this->PageGroups))
        {
            // do page number replacement
            foreach ($this->PageGroups as $k => $v)
            {
                for ($n = 1; $n <= $nb; $n++)
                {
                    $this->pages[$n]=str_replace($k, $v, $this->pages[$n]);
                }
            }
        }
        parent::_putpages();
    }





	//Page footer
/*	function Footer()
	{
		//Position at 1.5 cm from bottom
		$this->SetY(-15);
		//Arial italic 8
		$this->SetFont('Arial','I',8);
		//Page number
		$this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
	}
*/	
	function Footer()
	{
		$this->SetY(-20);
		$this->Cell(0, 6, 'Page '.$this->GroupPageNo().'/'.$this->PageGroupAlias(), 0, 0, 'C');
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


//**************************************************************************************************
// get static heading data
//**************************************************************************************************

$template = 'stmtemplate';

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
$gstabn = 'GST No. '.$gstno;

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
//$pdf->AliasNbPages();
//$pdf->AddPage();
$pdf->SetFont($fontname,$attrib,$ftsize);
$pdf->SetLeftMargin(0);

//***************************************************************************
function heading() 
//***************************************************************************
{
	global $template, $pdf, $fromadd, $toadd, $gstabn, $reference, $dt, $toadd, $notes;


	//$ymd = explode('-',$ddate);
	//$dt = $ymd[2].'/'.$ymd[1].'/'.$ymd[0];	
	//$dt = date("j F,Y",strtotime($ddate));

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
		
		$pdf->Cell($cellwidth,$cellheight,$coyname,$border,$nextpos,$align,$fill);
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
	global $template, $pdf, $lastpage, $currentline, $ac, $sb, $lastpage, $fdate, $tdate, $period;

	// get opening balance
	$qb = "select sum(debit-credit) as ob from trmain where accountno = ".$ac." and sub = ".$sb." and ddate <= '".$fdate."'";
	$rb = mysql_query($qb) or die(mysql_error().' '.$qb);
	$row = mysql_fetch_array($rb);
	extract($row);
	if (is_null($ob)) {
		$obal = 0;
	} else {
		$obal = $ob;
	}

	if ($period == 'f') {
		$q = "select ddate, ref_no, gldesc, totvalue, tax, (totvalue + tax) as total, drcr from invhead where accountno = ".$ac." and sub = ".$sb." and ddate > '".$fdate."'";
	} else {
		$q = "select ddate, ref_no, gldesc, totvalue, tax, (totvalue + tax) as total, drcr from invhead where accountno = ".$ac." and sub = ".$sb." and ddate > '".$fdate."' and ddate <= '".$tdate."'";
	}
	$rdetails = mysql_query($q) or die(mysql_error().' '.$q);
	$numlines = mysql_num_rows($rdetails);
	$nl = $numlines;
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
	
	$query = "select * from ".$template." where item = 'obdate'";
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
		
		$pdf->Cell($cellwidth,$cellheight,$fdate,$border,$nextpos,$align,$fill);
	}

	$query = "select * from ".$template." where item = 'obref'";
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

	$query = "select * from ".$template." where item = 'obtot'";
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
		
		if ($obal < 0) {
			$pdf->Cell($cellwidth,$cellheight,number_format($obal * -1,2).' CR',$border,$nextpos,$align,$fill);
		} else {
			$pdf->Cell($cellwidth,$cellheight,number_format($obal,2).' DR',$border,$nextpos,$align,$fill);
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
			
			if ($pdf->GroupPageNo() == 1) {
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
							$texte = number_format($row[$n],2).' '.$row[6];
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
	global $template, $pdf, $reference, $remmitance, $comment, $curbal, $d30bal, $d60bal, $d90bal, $d120bal, $footnote;

	$totaldue = $curbal + $d30bal + $d60bal + $d90bal + $d120bal;

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
		
		$pdf->MultiCell($cellwidth,$cellheight,$footnote);
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
		
		$dc = 'DR';
		if ($curbal < 0) {
			$curbal = $curbal * -1;
			$dc = 'CR';
		}
		$pdf->Cell($cellwidth,$cellheight,number_format($curbal,2).' '.$dc,$border,$nextpos,$align,$fill);
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
		
		$dc = 'DR';
		if ($d30bal < 0) {
			$d30bal = $d30bal * -1;
			$dc = 'CR';
		}
		$pdf->Cell($cellwidth,$cellheight,number_format($d30bal,2).' '.$dc,$border,$nextpos,$align,$fill);
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
		
		$dc = 'DR';
		if ($d60bal < 0) {
			$d60bal = $d60bal * -1;
			$dc = 'CR';
		}
		$pdf->Cell($cellwidth,$cellheight,number_format($d60bal,2).' '.$dc,$border,$nextpos,$align,$fill);
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
		
		$dc = 'DR';
		if ($d90bal < 0) {
			$d90bal = $d90bal * -1;
			$dc = 'CR';
		}
		$pdf->Cell($cellwidth,$cellheight,number_format($d90bal,2).' '.$dc,$border,$nextpos,$align,$fill);
	}
	
	
	
	$query = "select * from ".$template." where item = 'footlabel10'";
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
		
		$dc = 'DR';
		if ($d120bal < 0) {
			$d120bal = $d120bal * -1;
			$dc = 'CR';
		}
		$pdf->Cell($cellwidth,$cellheight,number_format($d120bal,2).' '.$dc,$border,$nextpos,$align,$fill);
	}
	
	$query = "select * from ".$template." where item = 'totlabel'";
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
	
	$query = "select * from ".$template." where item = 'totbal'";
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
		
		$dc = 'DR';
		if ($totaldue < 0) {
			$totaldue = $totaldue * -1;
			$dc = 'CR';
		}
		$pdf->Cell($cellwidth,$cellheight,number_format($totaldue,2).' '.$dc,$border,$nextpos,$align,$fill);
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


//***************************************************************************
// programme flow 
//***************************************************************************

$qf = "select * from ".$drfile." where sendby = 'Post'";
$rf = mysql_query($qf) or die(mysql_error());
while ($row = mysql_fetch_array($rf)) {
	extract($row);
	$toadd = trim($debtor)."\n";
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

	if ($period == 'c' || $period == 'f') {
		$curbal = $current;
		$d30bal = $d30;
		$d60bal = $d60;
		$d90bal = $d90;
		$d120bal = $d120;
	} else {
		$curbal = $d30;
		$d30bal = $d60;
		$d60bal = $d90;
		$d90bal = $d120;
		$d120bal = 0;
	}
	
	$ac = $account;
	$sb = $sub;
	$reference = $account.' '.$sub;
	$notes = "Between ".$fromdate." and ".$todate;
	$dt = date("d/m/Y");
	
	$f = explode('/',$fromdate);
	$y = $f[2];
	$m = $f[1];
	$d = $f[0];
	$fdate = $y.'-'.$m.'-'.$d;
	
	$t = explode('/',$todate);
	$y = $t[2];
	$m = $t[1];
	$d = $t[0];
	$tdate = $y.'-'.$m.'-'.$d;

	$lastpage = 'N';
	$currentline = 0;
	
	$pdf->StartPageGroup();
	$pdf->addpage();

	heading();
	details();
	footer();

}

$fname = 'trading_docs/'.$coyid.'/'."stp.pdf";
$fn = "stp.pdf";
$pdf->Output($fname,'F');
$pdf->Output($fn,'I');
$pdf->close();


?>