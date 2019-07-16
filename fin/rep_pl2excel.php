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

$coyname = $_SESSION['s_coyname'];

$plfile = 'ztmp'.$user_id.'_pl';

$findb = $_SESSION['s_findb'];

$db->query("select Header, AccountNumber, Branch, Sub, AccountName, Sbal, Bal, Total from ".$findb.".".$plfile);
$rows = $db->resultset();

$count = count($rows);

$heading = 'Profit & Loss '.' - '.$coyname;

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
            ->setCellValue('A2', '')
            ->setCellValue('B2', 'Account No.')
            ->setCellValue('C2', 'Branch')
            ->setCellValue('D2', 'Sub')
            ->setCellValue('E2', 'Account Name')
            ->setCellValue('F2', 'Sub Bal.')
            ->setCellValue('G2', 'Balance')
			->setCellValue('H2', 'Total');
		
			
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

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(8);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(6);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(40);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);

$i = 3;
foreach ($rows as $row) {
	extract($row);
	$cell = 'A'.$i;
	$objPHPExcel->getActiveSheet()->setCellValue($cell, $Header);
	$cell = 'B'.$i;
	$objPHPExcel->getActiveSheet()->setCellValue($cell, $AccountNumber);	
	$cell = 'C'.$i;
	$objPHPExcel->getActiveSheet()->setCellValue($cell, $Branch);	
	$cell = 'D'.$i;
	$objPHPExcel->getActiveSheet()->setCellValue($cell, $Sub);	
	$cell = 'E'.$i;
	$objPHPExcel->getActiveSheet()->setCellValue($cell, $AccountName);	
	$cell = 'F'.$i;
	$objPHPExcel->getActiveSheet()->setCellValue($cell, $Sbal);	
	$cell = 'G'.$i;
	$objPHPExcel->getActiveSheet()->setCellValue($cell, $Bal);	
	$cell = 'H'.$i;
	$objPHPExcel->getActiveSheet()->setCellValue($cell, $Total);	
	$i++;
}

// Rename sheet
$objPHPExcel->getActiveSheet()->setTitle('Profit and Loss');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="pl.xlsx"');
header('Cache-Control: max-age=0');

$db->closeDB();

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');

exit;
?>