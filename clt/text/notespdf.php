<?php

//ini_set('display_errors', true);



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
        $data[]=explode(';',chop($line)) ;
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
			$this->Cell(180,7,$row[3],1,0,'C',1);
			break;
		
		case "B":
			switch ($row[1]) {
			case "D":
				$this->SetLeftMargin(15);
			    $w=array(25,25,150);   
				$this->SetTextColor(0);
				$this->SetFont('Arial','',10);			
				$this->SetFillColor(0);
				$this->Cell($w[0],6,$row[3],'0',0,'L',0);
				$this->Cell($w[1],6,$row[4],'0',0,'L',0);	
				$this->Cell($w[2],6,$row[5],'0',0,'L',0);	
				break;				
			case "N":
				$this->SetLeftMargin(15);
			    $w=array(0);  
				$this->SetTextColor(0);
				$this->SetFont('Arial','',10);			
				$this->SetFillColor(0);
				$this->MultiCell($w[0],6,$row[3],'0','L',0);
				break;				
				
			}
			break;
		}
		$this->Ln();
    }
}
}

$pdf=new PDF();

//Data loading
$path = $_SERVER['DOCUMENT_ROOT'];
$data=$pdf->LoadData($path."/kenny2/clt/text/".'pdfnotes'.$user_id.'.txt');
$pdf->AliasNbPages();
$pdf->SetFont('Arial','',14);
$pdf->AddPage();
$pdf->TableLayout($data);



//Save PDF to file
$pdf->Output();

?>

