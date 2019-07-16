<?php
session_start();
$coyname = $_SESSION['s_coyname'];
$heading = 'Debtors Aged Balances';

$usersession = $_SESSION['usersession'];

$cltdb = $_SESSION['s_cltdb'];
$coyid = $_SESSION['s_coyid'];

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];

$findb = $_SESSION['s_findb'];

$db->query("SELECT client_company_xref.uid,client_company_xref.drno,client_company_xref.drsub,concat(members.firstname,' ',members.lastname) as coyname,client_company_xref.current,client_company_xref.d30,client_company_xref.d60,client_company_xref.d90,client_company_xref.d120,(client_company_xref.current+client_company_xref.d30+client_company_xref.d60+client_company_xref.d90+client_company_xref.d120) as bal from ".$cltdb.".client_company_xref,".$cltdb.".members where (client_company_xref.client_id = members.member_id) and (client_company_xref.drno <> 0) and (client_company_xref.company_id = ".$coyid.")");
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
            ->setCellValue('B2', 'Sub')
            ->setCellValue('C2', 'Debtor')
            ->setCellValue('D2', 'Current')
            ->setCellValue('E2', '30 day')
            ->setCellValue('F2', '60 day')
            ->setCellValue('G2', '90 day')
            ->setCellValue('H2', '120 day +')
			->setCellValue('I2', 'Balance');
		
			
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
$objPHPExcel->getActiveSheet()->getStyle('A2:I2')->applyFromArray($styleArray);			

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(6);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(50);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);

$i = 3;
foreach ($rows as $row) {
	extract($row);
	$cell = 'A'.$i;
	$objPHPExcel->getActiveSheet()->setCellValue($cell, $drno);
	$cell = 'B'.$i;
	$objPHPExcel->getActiveSheet()->setCellValue($cell, $drsub);	
	$cell = 'C'.$i;
	$objPHPExcel->getActiveSheet()->setCellValue($cell, $coyname);	
	$cell = 'D'.$i;
	$objPHPExcel->getActiveSheet()->setCellValue($cell, $current);	
	$objPHPExcel->getActiveSheet()->getStyle($cell)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);	
	$cell = 'E'.$i;
	$objPHPExcel->getActiveSheet()->setCellValue($cell, $d30);
	$objPHPExcel->getActiveSheet()->getStyle($cell)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);	
	$cell = 'F'.$i;
	$objPHPExcel->getActiveSheet()->setCellValue($cell, $d60);	
	$objPHPExcel->getActiveSheet()->getStyle($cell)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$cell = 'G'.$i;
	$objPHPExcel->getActiveSheet()->setCellValue($cell, $d90);	
	$objPHPExcel->getActiveSheet()->getStyle($cell)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$cell = 'H'.$i;
	$objPHPExcel->getActiveSheet()->setCellValue($cell, $d120);	
	$objPHPExcel->getActiveSheet()->getStyle($cell)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$cell = 'I'.$i;
	$objPHPExcel->getActiveSheet()->setCellValue($cell, $bal);	
	$objPHPExcel->getActiveSheet()->getStyle($cell)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$cell = 'J'.$i;	
	$i++;
}


$currenttot = 'D'.$i;
$d30tot = 'E'.$i;
$d60tot = 'F'.$i;
$d90tot = 'G'.$i;
$d120tot = 'H'.$i;
$baltot = 'I'.$i;
$lastrow = $i - 1;
$currentsum = 'D3:D'.$lastrow;
$d30sum = 'E3:E'.$lastrow;
$d60sum = 'F3:F'.$lastrow;
$d90sum = 'G3:G'.$lastrow;
$d120sum = 'H3:H'.$lastrow;
$balsum = 'I3:I'.$lastrow;

$objPHPExcel->getActiveSheet()->setCellValue($currenttot,'=sum('.$currentsum.')');
$objPHPExcel->getActiveSheet()->setCellValue($d30tot,'=sum('.$d30sum.')');
$objPHPExcel->getActiveSheet()->setCellValue($d60tot,'=sum('.$d60sum.')');
$objPHPExcel->getActiveSheet()->setCellValue($d90tot,'=sum('.$d90sum.')');
$objPHPExcel->getActiveSheet()->setCellValue($d120tot,'=sum('.$d120sum.')');
$objPHPExcel->getActiveSheet()->setCellValue($baltot,'=sum('.$balsum.')');

// Rename sheet
$objPHPExcel->getActiveSheet()->setTitle('Debtors Aged Balances');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="draged.xlsx"');
header('Cache-Control: max-age=0');

$db->closeDB();

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');

exit;
?>