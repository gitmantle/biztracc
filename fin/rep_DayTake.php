<?php
session_start();
$usersession = $_SESSION['usersession'];

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];
$subscriber = $subid;

$coyid = $_SESSION['s_coyid'];
$coyname = $_SESSION['s_coyname'];
$bdate = $_REQUEST['bdate'];
$edate = $_REQUEST['edate'];
$tcash = $_REQUEST['tcash'];
$tfloat = $_REQUEST['tfloat'];
$dtake = $tcash - $tfloat;
$tcash = (string)$tcash;
$tfloat = (string)$tfloat;
$dtake = (string)$dtake;

$rdt = explode('-',$bdate);
$fy = $rdt[0];
$fm = $rdt[1];
$fd = $rdt[2];
$fdate = $fd.'/'.$fm.'/'.$fy;

$ldt = explode('-',$edate);
$ey = $ldt[0];
$em = $ldt[1];
$ed = $ldt[2];
$ldate = $ed.'/'.$em.'/'.$ey;

$heading = "Day's Takings for ".$coyname." between ".$fdate." and ".$ldate;

$findb = $_SESSION['s_findb'];

$db->query('select sum(cash) as tcsh, sum(cheque) as tchq, sum(eftpos) as teft, sum(ccard) as tcrd from '.$findb.'.invhead where (ddate >= "'.$bdate.'" and ddate <= "'.$edate.'") and (transtype = "C_S" or transtype = "REC")');
$row = $db->single();
extract($row);

date_default_timezone_set($_SESSION['s_timezone']);
$dt = date('d/m/Y');

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
	function MaxLines($ldetails, $gwidths, $font, $attrib, $fontsize)
	//********************************************************************
	{

	$nlines = 0;
	$gtw = explode(',',$gwidths);
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

$pdf = new PDF("P","mm","A4");
$pdf->AliasNbPages();

$pdf->AddPage();
$pdf->SetFont('Arial','',10);
$pdf->SetLeftMargin(0);
			
$pdf->SetXY(20,9);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(150,9,$heading,0,0,"L",1);
$pdf->SetTextColor(0,0,0);

$x = 20;
$y = 15;

$pdf->SetXY($x,$y);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Arial','',10);
$pdf->Cell(50,9,"Total Cash on Hand",0,0,"L",1);
$pdf->SetTextColor(0,0,0);

$pdf->SetXY($x + 80,$y);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Arial','',10);
$pdf->Cell(20,9,number_format($tcash,2),0,0,'R');
$pdf->SetTextColor(0,0,0);

$y = $y + 7;
		
$pdf->SetXY($x,$y);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Arial','',10);
$pdf->Cell(50,9,"Less Float of",0,0,"L",1);
$pdf->SetTextColor(0,0,0);

$pdf->SetXY($x + 80,$y);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Arial','',10);
$pdf->Cell(20,9,number_format($tfloat,2),0,0,'R');
$pdf->SetTextColor(0,0,0);

$y = $y + 7;

$pdf->Line($x + 80,$y,$x + 100,$y);

$y = $y + 1;

$pdf->SetXY($x,$y);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Arial','',10);
$pdf->Cell(50,9,"Period Cash on Hand",0,0,"L",1);
$pdf->SetTextColor(0,0,0);

$pdf->SetXY($x + 80,$y);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Arial','',10);
$pdf->Cell(20,9,number_format($dtake,2),0,0,'R');
$pdf->SetTextColor(0,0,0);

$y = $y + 8;

$pdf->Line($x + 80,$y,$x + 100,$y);

$y = $y + 15;

$pdf->SetXY($x,$y);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Arial','',10);
$pdf->Cell(50,9,"Period Cash Takings",0,0,"L",1);
$pdf->SetTextColor(0,0,0);

$pdf->SetXY($x + 80,$y);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Arial','',10);
$pdf->Cell(20,9,number_format($tcsh,2),0,0,'R');
$pdf->SetTextColor(0,0,0);

$y = $y + 7;

$pdf->SetXY($x,$y);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Arial','',10);
$pdf->Cell(50,9,"Period Cheque Takings",0,0,"L",1);
$pdf->SetTextColor(0,0,0);

$pdf->SetXY($x + 80,$y);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Arial','',10);
$pdf->Cell(20,9,number_format($tchq,2),0,0,'R');
$pdf->SetTextColor(0,0,0);

$y = $y + 7;

$pdf->SetXY($x,$y);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Arial','',10);
$pdf->Cell(50,9,"Period Eftpos Takings",0,0,"L",1);
$pdf->SetTextColor(0,0,0);

$pdf->SetXY($x + 80,$y);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Arial','',10);
$pdf->Cell(20,9,number_format($teft,2),0,0,'R');
$pdf->SetTextColor(0,0,0);

$y = $y + 7;

$pdf->SetXY($x,$y);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Arial','',10);
$pdf->Cell(50,9,"Period Credit Card Takings",0,0,"L",1);
$pdf->SetTextColor(0,0,0);

$pdf->SetXY($x + 80,$y);
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Arial','',10);
$pdf->Cell(20,9,number_format($tcrd,2),0,0,'R');
$pdf->SetTextColor(0,0,0);

// get details from invhead

$lastpage = 'N';
$currentline = 0;

$db->query('select ref_no,gldesc,cash,cheque,eftpos,ccard from '.$findb.'.invhead where (ddate >= "'.$bdate.'" and ddate <= "'.$edate.'") and (transtype = "C_S" or transtype = "REC")');
$linedetails = $db->resultsetNum();
$numlines = count($linedetails);

$xcoord = 20;
$ycoord = $y + 15;
$gridwidths = '25,40,25,25,25,25';
$align = 'L,L,R,R,R,R';
$content = 'Reference,Description,Cash,Cheque,Eftpos,Card';

$fontname = 'Arial';
$attrib = "";
$ftsize = 10;
$pdf->SetXY($xcoord,$ycoord -4);
$pdf->SetFont($fontname,$attrib,$ftsize);
$gtw = explode(',',$gridwidths);
$galign = explode(',',$align);
$gt = explode(',',$content);
$gcount = count($gt) - 1;
$xpos = $xcoord;
$ypos = $ycoord +15;
$numlines = count($linedetails);
$gformat = explode(',',$content);
$cellheight = 4;
$border = 0;
$nextpos = 0;
$fill = 0;

for ($n = 0; $n <= $gcount; $n++) {
	$pdf->Cell($gtw[$n],$cellheight,$gt[$n],$border,$nextpos,$galign[$n],$fill);
}

$content = 'C,C,N,N,N,N';

		$maxypos = $ycoord + 15;
		
		while ($currentline < $numlines) {
			
			if ($pdf->PageNo() == 1) {
				$xpos = $xcoord;
				$ypos = $ycoord + 2;
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


$fname = 'DayTake_'.$edate.'.pdf';
$pdf->Output($fname,'I');

$db->closeDB();

?>
