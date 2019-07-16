<?php
require_once "../php/PHPSuito/jqScheduler.php";
require_once "../php/PHPSuito/DBdrivers/jqGridPdo.php";
require_once "jq-config.php";

ini_set("display_errors",1);
date_default_timezone_set('Europe/Sofia');
try {
	$conn = new PDO(DB_DSN,DB_USER,DB_PASSWORD);
	//$conn = sqlsrv_connect( $serverName, $connectionInfo);	   
} catch( Exception $e ) {
	die( "Error connecting to  Server" ); 
}
// Tell the db that we use utf-8
$conn->query("SET NAMES utf8");

$eventcal = new jqScheduler($conn);
$eventcal->setLocale('en_GB');
$eventcal->setUrl('eventcal.php');
$eventcal->setPdfOptions(array("path_to_pdf_class"=>"../php/PHPSuito/External/tcpdf/tcpdf.php"));
$eventcal->setUser(1);
$eventcal->setUserNames(array('1'=>"Calender User 1",'2'=>"Calendar user 2") );
$eventcal->setOption(array("weekends"=>true,"hiddenDays"=>array(0) ));
$eventcal->render();

