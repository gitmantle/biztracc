<?php
session_start();
$usersession = $_SESSION['usersession'];

$ddate = date("d/m/Y");
$hdate = date("Y-m-d");

$admindb = $_SESSION['s_admindb'];
require("../db.php");
mysql_select_db($admindb) or die(mysql_error());
$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);

$heading = $_REQUEST['heading'];

$coyname = $_SESSION['s_coyname'];

$arrprint = $_REQUEST['notes'];
$aprint = explode(',',$arrprint);
$filterfile = $_REQUEST['filterfile'];

$moduledb = $_SESSION['s_cltdb'];
mysql_select_db($moduledb) or die(mysql_error());


if (file_exists('text/pdflist'.$user_id.'.txt')) {
	unlink('text/pdflist'.$user_id.'.txt');
}
$fp = fopen('text/pdflist'.$user_id.'.txt', 'w'); 

$cstring = "H;H;N;".$coyname."\n";
fwrite($fp, $cstring);
$cstring = "H;H;N;".$heading."\n";
fwrite($fp, $cstring);
$ap = "B;H;H";
$cstring = $ap.";Last Name;First Name;Phone;Age;Advisor\n";
fwrite($fp, $cstring);
	

$query = "select lastname,firstname,phone,address,suburb,town,postcode,age,staff from ".$filterfile." order by lastname,firstname"; 
$calc = mysql_query($query) or die(mysql_error());
while ($row_calc = mysql_fetch_array($calc)) {
	extract($row_calc);
	
	$ap = "B;D;N";
	$cstring = $ap.";".$lastname.";".$firstname.";".$phone.";".$age.";".$staff."\n";
	fwrite($fp, $cstring);
	
	$ap = "B;N;N";
	$cstring = $ap.";".$address.";".$suburb.";".$town.";".$postcode."\n";
	fwrite($fp, $cstring);

}
      
fclose($fp); 


mysql_close($connect);

$host  = $_SERVER['HTTP_HOST'];
$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
$extra = "text/listpdf.php?coy=".$user_id;
header("Location: http://$host$uri/$extra");
exit;



?>