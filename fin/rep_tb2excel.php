<?php
session_start();
$coyname = $_SESSION['s_coyname'];
$heading = 'Trial Balance '.$_SESSION['s_tbheading'];

$usersession = $_SESSION['usersession'];

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];

$tbfile = 'ztmp'.$user_id.'_tb';

$findb = $_SESSION['s_findb'];

$db->query("select AccountNumber,Branch,Branchname,Sub,AccountName,Debit,Credit,Lastyear from ".$findb.".".$tbfile);
$rows = $db->resultset();

/** Error reporting */
error_reporting(E_ALL);

/** PHPExcel */
require_once '../includes/phpexcel/Classes/PHPExcel.php';
//require_once '../includes/phpexcel/Classes/PHPExcel/Cell/AdvancedValueBinder.php';

// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

// Set properties
$objPHPExcel->getProperties()->setCreator("Murray Russell")
							 ->setLastModifiedBy("Murray Russell")
							 ->setTitle("Office 2007 XLSX Filtered Mail List")
							 ->setSubject("Office 2007 XLSX Filtered Mail List")
							 ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
							 ->setKeywords("office 2007 openxml php")
							 ->setCategory("Filtered Mail List");


// Header Rows
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', $heading)
            ->setCellValue('A2', 'Account No.')
            ->setCellValue('B2', 'Branch')
            ->setCellValue('C2', 'Branch Name')
            ->setCellValue('D2', 'Sub')
            ->setCellValue('E2', 'Account Name')
            ->setCellValue('F2', 'Debit')
            ->setCellValue('G2', 'Credit')
            ->setCellValue('H2', 'Last Year');
		
			
$styleArray = array(
	'font' => array(
		'bold' => true,
	)
);
$objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);	

$styleArray = array(
	'font' => array(
		'bold' => true,
	),
	'borders' => array(
		'top' => array(
			'style' => PHPExcel_Style_Border::BORDER_THIN,
		),
		'bottom' => array(
			'style' => PHPExcel_Style_Border::BORDER_THIN,
		)
	)
);
$objPHPExcel->getActiveSheet()->getStyle('A2:H2')->applyFromArray($styleArray);			

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(6);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(60);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);

$i = 3;
foreach ($rows as $row) {
	extract($row);
	$cell = 'A'.$i;
	$objPHPExcel->getActiveSheet()->setCellValue($cell, $AccountNumber);
	$cell = 'B'.$i;
	$objPHPExcel->getActiveSheet()->setCellValue($cell, $Branch);	
	$cell = 'C'.$i;
	$objPHPExcel->getActiveSheet()->setCellValue($cell, $Branchname);	
	$cell = 'D'.$i;
	$objPHPExcel->getActiveSheet()->setCellValue($cell, $Sub);	
	$cell = 'E'.$i;
	$objPHPExcel->getActiveSheet()->setCellValue($cell, $AccountName);	
	$cell = 'F'.$i;
	$objPHPExcel->getActiveSheet()->setCellValue($cell, $Debit);	
	$objPHPExcel->getActiveSheet()->getStyle($cell)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$cell = 'G'.$i;
	$objPHPExcel->getActiveSheet()->setCellValue($cell, $Credit);	
	$objPHPExcel->getActiveSheet()->getStyle($cell)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$cell = 'H'.$i;
	$objPHPExcel->getActiveSheet()->setCellValue($cell, $Lastyear);	
	$objPHPExcel->getActiveSheet()->getStyle($cell)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$cell = 'I'.$i;
	$i++;
}


$drtot = 'F'.$i;
$crtot = 'G'.$i;
$lytot = 'H'.$i;
$lastrow = $i - 1;
$drsum = 'F3:F'.$lastrow;
$crsum = 'G3:G'.$lastrow;
$lysum = 'H3:H'.$lastrow;


$objPHPExcel->getActiveSheet()->setCellValue($drtot,'=sum('.$drsum.')');
$objPHPExcel->getActiveSheet()->setCellValue($crtot,'=sum('.$crsum.')');
$objPHPExcel->getActiveSheet()->setCellValue($lytot,'=sum('.$lysum.')');


// Rename sheet
$objPHPExcel->getActiveSheet()->setTitle('Trial Balance');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="tb.xlsx"');
header('Cache-Control: max-age=0');

$db->closeDB();

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');

exit;
?>