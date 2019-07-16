<?php
ini_set('display_errors', true);

session_start();
$coyid = $_SESSION['s_coyid'];
$stkgrp = $_REQUEST['stkgrp'];
$stkcat = $_REQUEST['stkcat'];
$stkloc = $_REQUEST['stkloc'];
$randomqty = $_REQUEST['randomqty'];

$usersession = $_SESSION['usersession'];

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];
$subscriber = $subid;
$sname = $row['uname'];

$stfile1 = 'ztmp'.$user_id.'_stocktake1';
$stfile2 = 'ztmp'.$user_id.'_stocktake2';

$findb = $_SESSION['s_findb'];

$db->query("drop table if exists ".$findb.".".$stfile1);
$db->execute();
$db->query("drop table if exists ".$findb.".".$stfile2);
$db->execute();

$db->query("create table ".$findb.".".$stfile1." (groupid int(11),catid int(11),itemcode varchar(30),item varchar(45),location varchar(25),instock decimal(16,3)) engine myisam"); 
$db->execute();
$db->query("create table ".$findb.".".$stfile2." (groupid int(11),catid int(11),itemcode varchar(30),item varchar(45),location varchar(25),instock decimal(16,3)) engine myisam"); 
$db->execute();

if ($stkgrp == '*' && $stkcat == '*' && $stkloc == '*') {
	$db->query("insert into ".$findb.".".$stfile1." select distinct stktrans.groupid,stktrans.catid,stktrans.itemcode,stktrans.item,stklocs.location,0 from ".$findb.".stktrans,".$findb.".stklocs,".$findb.".stkmast where stktrans.locid = stklocs.uid and stktrans.itemcode = stkmast.itemcode and stkmast.stock = 'Stock'");
}
if ($stkgrp == '*' && $stkcat == '*' && $stkloc <> '*') {
	$db->query("insert into ".$findb.".".$stfile1." select distinct stktrans.groupid,stktrans.catid,stktrans.itemcode,stktrans.item,stklocs.location,0 from ".$findb.".stktrans,".$findb.".stklocs,".$findb.".stkmast where stktrans.locid = stklocs.uid and stktrans.itemcode = stkmast.itemcode and stkmast.stock = 'Stock' and stklocs.uid = ".$stkloc);
}
if ($stkgrp <> '*' && $stkcat == '*' && $stkloc == '*') {
	$db->query("insert into ".$findb.".".$stfile1." select distinct stktrans.groupid,stktrans.catid,stktrans.itemcode,stktrans.item,stklocs.location,0 from ".$findb.".stktrans,".$findb.".stklocs,".$findb.".stkmast where stktrans.locid = stklocs.uid and stktrans.itemcode = stkmast.itemcode and stkmast.stock = 'Stock' and stktrans.groupid = ".$stkgrp);
}
if ($stkgrp <> '*' && $stkcat == '*' && $stkloc <> '*') {
	$db->query("insert into ".$findb.".".$stfile1." select distinct stktrans.groupid,stktrans.catid,stktrans.itemcode,stktrans.item,stklocs.location,0 from ".$findb.".stktrans,".$findb.".stklocs,".$findb.".stkmast where stktrans.locid = stklocs.uid and stktrans.itemcode = stkmast.itemcode and stkmast.stock = 'Stock' and stklocs.uid = ".$stkloc." and stktrans.groupid = ".$stkgrp);
}
if ($stkgrp == '*' && $stkcat <> '*' && $stkloc == '*') {
	$db->query("insert into ".$findb.".".$stfile1." select distinct stktrans.groupid,stktrans.catid,stktrans.itemcode,stktrans.item,stklocs.location,0 from ".$findb.".stktrans,".$findb.".stklocs,".$findb.".stkmast where stktrans.locid = stklocs.uid and stktrans.itemcode = stkmast.itemcode and stkmast.stock = 'Stock' and stktrans.catid = ".$stkcat);
}
if ($stkgrp == '*' && $stkcat <> '*' && $stkloc <> '*') {
	$db->query("insert into ".$findb.".".$stfile1." select distinct stktrans.groupid,stktrans.catid,stktrans.itemcode,stktrans.item,stklocs.location,0 from ".$findb.".stktrans,".$findb.".stklocs,".$findb.".stkmast where stktrans.locid = stklocs.uid and stktrans.itemcode = stkmast.itemcode and stkmast.stock = 'Stock' and stklocs.uid = ".$stkloc." and stktrans.catid = ".$stkcat);
}
if ($stkgrp <> '*' && $stkcat <> '*' && $stkloc == '*') {
	$db->query("insert into ".$findb.".".$stfile1." select distinct stktrans.groupid,stktrans.catid,stktrans.itemcode,stktrans.item,stklocs.location,0 from ".$findb.".stktrans,".$findb.".stklocs,".$findb.".stkmast where stktrans.locid = stklocs.uid and stktrans.itemcode = stkmast.itemcode and stkmast.stock = 'Stock' and stktrans.groupid = ".$stkgrp." and stktrans.catid = ".$stkcat);
}
if ($stkgrp <> '*' && $stkcat <> '*' && $stkloc <> '*') {
	$db->query("insert into ".$findb.".".$stfile1." select distinct stktrans.groupid,stktrans.catid,stktrans.itemcode,stktrans.item,stklocs.location,0 from ".$findb.".stktrans,".$findb.".stklocs,".$findb.".stkmast where stktrans.locid = stklocs.uid and stktrans.itemcode = stkmast.itemcode and stkmast.stock = 'Stock' and stklocs.uid = ".$stkloc." and stktrans.groupid = ".$stkgrp." and stktrans.catid = ".$stkcat);
}

