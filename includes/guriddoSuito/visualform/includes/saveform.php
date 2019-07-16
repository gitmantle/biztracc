<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
ini_set("display_errors",1);
include_once '../form_conf.inc.php';
include_once $projectdir.'jqformconfig.php';
include_once $CODE_PATH.'jqGridUtils.php';
$myFile = jqGridUtils::GetParam('fname');
$r="";
if(file_exists($myFile)) {
	if(is_writable($myFile)) {
		$r = save_server($myFile);
	}  else {
		$r = "File is not Writtable";
	}
} else {
	$r = save_server($myFile);
}
echo $r;
function getRealPOST() {
    $pairs = explode("&", file_get_contents("php://input"));
    $vars = array();
    foreach ($pairs as $pair) {
        $nv = explode("=", $pair);
        $name = urldecode($nv[0]);
        $value = urldecode($nv[1]);
        $vars[$name] = $value;
    }
    return $vars;
}

function save_server( $file )
{
	$fh = fopen($file, 'w');
	if(!$fh) { return "can't open file"; }
	$stringData = '<?xml version="1.0" encoding="utf-8"?>';
	$stringData .=  '<root>';
	$vars = getRealPOST();
	$stringData .= $vars['fcnt'];
	//jqGridUtils::GetParam('fcnt');
	$stringData .= '</root>';
	if( fwrite($fh, ($stringData) ) === FALSE ) {
		return "Cannot write to file ($myFile)";
	}
	fclose($fh);
	return "success";	
}

