<?php
session_start();
$company = $_SESSION['coyname'];

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Please Setup Company</title>
<link rel="stylesheet" href="../includes/acc.css" media="screen" type="text/css">

<style type="text/css">
<!--
.style1 {font-size: large}
-->
</style>
</head>

<body>

<div id="wrapper">
<div id="top"><img src="images/top.jpg"></div>
<div id="main">

	<form name="form1" method="post" action="">
	<br><br>
	<table width="885" border="0" cellpadding="3" cellspacing="1" align="center">
	<tr>
	  <td colspan="2" align="left"><div align="center" class="style1 style1"><u>Please Setup Company</u></div></td>
	  </tr>
	<tr>
	  <td colspan="2" align="left">&nbsp;</td>
	  </tr>
	<tr>
	  <td colspan="2" align="center"><?php echo $company; ?></td>
	  </tr>
	<tr>
	  <td><div align="center">This company has been created but not yet set up.</div></td>
	</tr>
	<tr>
	  <td><div align="center">Please have your administrator attend to this. </div></td>
	  </tr>
	
	</table>
	
	</form>
	</div>
</div>

</body>
</html>