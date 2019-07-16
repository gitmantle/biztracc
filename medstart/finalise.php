<?php
session_start();
//error_reporting (E_ALL ^ E_NOTICE ^ E_WARNING);
$totcost = $_SESSION['s_totcost'];
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Finalise Application</title>

<script>
function aprint() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	document.getElementById('paynow').style.visibility = 'visible';
	document.getElementById('exit').style.visibility = 'visible';

	window.open('printapp.php','ap','toolbar=0,scrollbars=1,height=720,width=1000,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function exit() {
	// ajax code to see if rectable exists and if so drop it
	
	
	this.close();
}


</script>

</head>

<body>
<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
<input type="hidden" name="cmd" value="_xclick">
<input type="hidden" name="business" id="business" value="mrsagana@gmail.com">
<input type="hidden" name="lc" value="US">
<input type="hidden" name="item_name" id="item_name" value="Chronic Medicines">
<input type="hidden" name="amount" id="amount" value="<?php echo $totcost; ?>">
<input type="hidden" name="currency_code" value="USD">
<input type="hidden" name="button_subtype" value="services">
<input type="hidden" name="bn" value="PP-BuyNowBF:btn_buynowCC_LG.gif:NonHosted">

<table width="600" align="center">
	<tr>
    	<td><input type="button" name="bprint" id="bprint" value="View/Print" onclick="aprint()"/></td>
    	<td align="center" id="paynow"><input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_buynowCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!"><img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1"></td>
    	<td id="exit"><input type="button" name="bexit" id="bexit" value="Exit" onclick="exit()"/></td>
	</tr>
</table>


</body>

<script>
 document.getElementById('paynow').style.visibility = 'hidden';
 document.getElementById('exit').style.visibility = 'hidden';

</script>

</form>

</html>