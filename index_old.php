<?php

$thisyear = date('Y');

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Logtracc Home</title>
<link href="includes/logcss.css" media="all" type="text/css" rel="stylesheet">
</head>

<body>

<div id="header">
<div id="header-content">
<div id="header-logo">
<img src="images/logo-trans.png" width="371" height="86" alt="LogTracc">
</div>
</div>
</div>

<div id="container">


<div id="bodyleft" style="position:absolute;visibility:visible;top:71px;left:1px;height:400px;width:800px;background-color:#FC9;">
<table width="798" border="0" align="center">
	<tr>
    	<th align="center">Logging Truck Accounting & Administration</th> 
    </tr>
	<tr>
    	<td>Logtracc integrated business systems comprise of software components catering for client management, accounting, stock control, fixed asset management and facilities specific to logging truck operations.
          <p>These systems provide data capture from tablets mounted in the truck cab that feed to a back end web based system where administrators can correlate, analyse and obtain reports on all aspects of their operations. These inlcude:-</p>
        <ul>
        <li>Full accounting reports plus profit and loss per vehicle</li>
        <li>RUC refund forms automatically filled in</li>
        <li>Driver log data</li>
        <li>Location tracking of vehicle movements</li>
        </ul>
        </td>
	</tr>
</table>
</div>

<div id="righttop" style="position:absolute;visibility:visible;top:71px;left:801px;height:135px;width:200px;background-color:#FF9;">
<table width="198" border="0" align="center">
	<tr>
    	<td align="right"><a href="index2.php" title="Login">Login</a></td>
    </tr>
</table>
<table width="198" border="0" align="center" background="images/back.jpg">
	<tr>
    	<td>For a demonstration, login using</td>
    </tr>
 	<tr>
        <td>username: logs</td>
    </tr>
	<tr>
   	    <td>password: logs</td>
    </tr>
</table>
</div>

<div id="rightbottom" style="position:absolute;visibility:visible;top:206px;left:801px;height:265px;width:200px;background-color:#FFC;">
<table width="198" border="0" align="center">
    <tr>
    	<td><a href="pricing.php" title="Facilities and Pricing">Facilities and Pricing</a></td>
    </tr>
    <tr>
    	<td><a href="contact.php" title="Contact us">Contact us</a></td>
    </tr>
</table>
</div>

<div id="footer" style="position:absolute;visibility:visible;top:471px;left:1px;height:30px;width:1000px;background-color:#CCC;">
<table width="800" border="0" align="center">
    <tr>
    <td align="center">© Murray Russell. 2012 - <?php echo $thisyear; ?> </td>
    </tr>
</table>

</div>

</div>

</body>
</html>