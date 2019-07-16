<?php
session_start();
$coyname = $_SESSION['s_coyname'];
$heading = $_SESSION['s_coyname'].' - '.$_SESSION['s_finheading'];
$todate = $_SESSION['s_todate'];

$usersession = $_SESSION['usersession'];

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];

$mbfile = 'ztmp'.$user_id.'_mthbal';

$findb = $_SESSION['s_findb'];

//  month headings
for ($i = 0; $i <= 11; $i++) {
    $months[] = date("Y-m", strtotime( date( $todate.'-01' )." -$i months"));
}

$y = date('Y', strtotime($months[11]."-01"));
$mth1 = date('M', strtotime($months[11]."-01")).' '.$y;
$y = date('Y', strtotime($months[10]."-01"));
$mth2 = date('M', strtotime($months[10]."-01")).' '.$y;
$y = date('Y', strtotime($months[9]."-01"));
$mth3 = date('M', strtotime($months[9]."-01")).' '.$y;
$y = date('Y', strtotime($months[8]."-01"));
$mth4 = date('M', strtotime($months[8]."-01")).' '.$y;
$y = date('Y', strtotime($months[7]."-01"));
$mth5 = date('M', strtotime($months[7]."-01")).' '.$y;
$y = date('Y', strtotime($months[6]."-01"));
$mth6 = date('M', strtotime($months[6]."-01")).' '.$y;
$y = date('Y', strtotime($months[5]."-01"));
$mth7 = date('M', strtotime($months[5]."-01")).' '.$y;
$y = date('Y', strtotime($months[4]."-01"));
$mth8 = date('M', strtotime($months[4]."-01")).' '.$y;
$y = date('Y', strtotime($months[3]."-01"));
$mth9 = date('M', strtotime($months[3]."-01")).' '.$y;
$y = date('Y', strtotime($months[2]."-01"));
$mth10 = date('M', strtotime($months[2]."-01")).' '.$y;
$y = date('Y', strtotime($months[1]."-01"));
$mth11 = date('M', strtotime($months[1]."-01")).' '.$y;
$y = date('Y', strtotime($months[0]."-01"));
$mth12 = date('M', strtotime($months[0]."-01")).' '.$y;

$db->query("select AccountNumber,Branch,Branchname,Sub,AccountName,m1,m2,m3,m4,m5,m6,m7,m8,m9,m10,m11,m12 from ".$findb.".".$mbfile);
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
            ->setCellValue('F2', $mth1)
            ->setCellValue('G2', $mth2)
            ->setCellValue('H2', $mth3)
            ->setCellValue('I2', $mth4)
            ->setCellValue('J2', $mth5)
            ->setCellValue('K2', $mth6)
            ->setCellValue('L2', $mth7)
            ->setCellValue('M2', $mth8)
            ->setCellValue('N2', $mth9)
            ->setCellValue('O2', $mth10)
            ->setCellValue('P2', $mth11)
            ->setCellValue('Q2', $mth12);
			
			
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
$objPHPExcel->getActiveSheet()->getStyle('A2:Q2')->applyFromArray($styleArray);			

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(6);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(60);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(20);

$objPHPExcel->getActiveSheet()->getStyle('F2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$objPHPExcel->getActiveSheet()->getStyle('G2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$objPHPExcel->getActiveSheet()->getStyle('H2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$objPHPExcel->getActiveSheet()->getStyle('I2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$objPHPExcel->getActiveSheet()->getStyle('J2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$objPHPExcel->getActiveSheet()->getStyle('K2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$objPHPExcel->getActiveSheet()->getStyle('L2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$objPHPExcel->getActiveSheet()->getStyle('M2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$objPHPExcel->getActiveSheet()->getStyle('N2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$objPHPExcel->getActiveSheet()->getStyle('O2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$objPHPExcel->getActiveSheet()->getStyle('P2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$objPHPExcel->getActiveSheet()->getStyle('Q2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

$i = 3;
foreach ($rows as $row){
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
	$objPHPExcel->getActiveSheet()->setCellValue($cell, $m1);	
	$objPHPExcel->getActiveSheet()->getStyle($cell)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$cell = 'G'.$i;
	$objPHPExcel->getActiveSheet()->setCellValue($cell, $m2);	
	$objPHPExcel->getActiveSheet()->getStyle($cell)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$cell = 'H'.$i;
	$objPHPExcel->getActiveSheet()->setCellValue($cell, $m3);	
	$objPHPExcel->getActiveSheet()->getStyle($cell)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$cell = 'I'.$i;
	$objPHPExcel->getActiveSheet()->setCellValue($cell, $m4);	
	$objPHPExcel->getActiveSheet()->getStyle($cell)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$cell = 'J'.$i;
	$objPHPExcel->getActiveSheet()->setCellValue($cell, $m5);	
	$objPHPExcel->getActiveSheet()->getStyle($cell)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$cell = 'K'.$i;
	$objPHPExcel->getActiveSheet()->setCellValue($cell, $m6);	
	$objPHPExcel->getActiveSheet()->getStyle($cell)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$cell = 'L'.$i;
	$objPHPExcel->getActiveSheet()->setCellValue($cell, $m7);	
	$objPHPExcel->getActiveSheet()->getStyle($cell)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$cell = 'M'.$i;
	$objPHPExcel->getActiveSheet()->setCellValue($cell, $m8);	
	$objPHPExcel->getActiveSheet()->getStyle($cell)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$cell = 'N'.$i;
	$objPHPExcel->getActiveSheet()->setCellValue($cell, $m9);	
	$objPHPExcel->getActiveSheet()->getStyle($cell)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$cell = 'O'.$i;
	$objPHPExcel->getActiveSheet()->setCellValue($cell, $m10);	
	$objPHPExcel->getActiveSheet()->getStyle($cell)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$cell = 'P'.$i;
	$objPHPExcel->getActiveSheet()->setCellValue($cell, $m11);	
	$objPHPExcel->getActiveSheet()->getStyle($cell)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$cell = 'Q'.$i;
	$objPHPExcel->getActiveSheet()->setCellValue($cell, $m12);	
	$objPHPExcel->getActiveSheet()->getStyle($cell)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$cell = 'R'.$i;
	$i++;
}


// Rename sheet
$objPHPExcel->getActiveSheet()->setTitle('Monthly Balances');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="mb.xlsx"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');

$db->closeDB();

exit;

?>