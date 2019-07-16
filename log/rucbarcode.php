<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>RUC barcode</title>
</head>

<body>



<?php

error_reporting (E_ALL ^ E_NOTICE ^ E_WARNING);

$regno = 'MANTLE';
$min = 276702;
$max = 286702;
$lic = 397611249;
$vtype = '001';
$rucweight = '01';
$site = 'SITE WWW999999';
$dt = '261112';
$tm = '09:23:28';
$hub = '          ';
$uid = '232864';
//$bartxt = $dt.$regno.$lic.$min.$max.$hub.$rucweight.$vtype.$uid;
echo $bartxt;
$bartxt = '261112MANTLE397611249276702286702          001001232864';
$filename = 'ruc'.$lic;
$pdfname = $filename.'.pdf';
$jpgname = $filename.'.lpg';

require("../includes/barcodes/barcode/barcode.class.php");
$bar = new BARCODE();

// Simple use with only the mandatory parameters. File will be saved in scripts folder
//$bar->PDF417_save($bartxt, $filename, "./ruc/");
// Extended use with all parameters
$bar->PDF417_save($bartxt, $filename, "./ruc/", 150, 300, 15, "#ffffff", "#000000", 2);
// Uses (REMEMBER $_SESSION["_CREATED_FILE_"] will be overwritten when you call PDF417_save)
echo "<img src='".$_SESSION["_CREATED_FILE_"]."' />";



?>
</body>
</html>