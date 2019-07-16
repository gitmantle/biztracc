<?php
session_start();

$findb = $_SESSION['s_findb'];

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select bedate,yrdate from ".$findb.".globals");
$row = $db->single();
extract($row);

$dt = explode('-',$yrdate);
$y = $dt[0];
$m = $dt[1];
$d = $dt[2];
$ddate = $d.'/'.$m.'/'.$y;
$ddateh = $yrdate;

$today = date('Y-m-d');


$db->closeDB();
?>


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Depreciate Fixed Assets</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<script language="javascript" type="text/javascript" src="../includes/datetimepick/datetimepicker.js"></script>



<style type="text/css">
<!--
.style1 {font-size: large}
-->
</style>
</head>

<body>

<form name="form1" id="form1" method="post" action="">
  <input type="hidden" name="yrdate" id="yrdate" value="<?php echo $yrdate; ?>">
  <input type="hidden" name="today" id="today" value="<?php echo $today; ?>">
  <table width="700" border="1" align="center" cellpadding="3" cellspacing="1">
    <tr>
      <td colspan="2"><div align="center" class="style1">Depreciate Fixed Assets </div></td>
    </tr>
    <tr>
      <td colspan="2"><div align="center">
        <p>Fixed Assets will be depreciated for the period date of purchase or begining of tax year to the date specified below, by the percentage and method (Diminishing or Fixed) as set up in the Fixed Asset accounts. </p>
        <p>The Depreciation Account in the Expenses will be debited and the Accumulated Depreciation Control Account will be credited with the  amounts of depreciation per individual asset in the Asset Register. </p>
      </div></td>
    </tr>
    <tr>
      <td width="490">
        Enter the date up to which you want the depreciation calculated </td>
      <td width="189"><input type="Text" id="depdate" name="depdate" value="<?php echo $ddateh; ?>" class="flatpickr" data-alt-input="true" data-alt-format="F j,Y"></a>
		</td>
    </tr>
    <tr>
      <td colspan="2" align="right"><input type="button" value="Proceed" id="btndep" name="save" onClick="calcdepn()"  >
      </td>
    </tr>
  </table>
</form>


 <script>
 	document.getElementById("depdate").flatpickr({
		onChange: function(dateObj, dateStr, instance) {
			ajaxCheckTransDate();			
		}
	});
 </script>  

</body>
</html
