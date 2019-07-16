<?php

require_once("../includes/backgrounds.php");


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
     "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8">
<title>Calculators</title>
<style type="text/css">
@import "calculator/jquery.calculator.css";
</style>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<link rel="stylesheet" type="text/css" href="css/jquery.calculator.css"> 
<script type="text/javascript" src="calculator/jquery.plugin.js"></script> 
<script type="text/javascript" src="calculator/jquery.calculator.js"></script>
<script type="text/javascript">
$(function () {
	$('#basicCalculator').calculator({
		showOn: 'both', buttonImageOnly: true, buttonImage: 'calculator/calculator.png'});
	
	$('#scientificCalc').calculator({layout: $.calculator.scientificLayout, 
		showOn: 'both', buttonImageOnly: true, buttonImage: 'calculator/calculator.png'});	
	
	$('#baseCalc').calculator({layout: ['BBBOBDBH', '_0_1_2_3_+@X', 
    '_4_5_6_7_-@U', '_8_9_A_B_*@E', '_C_D_E_F_/', 'BSCECA_._='], 
    showOn: 'both', buttonImageOnly: true, buttonImage: 'calculator/calculator.png'});
	
});


// number formatting function
// copyright Stephen Chapman 24th March 2006, 22nd August 2008
// permission to use this function is granted provided
// that this copyright notice is retained intact

// num = number to be formatted
// dec = to how many decimal places
// thou = thousands seperator
// pnt = symbol for decimal
// curr1 = currency symbol to left of number
// curr2 = currency symbol to right of number
// n1 = symbol to place in front of number eg - (minus)
// n2 = symbol to place after number eg CR

function formatNumber(num,dec,thou,pnt,curr1,curr2,n1,n2) {var x = Math.round(num * Math.pow(10,dec));if (x >= 0) n1=n2='';var y = (''+Math.abs(x)).split('');var z = y.length - dec; if (z<0) z--; for(var i = z; i < 0; i++) y.unshift('0'); if (z<0) z = 1; y.splice(z, 0, pnt); if(y[0] == pnt) y.unshift('0'); while (z > 3) {z-=3; y.splice(z,0,thou);}var r = curr1+n1+y.join('')+n2+curr2;return r;}


function pv() {
	var fv = document.getElementById('pvfv').value;
	var i = document.getElementById('pvint').value/1200;
	var n = document.getElementById('pvperiod').value*12;
	var v = Math.pow((1+i),n);
	var pv = (fv*1/v);
	pvf = formatNumber(pv,2,',','.','','','','');
	document.getElementById('pvpv').value = pvf;
}

function fv() {
	var pv = document.getElementById('fvpv').value;
	var i = document.getElementById('fvint').value/1200;
	var n = document.getElementById('fvperiod').value*12;
	var v = Math.pow((1+i),n);
	var fv = (pv*v);
	fvf = formatNumber(fv,2,',','.','','','','');
	document.getElementById('fvfv').value = fvf;
}


function checkNumber(input, min, max, msg) {
    msg = msg + " field has invalid data: " + input.value;
    var str = input.value;

    for (var i = 0; i < str.length; i++) {
        var ch = str.substring(i, i + 1)
        if ((ch < "0" || "9" < ch) && ch != '.') {
            alert(msg);
            return false;
        }
    }

    var num = 0 + str
    if (num < min || max < num) {
        alert(msg + " not in range [" + min + ".." + max + "]");
        return false;
    }

    input.value = str;
    return true;
}

function fv2(form) {

    if ((form.payments.value == null || form.payments.value.length == 0) ||
        (form.moAdd.value == null || form.moAdd.value.length == 0) ||
        (form.interest.value == null || form.interest.value.length == 0) ||
        (form.principal.value == null || form.principal.value.length == 0)) {
        return;
    }

    if (!checkNumber(form.interest, .001, 99, "Interest Rate") ||
        !checkNumber(form.payments, 1, 99, "Years") ||
        !checkNumber(form.moAdd, 0, 10000000, "Monthly Addition") ||
        !checkNumber(form.principal, 10, 10000000, "Initial Investment")) {
        return;
    }

    var i = form.interest.value;
    if (i > 1.0) {i = form.interest.value / 100} else {i = form.interest.value};
    i /= 12;
    var ma = eval(form.moAdd.value);
    var prin = eval(form.principal.value);
    var pmts = eval(form.payments.value * 12);
    var count = 0;
 
    while(count < pmts) {
        newprin = prin + ma;
        prin = (newprin * i) + eval(prin + ma);
        count = count + 1;
      }
	  
	pvf = formatNumber(prin,2,',','.','','','','');

    form.fvfv2.value = pvf;
}



