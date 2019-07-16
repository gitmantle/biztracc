<?php
session_start();

$findb = $_SESSION['s_findb'];

include_once("../includes/DBClass.php");
$db = new DBClass();

// populate bank account drop down
$db->query("select accountno,branch,sub,account from ".$findb.".glmast where ctrlacc = 'N' and accountno >= 751 and accountno <= 800 and recon = 'Y' order by accountno,branch,sub");
$rows = $db->resultset();
$bankacc_options = "<option value=\"0\">Select Bank Account</option>";
foreach ($rows as $row) {
	extract($row);
	$selected = '';
	$bankacc_options .= "<option value=\"".$accountno."~".$branch."~".$sub.'~'.$account."\">".$account."</option>";
}

date_default_timezone_set($_SESSION['s_timezone']);
$ddate = date("d/m/Y");
$hdate = date("Y-m-d");

$db->closeDB();

require_once("../includes/backgrounds.php");
$theme = $_SESSION['deftheme'];

?>



<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Bank Reconciliation</title>
<link rel="stylesheet" type="text/css" href="../includes/flatpickr_date/dist/flatpickr.min.css">
<script src="../includes/flatpickr_date/dist/flatpickr.js"></script>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">

<script>

function stopRKey(evt) {
  var evt = (evt) ? evt : ((event) ? event : null);
  var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
  if ((evt.keyCode == 13) && (node.type=="text"))  {return false;}
}




</script>

</head>

<body>


<form name="form1" method="post" action="">
 <input type="hidden" name="ddateh" id="ddateh" value="<?php echo $hdate; ?>">
  <table width="500" border="0" align="center" cellpadding="3" cellspacing="1">
    <tr>
      <td colspan="2" align="center"><strong>Bank Reconciliaton</strong></td>
    </tr>
    <tr>
      <td class="boxlabel">Bank Account</td>
      <td><select name="bankno" id="bankno" onChange="recondate()">
        <?php echo $bankacc_options; ?>
      </select></td>
    </tr>
    <tr>
    	<td>Account last reconciled on</td>
        <td><input name="trecdate" id="trecdate" type="text" readonly="true" ></td>
    </tr>
    <tr>
      <td colspan="2" align="left">Please ensure you have entered all relevant transactions eg. bank charges, interest etc. from this statement before running the bank reconcilliation.</td>
      </tr>
      <tr>
          <input type="Text" id="ddate" name="ddate" maxlength="25" size="25" value="<?php echo $hdate; ?>" class="flatpickr" data-alt-input="true" data-alt-format="F j, Y">
      </tr>
  
    <tr>
      <td>&nbsp;</td>
      <td align="right"><input type="button" align="right" value="Run" name="run" id="run" onclick="bankrec()"></td>
    </tr>
  </table>
  
 <script>
 	document.getElementById("ddate").flatpickr({
		onChange: function(dateObj, dateStr, instance) {
			ajaxCheckTransDate();	
		}
	});
 </script>
    
</form>


</body>
</html>
