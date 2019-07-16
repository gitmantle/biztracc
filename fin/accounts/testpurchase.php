<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" >
<html>
<head>
<title>PHP&nbsp;ToMyob</title>
</head>
<body>
<?php
  require_once("tomyob.php");

	$oTomyob = new ToMYOB;

	//Set the Hostname, port, and connection string
    $oTomyob->sHostName = "10.2.1.8";
	$oTomyob->nPort = 9628;
	$oTomyob->sConnectionString = "DSN=myob;UID=odbc;PWD;";

	$oTomyob->sRefNo = "'ON012346'";
	$oTomyob->dDate = "'11/27/2006'";
	$oTomyob->sMemo = "'Test Purchase 1'";
	$oTomyob->sAcc2Dr = "'51100'";
	$oTomyob->sAcc2Cr = "'21200'";
	$oTomyob->nAmtIncTax = '110.00';
	$oTomyob->nAmtExTax = '100.00';
	$oTomyob->nAmtGST = '10.00';

	//execute the SQL
	$sResult = $oTomyob->Purchase();

	$sResult = HtmlSpecialChars($sResult);
	echo("<pre>");
	echo($sResult);
	echo("</pre>");

?>

</body>
</html>