function pmtmp() {
	var pmtsum = document.getElementById('pmtsum').value;
	var i = document.getElementById('pmtint').value/1200;
	var n = document.getElementById('pmtperiod').value*12;
	var v = (pmtsum*i) / (1-Math.pow(1+i, -n));
	vf = formatNumber(v,2,',','.','','','','');
	document.getElementById('pmt').value = vf;
}

function  calculateBMIm() {
  var weight = document.getElementById('weightkgs').value;
  var height = document.getElementById('heightcms').value;
  var height2 = height / 100;
  var BMI = weight  / (height2 * height2);
  document.getElementById('bmim').value=custRound(BMI,1);
}

function custRound(x,places) {
  return (Math.round(x*Math.pow(10,places)))/Math.pow(10,places)
}

function mod(div,base) {
  return Math.round(div - (Math.floor(div/base)*base));
}
function calculateBMIi() {
  var w = document.getElementById('weightlb').value * 1;
  var HeightFeetInt = document.getElementById('heightft').value * 1;
  var HeightInchesInt = document.getElementById('heightin').value * 1;
  var HeightFeetConvert = HeightFeetInt * 12;
  var h = HeightFeetConvert + HeightInchesInt;
  var displaybmi = (Math.round((w * 703) / (h * h)));
  document.getElementById('bmii').value = displaybmi;
}


