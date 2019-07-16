<?php
session_start();
//ini_set('display_errors', true);
error_reporting(0);

define('FPDF_FONTPATH','../../includes/font/');
require('../../includes/fpdf/fpdf.php');

$user_id=$_REQUEST['coy'];

class PDF extends FPDF
{
//Load data
function LoadData($file)
{
    //Read file lines
    $lines=file($file);
    $data=array();
    foreach($lines as $line)
        $data[]=explode(';',chop($line));
    return $data;
}

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

//Colored table
function TableLayout($data)
{
    foreach($data as $row)
    {
		if ($row[2] == "X") {
			$this->SetLeftMargin(15);
			$this->Ln();		
		}//if
		
		switch ($row[0]) {
			case "H":
				$this->SetFillColor(0,120,199);
				$this->SetTextColor(255);
				$this->SetFont('Arial','B',12);
				$this->SetLeftMargin(15);
				$this->Cell(270,7,$row[3],1,0,'C',1);
				break;
			
			case "B":
				switch ($row[1]) {
					case "H":
						$this->SetLeftMargin(15);
						$w=array(40,50,30,10,40,15);   
						$this->SetTextColor(0);
						$this->SetFont('Arial','BU',10);			
						$this->SetFillColor(0);
						$this->Cell($w[0],6,$row[3],'0',0,'L',0);
						$this->Cell($w[1],6,$row[4],'0',0,'L',0);	
						$this->Cell($w[2],6,$row[5],'0',0,'L',0);	
						$this->Cell($w[3],6,$row[6],'0',0,'R',0);	
						$this->Cell($w[4],6,$row[7],'0',0,'L',0);	
						break;				
					case "D":
						$this->SetLeftMargin(15);
						$w=array(40,50,30,10,40,15);   
						$this->SetTextColor(0);
						$this->SetFont('Arial','',10);			
						$this->SetFillColor(0);
						$this->Cell($w[0],6,$row[3],'0',0,'L',0);
						$this->Cell($w[1],6,$row[4],'0',0,'L',0);	
						$this->Cell($w[2],6,$row[5],'0',0,'L',0);	
						$this->Cell($w[3],6,$row[6],'0',0,'R',0);	
						$this->Cell($w[4],6,$row[7],'0',0,'L',0);	
						break;				
					case "N":
						$this->SetLeftMargin(55);
						$w=array(60,40,40,15);  
						$this->SetTextColor(0);
						$this->SetFont('Arial','',10);			
						$this->SetFillColor(0);
						$this->Cell($w[0],6,$row[3],'0',0,'L',0);
						$this->Cell($w[1],6,$row[4],'0',0,'L',0);	
						$this->Cell($w[2],6,$row[5],'0',0,'L',0);	
						$this->Cell($w[3],6,$row[6],'0',0,'L',0);	
						break;				
				
				}
				break;
			}
			$this->SetLeftMargin(15);
			$this->Ln();
	    }
	}
}

$pdf=new PDF('L','mm','A4');

//Data loading
$path = $_SERVER['DOCUMENT_ROOT'];
$data=$pdf->LoadData($path."/infin8/clt/text/".'pdflist'.$user_id.'.txt');
$pdf->AliasNbPages();
$pdf->SetFont('Arial','',14);
$pdf->AddPage();
$pdf->TableLayout($data);



//Save PDF to file
$pdf->Output();

?>

