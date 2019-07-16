<?php
session_start();
error_reporting (E_ALL ^ E_NOTICE);

$thisyear = date('Y');

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Cmeds4U - Application</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">


<script>

function countries() {
	var cty = document.getElementById('lcountry').value;
	switch (cty) {
		case "aus":
			document.getElementById('australia').style.visibility = "visible";
			document.getElementById('tanzania').style.visibility = "hidden";
			document.getElementById('zambia').style.visibility = "hidden";
			document.getElementById('zimbabwe').style.visibility = "hidden";
		break;
		case "tan":
			document.getElementById('australia').style.visibility = "hidden";
			document.getElementById('tanzania').style.visibility = "visible";
			document.getElementById('zambia').style.visibility = "hidden";
			document.getElementById('zimbabwe').style.visibility = "hidden";
		break;
		case "zam":
			document.getElementById('australia').style.visibility = "hidden";
			document.getElementById('tanzania').style.visibility = "hidden";
			document.getElementById('zambia').style.visibility = "visible";
			document.getElementById('zimbabwe').style.visibility = "hidden";
		break;
		case "zim":
			document.getElementById('australia').style.visibility = "hidden";
			document.getElementById('tanzania').style.visibility = "hidden";
			document.getElementById('zambia').style.visibility = "hidden";
			document.getElementById('zimbabwe').style.visibility = "visible";
		break;
	}
}

function zim2() {
	window.open('onlineappzim2.php','azim2');
}
function zima() {
	window.open('availablezim.php','azim2');
}

</script>

</head>

<body>
<div id="wrapper">

<div id="mainheader">
	<div id="mainlefttop"><img src="../images/jacarandasmall.png" width="90" height="60"></div>
</div>

<form name="form1" id="form1" method="post">

  <table border="0" align="center">
  	<tr>
    	<td colspan="2">&nbsp;</td>
    </tr>
    <tr>
      <td class="boxlabel"><label style="font-size:16px">Country</label> </td>
      <td><select name="lcountry" id="lcountry" onchange="countries()">
        <option value="">Select Country</option>
        <option value="aus">Australia</option>
        <option value="tan">Tanzania</option>
        <option value="zam">Zambia</option>
        <option value="zim">Zimbabwe</option>
      </select></td>
    </tr>
    </table>
    <div id="container" style="position:relative;visibility:visible;top:1px;left:3px;height:300px;width:990px;border-width:thin thin thin thin; border-color:#999; border-style:solid;">
        <div id="australia" style="position:relative;visibility:hidden;top:5px;left:5px;height:200px;width:980px;">
        	<table border="0" width="950" align="center">
            <tr><td>This is what is available in Australia</td></tr>
            <tr><td><textarea rows="3" cols="90">Blurb on Pharmacy distribution</textarea></td>
            <td><input type="button" name="baus1" id="baus1" value="Apply" onclick="aus1();"/></td></tr>
            </table>
        </div>
        <div id="tanzania" style="position:absolute;visibility:hidden;top:5px;left:5px;height:200px;width:980px;">
        	<table border="0" width="950" align="center">
            <tr><td>This is what is available in Tanzania</td></tr>
            <tr><td><textarea rows="3" cols="90">Blurb on HIV TB distribution</textarea></td>
            <td>&nbsp;</td></tr>
            </table>
        </div>
        <div id="zambia" style="position:absolute;visibility:hidden;top:5px;left:5px;height:200px;width:980px;">
        	<table border="0" width="950" align="center">
            <tr><td>This is what is available in Zambia</td></tr>
            <tr><td><textarea rows="3" cols="90">Blurb on HIV TB distribution</textarea></td>
            <td>&nbsp;</td></tr>
            </table>
        </div>
        <div id="zimbabwe" style="position:absolute;visibility:visible;top:5px;left:5px;height:200px;width:980px;">
        	<table border="0" width="950" align="center">
            <tr><td>This is what is available in Zimbabwe</td><td>&nbsp;</td></tr>
            <tr><td><textarea rows="3" cols="90">Blurb on HIV TB distribution</textarea></td>
            <td>&nbsp;</td></tr>
            <tr><td rowspan="2"><textarea rows="3" cols="90">Blurb on Chronic Medicine distribution</textarea></td>
            <td><input type="button" name="bzim2" id="bzim2" value="Apply" onclick="zim2();"/></td></tr>
            <tr>
              <td><input type="button" name="bzima" id="bzima" value="Available" onclick="zima();"/></td>
            </tr>
            </table>
        </div>
    </div>
<div id="footer"; align="center">
	 © Murray Russell 2014 - <?php echo $thisyear; ?>
</div>
 
<script>
	document.getElementById('australia').style.visibility = "hidden";
	document.getElementById('tanzania').style.visibility = "hidden";
	document.getElementById('zambia').style.visibility = "hidden";
	document.getElementById('zimbabwe').style.visibility = "hidden";
</script>
 
  
</form>

</div>

</body>
</html>