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

$findb = $_SESSION['s_findb'];

$drfile = $findb.'.'.'ztmp'.$user_id.'_1dr';

$vac = $_SESSION['s_viewac'];
$va = explode('~',$vac);
$ac = $va[0];
$br = trim($va[1]);
$sb = $va[2];

$fromdate = $_SESSION['s_fromdate'];
$todate = $_SESSION['s_todate'];

$coyname = $_SESSION['s_coyname'];
$account = $_SESSION['s_clientname'];

$heading = $coyname.' - '.$account." - between '".$fromdate."' and '".$todate."'";
$short = $ac."_".$br."_".$sb;

$db->query("select ddate,otherleg,debit,credit,runbal,reference,descript1 from ".str_replace("'","",stripslashes($drfile)));
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
            ->setCellValue('A2', 'Date')
            ->setCellValue('B2', 'Corresponding Entry')
            ->setCellValue('C2', 'Debit')
            ->setCellValue('D2', 'Credit')
            ->setCellValue('E2', 'Balance')
            ->setCellValue('F2', 'Reference')
            ->setCellValue('G2', 'Description');
			
			
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
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(24);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(60);

$i = 3;
foreach ($rows as $row){
	extract($row);
	$cell = 'A'.$i;
	$objPHPExcel->getActiveSheet()->setCellValue($cell, $ddate);
	$cell = 'B'.$i;
	$objPHPExcel->getActiveSheet()->setCellValue($cell, $otherleg);	
	$cell = 'C'.$i;
	$objPHPExcel->getActiveSheet()->setCellValue($cell, $debit);	
	$objPHPExcel->getActiveSheet()->getStyle($cell)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$cell = 'D'.$i;
	$objPHPExcel->getActiveSheet()->setCellValue($cell, $credit);	
	$objPHPExcel->getActiveSheet()->getStyle($cell)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$cell = 'E'.$i;
	$objPHPExcel->getActiveSheet()->setCellValue($cell, $runbal);	
	$objPHPExcel->getActiveSheet()->getStyle($cell)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$cell = 'F'.$i;
	$objPHPExcel->getActiveSheet()->setCellValue($cell, $reference);	
	$cell = 'G'.$i;
	$objPHPExcel->getActiveSheet()->setCellValue($cell, $descript1);	
	$cell = 'H'.$i;
	$i++;
}


// Rename sheet
$objPHPExcel->getActiveSheet()->setTitle($short);


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="dr1.xlsx"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');

$db->closeDB();

exit;
?>