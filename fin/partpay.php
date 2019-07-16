<?php

session_start();

//ini_set('display_errors', true);

require_once("../includes/backgrounds.php");
$theme = $_SESSION['deftheme'];

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>RFeceipt Part Payment</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">

<style type="text/css">
<!--
.style1 {font-size: large}
-->
</style>
</head>


<body>
<div id="swin">
<form name="form1" method="post" >
  <table width="400" border="0" align="center">
    <tr>
      <td colspan="2"><div align="center" class="style1"><u>Part Payment </u></div></td>
    </tr>
    <tr>
      <td class="boxlabel">Amount being paid against this line</td>
      <td><div align="left"><input name="partamount" type="text" id="paramount" value="0"></div></td>
      </tr>
	<tr>
	  <td>&nbsp;</td>
	  <td align="right"><input type="submit" value="Save" name="save" ></td>
	  </tr>
  </table>
</form>
</div>

<?php
	if(isset($_POST['save'])) {
		$pp = $_REQUEST['partamount'];
		$_SESSION['s_partpay'] = $pp;
		echo "<script>";
		echo "this.close();";
		echo "</script>";
	}
?>
 

</body>
</html>

