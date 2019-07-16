<?php
session_start();

$admindb = $_SESSION['s_admindb'];
$coyid = $_SESSION['s_coyid'];

require_once('../db.php');
mysql_select_db($admindb) or die(mysql_error());

$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);

$moduledb = $_SESSION['s_logdb'];
mysql_select_db($moduledb) or die(mysql_error());


$fl = "imports/docinv_".$subid."__".$coyid.".xlsx";


require_once("../includes/backgrounds.php");
$theme = $_SESSION['deftheme'];


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Process docket/invoice Spreadsheet</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<script src="../includes/jquery/js/jquery.js" type="text/javascript"></script>

<script>
function stopRKey(evt) {
  var evt = (evt) ? evt : ((event) ? event : null);
  var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
  if ((evt.keyCode == 13) && (node.type=="text"))  {return false;}
}

</script>

<style type="text/css">
<!--
.style1 {font-size: large}
-->
</style>
</head>


<body>

<form name="form1" id="form1" method="post" >
 <input type="hidden" name="savebutton" id="savebutton" value="N">
 <table width="500" border="1" align="center" bgcolor="<?php echo $bgcolor; ?>">
   <tr>
     <td bgcolor="<?php echo $bghead; ?>" align="center"><label style="color: <?php echo $thfont; ?>"><strong>Processing spreadsheet</strong></label></td>
   </tr>
 </table>
<script>
	var x = confirm("Confirm you have run a backup immediately before you entered this routine");
	if (x == false) {
		this.close();
	}
</script>

<?php

if (file_exists($fl)) {
	// PHPExcel_IOFactory 
	require_once '../includes/phpexcel/Classes/PHPExcel/IOFactory.php';
	
	$inputFileName = $fl;
	
	$objReader = PHPExcel_IOFactory::createReader('Excel2007');
	/**  Advise the Reader that we only want to load cell data  **/
	$objReader->setReadDataOnly(true);
	try {
		// Load $inputFileName to a PHPExcel Object 
		$objPHPExcel = $objReader->load($fl); 
	} catch(Exception $e) {
		die('Error loading file: '.$e->getMessage());
	}
	
		$highestrow = $objPHPExcel->getActiveSheet()->getHighestRow();
				
		for ($i = 2; $i <= $highestrow; $i++) {
			$dkt = $objPHPExcel->getActiveSheet()->getCell('A' . $i)->getValue();
			$amt = $objPHPExcel->getActiveSheet()->getCell('B' . $i)->getValue();
			$q = "update dockets set amount = ".$amt." where docket_no =".$dkt;
			$r = mysql_query($q) or die (mysql_error().' '.$q);
		} // for 
	
	$objPHPExcel->disconnectWorksheets();
	unset($objPHPExcel);
	
  echo '<script>';
  echo 'alert("Processed");';
  echo 'this.close();';
  echo '</script>';
	
	

} else { // file not exists
  echo '<script>';
  echo 'alert("'.$provder.' spreadsheet does not exist");';
  echo 'this.close();';
  echo '</script>';
} // file exists

?>
        
</form>

<script>document.onkeypress = stopRKey;</script> 


</body>
</html>

