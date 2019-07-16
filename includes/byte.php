<?php
session_start();
error_reporting(0);
set_time_limit(0);
ini_set("memory_limit", "64M");


// load SOAP library
require_once("nusoap.php");

// load library that holds implementations of functions we're making available to the web service
// set namespace
$ns = "http://www.kensure.co.nz";
// create SOAP server object
$server = new soap_server();
// setup WSDL file, a WSDL file can contain multiple services
$server->configureWSDL('ByteService',$ns);
$server->wsdl->schemaTargetNamespace=$ns;

// Create a complex type to upload a file
$server->wsdl->addComplexType(
	'FileBytes', // Name
	'complexType', // Type Class
	'array', // PHP Type
	'', // Compositor
	'SOAP-ENC:Array', // Restricted Base
	array(),
	array(
		array('ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'xsd:unsignedByte[]')
	),
	'xsd:unsignedByte'
);

$server->register('createfile',                    // method name
    array('bytearray' => 'tns:FileBytes','filetype' => 'xsd:string','filename' => 'xsd:string'),          // input parameters
    array('return' => 'xsd:string'),    // output parameters
    $ns,                         // namespace
    $ns . '#createfile',                   // soapaction
    'rpc',                                    // style
    'encoded',                                // use
    'Create File'        // documentation
);

function saveref($filename) {
	$dbase = $_SESSION['s_dbase'];

	date_default_timezone_set($_SESSION['s_timezone']);
	$dt = date("Y-m-d");
	
	require("db1.php");
	mysql_select_db($dbase) or die(mysql_error());
	
	$fl = split('__',$filename);
	$memid = $fl[0];
	$fl2 = $fl[1];
	$q = "select sub_id from members where member_id = ".$memid;
	$r = mysql_query($q);
	$row = mysql_fetch_array($r);
	extract($row);
	$subscriber = $sub_id;
	
	//insert entry into documents table
	$query = "insert into documents (member_id,ddate,doc,staff,subject,sub_id) values ";
	$query .= "(".$memid.",'";
	$query .= $dt."','";
	$query .= $fl2."','";
	$query .= " "."','";
	$query .= "From Outlook"."',";
	$query .= $subscriber.")";	
	
	$result = mysql_query($query) or die(mysql_error().' '.$query);
	
	
}



function createfile($bytearray, $filetype, $filename){
	$FILE_LOCATION = 'documents/clients/';
	if ($filename != '') {
		$imgName = $filename;
	} else {
		$imgName = time();
	}
	//$s_Filename = $FILE_LOCATION . $imgName . ".$filetype";
	$s_Filename = $FILE_LOCATION . $imgName;
    file_put_contents($s_Filename, base64_decode($bytearray)); 

	if (file_exists($s_Filename) && filesize($s_Filename)) {
		$xml_return="<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>\r\n";
		$xml_return .= "<RESPONSE>";
		$xml_return .= "success|" . filesize($s_Filename) . " Bytes|" . date("Y-m-d H:i");
		$xml_return .= "</RESPONSE>";
	} else {
		$xml_return="<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>\r\n";
		$xml_return .= "<RESPONSE>\r\n";
		$xml_return .= "failed";
		$xml_return .= "</RESPONSE>";
	}
	saveref($imgName);
	return $xml_return;
	
	
}
// service the methods 
$server->service($HTTP_RAW_POST_DATA);
?>