</script>
</head>
<body>
<form name="calculators" id="calculators">
  <div id="bwin">
    <div id="math" style="position:absolute;visibility:visible;top:1px;left:1px;height:150px;width:460px;background-color:<?php echo $bgcolor; ?>;border-width:thin thin thin thin; border-color:blue; border-style:solid;">
      <table align="center" bgcolor="<?php echo $bgcolor; ?>">
        <tr>
          <td colspan="2" align="center" bgcolor="<?php echo $bghead; ?>"><label style="color: <?php echo $thfont; ?>">Mathematical</label></td>
        </tr>
        <tr>
          <td><label style="color: <?php echo $tdfont; ?>">Basic Calculator</label></td>
          <td><input type="text" id="basicCalculator"></td>
        </tr>
        <tr>
          <td><label style="color: <?php echo $tdfont; ?>">Scientific Calculator</label></td>
          <td><input type="text" id="scientificCalc"></td>
        </tr>
        <tr>
          <td><label style="color: <?php echo $tdfont; ?>">Dec, Oct, Hex Calculator</label></td>
          <td><input type="text" id="baseCalc"></td>
        </tr>
      </table>
    </div>
    <div id="pv" style="position:absolute;visibility:visible;top:1px;left:461px;height:150px;width:460px;background-color:<?php echo $bgcolor; ?>;border-width:thin thin thin thin; border-color:blue; border-style:solid;">
      <table align="center">
        <tr>
          <td colspan="2" align="center" bgcolor="<?php echo $bghead; ?>"><label style="color: <?php echo $thfont; ?>">Present Value</label></td>
        </tr>
        <tr>
          <td><label style="color: <?php echo $tdfont; ?>">Future Value</label></td>
          <td><input type="text" name="pvfv" id="pvfv"></td>
        </tr>
        <tr>
          <td><label style="color: <?php echo $tdfont; ?>">Interest pa.</label></td>
          <td><input type="text" name="pvint" id="pvint"></td>
        </tr>
        <tr>
          <td><label style="color: <?php echo $tdfont; ?>">Number of Years</label></td>
          <td><input type="text" name="pvperiod" id="pvperiod"></td>
        </tr>
        <tr>
          <td><input type="button" name="btnpv" id="btnpv" value="Present Value" onclick="pv();return false;"></td>
          <td><input type="text" name="pvpv" id="pvpv"></td>
        </tr>
      </table>
    </div>
    <div id="fv" style="position:absolute;visibility:visible;top:151px;left:1px;height:150px;width:460px;background-color:<?php echo $bgcolor; ?>;border-width:thin thin thin thin; border-color:blue; border-style:solid;">
      <table align="center">
        <tr>
          <td colspan="2" align="center" bgcolor="<?php echo $bghead; ?>"><label style="color: <?php echo $thfont; ?>">Future Value - Fixed Sum</label></td>
        </tr>
        <tr>
          <td><label style="color: <?php echo $tdfont; ?>">Present Value</label></td>
          <td><input type="text" name="fvpv" id="fvpv"></td>
        </tr>
        <tr>
          <td><label style="color: <?php echo $tdfont; ?>">Interest pa.</label></td>
          <td><input type="text" name="fvint" id="fvint"></td>
        </tr>
        <tr>
          <td><label style="color: <?php echo $tdfont; ?>">Number of Years</label></td>
          <td><input type="text" name="fvperiod" id="fvperiod"></td>
        </tr>
        <tr>
          <td><input type="button" name="btnfv" id="btnfv" value="Future Value" onclick="fv();return false;"></td>
          <td><input type="text" name="fvfv" id="fvfv"></td>
        </tr>
      </table>
    </div>
    <div id="ot" style="position:absolute;visibility:visible;top:151px;left:461px;height:150px;width:460px;background-color:<?php echo $bgcolor; ?>;border-width:thin thin thin thin; border-color:blue; border-style:solid;">
      <table align="center">
        <tr>
          <td colspan="2" align="center" bgcolor="<?php echo $bghead; ?>"><label style="color: <?php echo $thfont; ?>">Monthly Payments</label></td>
        </tr>
        <tr>
          <td><label style="color: <?php echo $tdfont; ?>">Sum Required</label></td>
          <td><input type="text" name="pmtsum" id="pmtsum"></td>
        </tr>
        <tr>
          <td><label style="color: <?php echo $tdfont; ?>">Interest pa.</label></td>
          <td><input type="text" name="pmtint" id="pmtint"></td>
        </tr>
        <tr>
          <td><label style="color: <?php echo $tdfont; ?>">Number of Years</label></td>
          <td><input type="text" name="pmtperiod" id="pmtperiod"></td>
        </tr>
        <tr>
          <td><input type="button" name="btnpmt" id="btnpmt" value="Monthly Payment" onclick="pmtmp();return false;"></td>
          <td><input type="text" name="pmt" id="pmt"></td>
        </tr>
      </table>
    </div>
    <div id="fv" style="position:absolute;visibility:visible;top:302px;left:1px;height:165px;width:460px;background-color:<?php echo $bgcolor; ?>;border-width:thin thin thin thin; border-color:blue; border-style:solid;">
      <table align="center">
        <tr>
          <td colspan="2" align="center" bgcolor="<?php echo $bghead; ?>"><label style="color: <?php echo $thfont; ?>">BMI - Imperial</label></td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td colspan="2" align="center" bgcolor="<?php echo $bghead; ?>"><label style="color: <?php echo $thfont; ?>">BMI - Metric</label></td>
        </tr>
        <tr>
          <td><label style="color: <?php echo $tdfont; ?>">Height</label></td>
          <td><input name="heightft" type="text" id="heightft" size="5" maxlength="2">
            ft.
            <input name="heightin" type="text" id="heightin" size="5" maxlength="5">
            ins.</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td><label style="color: <?php echo $tdfont; ?>">Height</label></td>
          <td><input name="heightcms" type="text" id="heightcms" size="5" maxlength="3">
            cms.</td>
        </tr>
        <tr>
          <td><label style="color: <?php echo $tdfont; ?>">Weight</label></td>
          <td><input name="weightlb" type="text" id="weightlb" size="5" maxlength="3">
            lbs.</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
           <td><label style="color: <?php echo $tdfont; ?>">Weight</label></td>
          <td><input name="weightkgs" type="text" id="weightkgs" size="5" maxlength="3">
            kgs.</td>
       </tr>
        <tr>
          <td><input type="button" name="btnfv" id="btnfv" value="BMI" onclick="calculateBMIi();return false;"></td>
          <td><input name="bmii" type="text" id="bmii" size="7" maxlength="7"></td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td><input type="button" name="btnfv" id="btnfv" value="BMI" onclick="calculateBMIm();return false;"></td>
          <td><input name="bmim" type="text" id="bmim" size="7" maxlength="7"></td>
        </tr>
      </table>
    </div>
    <div id="ot" style="position:absolute;visibility:visible;top:302px;left:461px;height:165px;width:460px;background-color:<?php echo $bgcolor; ?>;border-width:thin thin thin thin; border-color:blue; border-style:solid;">
      <table align="center">
        <tr>
          <td colspan="2" align="center" bgcolor="<?php echo $bghead; ?>"><label style="color: <?php echo $thfont; ?>">Future Value - Installments</label></td>
        </tr>
        <tr>
          <td><label style="color: <?php echo $tdfont; ?>">Initial Investment</label></td>
          <td><input type="text" name="principal" id="principal"></td>
        </tr>
        <tr>
          <td><label style="color: <?php echo $tdfont; ?>">Interest pa.</label></td>
          <td><input type="text" name="interest" id="interest"></td>
        </tr>
        <tr>
          <td><label style="color: <?php echo $tdfont; ?>">Number of Years</label></td>
          <td><input type="text" name="payments" id="payments"></td>
        </tr>
        <tr>
          <td><label style="color: <?php echo $tdfont; ?>">Monthly Payments</label></td>
          <td><input type="text" name="moAdd" id="moAdd"></td>
        </tr>
        <tr>
          <td><input type="button" name="btnfv" id="btnfv" value="Future Value" onclick="fv2(this.form);return false;"></td>
          <td><input type="text" name="fvfv2" id="fvfv2"></td>
        </tr>
      </table>
    </div>
  </div>
</form>
</body>
</html>
