<?php
session_start();
$usersession = $_SESSION['usersession'];
$dbs = $_SESSION['s_admindb'];

include_once("DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id']
$subscriber = $subid;

$table = 'ztmp'.$user_id.'_tasks';

$heading = 'Tasks for '.$uname;

$cltdb = $_SESSION['s_cltdb'];

$db->query('SELECT todo_id,enter_date,enter_staff,todo_by,complete_by,task,done,category from '.$cltdb.'.'.$table.' order by complete_by desc');
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
            ->setCellValue('A2', 'Entered')
            ->setCellValue('B2', 'By')
            ->setCellValue('C2', 'Responsibility of')
            ->setCellValue('D2', 'Complete by')
            ->setCellValue('E2', 'Task')
            ->setCellValue('F2', 'Done')
            ->setCellValue('G2', 'Category');
	
			
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
$objPHPExcel->getActiveSheet()->getStyle('A2:G2')->applyFromArray($styleArray);			

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(80);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);

$i = 3;
foreach ($rows as $row) {
	extract($row);
	$cell = 'A'.$i;
	$objPHPExcel->getActiveSheet()->setCellValue($cell, $enter_date);
	//$objPHPExcel->getActiveSheet()->getStyle($cell)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYY);
	$cell = 'B'.$i;
	$objPHPExcel->getActiveSheet()->setCellValue($cell, $enter_staff);	
	$cell = 'C'.$i;
	$objPHPExcel->getActiveSheet()->setCellValue($cell, $todo_by);	
	$cell = 'D'.$i;
	$objPHPExcel->getActiveSheet()->setCellValue($cell, $complete_by);	
	$cell = 'E'.$i;
	$objPHPExcel->getActiveSheet()->setCellValue($cell, $task);	
	$cell = 'F'.$i;
	$objPHPExcel->getActiveSheet()->setCellValue($cell, $done);	
	$cell = 'G'.$i;
	$objPHPExcel->getActiveSheet()->setCellValue($cell, $category);	
	$cell = 'H'.$i;
	$i++;
}

$db->closeDB();

// Rename sheet
$objPHPExcel->getActiveSheet()->setTitle('Task List');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="tasklist.xlsx"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');

exit;

