<?php
session_start();

$coyname = $_SESSION['s_coyname'];
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

$hed = $_REQUEST['heading'];
$coyid = $_SESSION['s_coyid'];

$fafile = 'ztmp'.$user_id.'_assets';

$findb = $_SESSION['s_findb'];

$db->query("select aname,bought,acost,adepn,abv,atot,rate from ".$findb.".".$fafile);
$rows = $db->resultset();
$count = count($rows);

$heading = $coyname.' - '.$hed;

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
            ->setCellValue('A3', 'Asset')
            ->setCellValue('B3', 'Bought')
            ->setCellValue('C3', 'Cost')
            ->setCellValue('D3', 'Depreciation')
            ->setCellValue('E3', 'Book Value')
            ->setCellValue('F3', 'Total')
            ->setCellValue('G3', 'Dep. rate %');
		
			
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
$objPHPExcel->getActiveSheet()->getStyle('A3:G3')->applyFromArray($styleArray);			

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(40);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);

$i = 4;
foreach ($rows as $row){
	extract($row);
	$cell = 'A'.$i;
	$objPHPExcel->getActiveSheet()->setCellValue($cell, $aname);	
	$cell = 'B'.$i;
	$objPHPExcel->getActiveSheet()->setCellValue($cell, $bought);	
	$cell = 'C'.$i;
	$objPHPExcel->getActiveSheet()->setCellValue($cell, $acost);	
	$cell = 'D'.$i;
	$objPHPExcel->getActiveSheet()->setCellValue($cell, $adepn);	
	$cell = 'E'.$i;
	$objPHPExcel->getActiveSheet()->setCellValue($cell, $abv);	
	$cell = 'F'.$i;
	$objPHPExcel->getActiveSheet()->setCellValue($cell, $atot);	
	$cell = 'G'.$i;
	$objPHPExcel->getActiveSheet()->setCellValue($cell, $rate);	
	$i++;
}

// Rename sheet
$objPHPExcel->getActiveSheet()->setTitle('Asset Register');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="as.xlsx"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');

$db->closeDB();

exit;
?>