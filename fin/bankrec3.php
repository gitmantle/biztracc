<?php
session_start();

$param = $_REQUEST['p'];

switch ($param) {
	case 'c':
		$msg = 'Bank Reconciliation workings have been committed';
		break;	
	case 'n':
		$msg = 'Bank Reconciliation workings reset';
		break;	
	case 's':
		$msg = 'Bank Reconciliation workings have been saved for later';
		break;
}


?>



<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Finished Bank Reconciliation</title>
</head>

<body>
<form name="form1" method="post" action="">
<br><br>
  <table width="500" border="1" align="center" cellpadding="3" cellspacing="1">
    <tr>
      <td><div align="center"><?php echo $msg; ?></div></td>
    </tr>
  </table>
</form>
</body>
</html>
