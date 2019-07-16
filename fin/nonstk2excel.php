<?php
session_start();

$nstk = $_SESSION['s_nstk'];
$bdt = $_SESSION['s_bdt'];
$edt = $_SESSION['s_edt'];

//include_once("../includes/logging.php");
$where = " ";
if (isset($_SESSION['s_resultslist_filters'])&&$_SESSION['s_resultslist_filters']) {

	$filterCriteria = $_SESSION['s_resultslist_filters'];
	
	//$sFilter = $filterCriteria['filters']; 
	$output = json_decode($filterCriteria,true); 
	$sOperation = $output["groupOp"];
	$rules=$output["rules"];
	foreach($rules as $val)   
	{  
	   $item = $val["field"];
	   if($item != "ImportRef" && $item != "Grouping" && $item != "Notes")
	   {
		   $criteria = $val["data"];
		   $op = $val["op"];
		   $where .= " {$sOperation} ".$item;
		   switch ($op)
		   {
				case "eq":
					$where .= " = '".$criteria."'";
					break;
				case "ne":
					$where .= " != '".$criteria."'";
					break;    
				case "bw":
					$where .= " like '".$criteria."%'";
					break;    
				case "bn":
					$where .= " not like '".$criteria."%'";
					break;    
				case "ew":
					$where .= " like '%".$criteria."'";
					break;    
				case "en":
					$where .= " not like '%".$criteria."'";
					break;    
				case "cn":
					$where .= " like '%".$criteria."%'";
					break;    
				case "nc":
					$where .= " not like '%".$criteria."%'";
					break;
				case "nu":
					$where .= " is null";
					break;
				case "nn":
					$where .= " is not null";
					break;
				case "in":
					$where .= " in ('".$criteria."')";
					break;
				case "ni":
					$where .= " not in ('".$criteria."')";
					break;                        
		   }
	   }
	}
}			


include_once("../includes/DBClass.php");
$db = new DBClass();

$findb = $_SESSION['s_findb'];

$sSQL="select h.ddate, h.client, i.ref_no, i.item, i.quantity, i.unit, i.value from ".$findb.".invtrans i, ".$findb.".invhead h, ".$findb.".stkmast s where (i.ref_no = h.ref_no) and (i.itemcode = s.itemcode) and (s.stock = 'Service') and (h.ddate >= '".$bdt."' and h.ddate <= '".$edt."') and i.itemcode = '".$nstk."'".$where;

//send_to_log($sSQL);

$db->query($sSQL);
$rows = $db->resultset();

$db->closeDB();

//Error reporting 
error_reporting(E_ALL);

// PHPExcel 
require_once '../includes/phpexcel/Classes/PHPExcel.php';
//require_once '../includes/phpexcel/Classes/PHPExcel/Cell/AdvancedValueBinder.php';

// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

// Set properties
$objPHPExcel->getProperties()->setCreator("Murray Russell")
							 ->setLastModifiedBy("Murray Russell")
							 ->setTitle("Non stock transactions")
							 ->setSubject("Non stock transactions")
							 ->setDescription("Non stock transactions, generated using PHP classes.")
							 ->setKeywords("bizTracc Non-Stock openxml php")
							 ->setCategory("Non stock transactions");


// Header Rows
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'Non-stock transactions for item code '.$nstk)
            ->setCellValue('A2', 'Date')
            ->setCellValue('B2', 'Client')
            ->setCellValue('C2', 'Reference')
            ->setCellValue('D2', 'Item')
            ->setCellValue('E2', 'Quantity')
            ->setCellValue('F2', 'Unit')
            ->setCellValue('G2', 'Value');
			
			
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

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(35);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(10);

$i = 3;
foreach ($rows as $row) {
	extract($row);
	$cell = 'A'.$i;
	$objPHPExcel->getActiveSheet()->setCellValue($cell, $ddate);
	$cell = 'B'.$i;
	$objPHPExcel->getActiveSheet()->setCellValue($cell, $client);	
	$cell = 'C'.$i;
	$objPHPExcel->getActiveSheet()->setCellValue($cell, $ref_no);	
	$cell = 'D'.$i;
	$objPHPExcel->getActiveSheet()->setCellValue($cell, $item);	
	$cell = 'E'.$i;
	$objPHPExcel->getActiveSheet()->setCellValue($cell, $quantity);	
	$cell = 'F'.$i;
	$objPHPExcel->getActiveSheet()->setCellValue($cell, $unit);	
	$cell = 'G'.$i;
	$objPHPExcel->getActiveSheet()->setCellValue($cell, $value);	
	$objPHPExcel->getActiveSheet()->getStyle($cell)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$cell = 'H'.$i;
	$i++;
}

$valtot = 'G'.$i;
$lastrow = $i - 1;
$valsum = 'G3:G'.$lastrow;


$objPHPExcel->getActiveSheet()->setCellValue($valtot,'=sum('.$valsum.')');
$objPHPExcel->getActiveSheet()->getStyle($valtot)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

// Rename sheet
$objPHPExcel->getActiveSheet()->setTitle('Non-stock transactions');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="nonstocktrans.xlsx"');
header('Cache-Control: max-age=0');

$db->closeDB();

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');

exit;
?>
