<?php
session_start();

$findb = $_SESSION['s_findb'];

include_once("../includes/DBClass.php");
$db = new DBClass();



$today = date('Y-m-d');

$db->closeDB();

?>


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Reverse Transaction</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">



<style type="text/css">
<!--
.style1 {font-size: large}
-->
</style>
</head>

<body>

<form name="form1" id="form1" method="post" action="">
  <input type="hidden" name="today" id="today" value="<?php echo $today; ?>">
  <table width="600" border="1" align="center" cellpadding="3" cellspacing="1">
    <tr>
      <td colspan="2"> <div align="center" class="style1">Reverse a Transaction</div></td>
    </tr>
 <!--   <tr colspan="2">
        <td>
          <label>Standard Transaction or Journal Transaction
            <input type="radio" name="reverse" value="std" id="std" onClick="transtype()">
            </label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          <label>Trading Transaction (INV, GRN etc.)
            <input type="radio" name="reverse" value="trd" id="trd" onClick="transtype()">
            </label>
        </td>
    </tr>
    <tr style="display:none;" id="stdentry" colspan="2">
    	<td>Enter the standard/journal transaction reference &nbsp;&nbsp;<input type="text" name="stdref" id="stdref" size="10" ></td>
    </tr>
    <tr style="display:none;" id="trdentry" colspan="2">
    	<td>Enter the trading transaction reference &nbsp;&nbsp;<input type="text" name="trdref" id="trdref" size="10" ></td>
    </tr> -->
    
     <tr colspan="2">
    	<td>Enter the standard/journal transaction reference &nbsp;&nbsp;<input type="text" name="stdref" id="stdref" size="10" ></td>
    </tr>   
    
    <tr colspan="2">
    	<td><input name="btntrans" id="btntrans" type="button" value="Reverse Transaction" onClick="doRevTrans()"></td>
    </tr>
  </table>
  
</form>

</body>
</html>