$db->execute();

// add uid field
$db->query("alter table ".$findb.".".$stfile1." add `uid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY");
$db->execute();
$db->query("alter table ".$findb.".".$stfile2." add `uid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY");
$db->execute();


// if random selection required
if ($randomqty > 0) {
	$db->query("SELECT FLOOR(RAND() * COUNT(*)) AS `offset` FROM ".$findb.".".$stfile1);
	$row = $db->single();
	extract($row);
	$db->query("SELECT * FROM ".$findb.".".$stfile1." LIMIT ".$offset.", ".$randomqty);
	$rows = $db->resultset(); 
	foreach ($rows as $row) {
		extract($row);
		$db->query("insert into ".$findb.".".$stfile2." (uid,groupid,catid,itemcode,item,location) values (".$uid.",".$groupid.",".$catid.",'".$itemcode."','".$item."','".$location."')");
		$db->execute(); 
	}
} else {
	
	$db->query("insert into ".$findb.".".$stfile2." select * from ".$findb.".".$stfile1);
	$db->execute();
}

// get number of items in stock for chosen codes.
$db->query("select uid,itemcode from ".$findb.".".$stfile2);
$rows = $db->resultset();
	foreach ($rows as $row) {
		extract($row);
		$ic = $itemcode;
		$id = $uid;
		if ($stkloc == '*') {
			$db->query("select sum(increase-decrease) as onhand from ".$findb.".stktrans where itemcode = '".$ic."'");
		} else {
			$db->query("select sum(increase-decrease) as onhand from ".$findb.".stktrans where itemcode = '".$ic."' and locid = ".$stkloc);
		}
		$row = $db->single();
		extract($row);
		$db->query("update ".$findb.".".$stfile2." set instock = ".$onhand." where uid = ".$id);
		$db->execute();
}


if ($stkgrp == '*') {
	$grp = 'All Groups';
} else {
	$db->query("select groupname from ".$findb.".stkgroup where groupid = ".$stkgrp);
	$row = $db->single();
	extract($row);
	$grp = "Group - ".$groupname;
}
if ($stkcat == '*') {
	$cat = 'All Categories';
} else {
	$db->query("select category from ".$findb.".stkcategory where catid = ".$stkcat);
	$row = $db->single();
	extract($row);
	$cat = "Category - ".$category;
}
if ($stkloc == '*') {
	$loc = 'All Locations';
} else {
	$db->query("select location from ".$findb.".stklocs where uid = ".$stkloc);
	$row = $db->single();
	extract($row);
	$loc = "Location - ".$location;
}

