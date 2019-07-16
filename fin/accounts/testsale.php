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

	$oTomyob->sRefNo = "'IN012366'";
	$oTomyob->dDate = "'27/11/2007'";
	$oTomyob->sMemo = "'Test Sale 25'";
	$oTomyob->sAcc2Dr = "'11200'";
	$oTomyob->sAcc2Cr = "'42100'";
	$oTomyob->nAmtIncTax = '110.00';
	$oTomyob->nAmtExTax = '100.00';
	$oTomyob->nAmtGST = '10.00';

   //echo($oTomyob->Sales());

	//execute the SQL
	$sResult = $oTomyob->Sales();

	$sResult = HtmlSpecialChars($sResult);
	echo("<pre>");
	echo($sResult);
	echo("</pre>");

?>

</body>
</html>