$dt = date('d/m/Y');


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

    function _beginpage($orientation,$size)
    {
        parent::_beginpage($orientation,$size);
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

$coyname = $_SESSION['s_coyname'];
$coyid = $_SESSION['s_coyid'];

$fontname = 'Arial';
$attrib = '';
$ftsize = 10;

$lastpage = 'N';
$currentline = 0;

$pdf=new PDF("P","mm","A4");

// Stock Take copy

$pdf->StartPageGroup();
$pdf->AddPage();
$pdf->SetFont("Arial","",10);
$pdf->SetLeftMargin(0);

$hfontname = "Arial";
$hattrib = "";
$hfontsize = 10;

$pdf->SetXY(10,10);
$pdf->SetFont($hfontname,$hattrib,$hfontsize);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$hed = "Stock Take List for ".$coyname.", ".$grp.", ".$cat.", ".$loc;
$pdf->Cell(194,5,$hed,1,0,"L",1);

$pdf->SetXY(110,15);
$hed = "Prepared on ".$dt;
$pdf->Cell(94,5,$hed,0,0,"R",1);

$pdf->SetXY(10,20);
$hed = "Stock Take Copy";
$pdf->SetFont($hfontname,'B',14);
$pdf->Cell(194,5,$hed,0,0,"C",1);
$pdf->SetFont($hfontname,$hattrib,$hfontsize);

$xcoord = 10;
$ycoord = 25;
$lineno = 1;

$ycoord = $ycoord + 6;
$pdf->SetXY($xcoord,$ycoord);
$pdf->SetFont($hfontname,'B',$hfontsize);
$pdf->Cell(30,4,'Item Code',0,0,"L",0);
$pdf->SetXY($xcoord + 31,$ycoord);
$pdf->Cell(75,4,'Item',0,0,"L",0);
$pdf->SetXY($xcoord + 106,$ycoord);
$pdf->Cell(89,4,'Number Counted',0,0,"L",0);

$db->query("select itemcode,item from ".$findb.".".$stfile2);
$rows = $db->resultset();
$numrecs = count($rows);
foreach ($rows as $row) {
	extract($row);

	$ycoord = $ycoord + 6;
	$pdf->SetXY($xcoord,$ycoord);
	$pdf->SetFont($hfontname,$hattrib,$hfontsize);
	$pdf->Cell(30,6,$itemcode,1,0,"L",0);
	$pdf->SetXY($xcoord + 31,$ycoord);
	$pdf->Cell(75,6,$item,1,0,"L",0);
	$pdf->SetXY($xcoord + 107,$ycoord);
	$pdf->Cell(88,6,'',1,0,"L",0);
		
	$currentline = $currentline + 1;
	$lineno = $lineno + 1;
	if ($currentline > 60 && $lineno < $numrecs) {
		$lastpage = 'N';
		$pdf->AddPage();
		break;
	}
}


// Administration Copy

$pdf->StartPageGroup();
$pdf->AddPage();
$pdf->SetFont("Arial","",10);
$pdf->SetLeftMargin(0);

$hfontname = "Arial";
$hattrib = "";
$hfontsize = 10;

$pdf->SetXY(10,10);
$pdf->SetFont($hfontname,$hattrib,$hfontsize);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$hed = "Stock Take List for ".$coyname.", ".$grp.", ".$cat.", ".$loc;
$pdf->Cell(194,5,$hed,1,0,"L",1);

$pdf->SetXY(110,15);
$hed = "Prepared on ".$dt;
$pdf->Cell(94,5,$hed,0,0,"R",1);

$pdf->SetXY(10,20);
$hed = "Administration Copy";
$pdf->SetFont($hfontname,'B',14);
$pdf->Cell(194,5,$hed,0,0,"C",1);
$pdf->SetFont($hfontname,$hattrib,$hfontsize);

$xcoord = 10;
$ycoord = 25;
$lineno = 1;

$ycoord = $ycoord + 6;
$pdf->SetXY($xcoord,$ycoord);
$pdf->SetFont($hfontname,'B',$hfontsize);
$pdf->Cell(30,4,'Item Code',0,0,"L",0);
$pdf->SetXY($xcoord + 31,$ycoord);
$pdf->Cell(75,4,'Item',0,0,"L",0);
$pdf->SetXY($xcoord + 106,$ycoord);
$pdf->Cell(30,4,'Number Counted',0,0,"L",0);
$pdf->SetXY($xcoord + 137,$ycoord);
$pdf->Cell(30,4,'System Holds',0,0,"L",0);
$pdf->SetXY($xcoord + 168,$ycoord);
$pdf->Cell(29,4,'Difference',0,0,"L",0);

$db->query("select itemcode,item,instock from ".$findb.".".$stfile2);
$rows = $db->resultset();
$numrecs = count($rows);
foreach ($rows as $row) {
	extract($row);

	$ycoord = $ycoord + 6;
	$pdf->SetXY($xcoord,$ycoord);
	$pdf->SetFont($hfontname,$hattrib,$hfontsize);
	$pdf->Cell(30,6,$itemcode,1,0,"L",0);
	$pdf->SetXY($xcoord + 31,$ycoord);
	$pdf->Cell(75,6,$item,1,0,"L",0);
	$pdf->SetXY($xcoord + 107,$ycoord);
	$pdf->Cell(30,6,'',1,0,"L",0);
	$pdf->SetXY($xcoord + 137,$ycoord);
	$pdf->Cell(30,6,$instock,1,0,"L",0);
	$pdf->SetXY($xcoord + 167,$ycoord);
	$pdf->Cell(29,6,'',1,0,"L",0);
		
	$currentline = $currentline + 1;
	$lineno = $lineno + 1;
	if ($currentline > 60 && $lineno < $numrecs) {
		$lastpage = 'N';
		$pdf->AddPage();
		break;
	}
}

$db->closeDB();

$fname = "stktake.pdf";
$pdf->Output($fname,'I');
$pdf->close();


?>