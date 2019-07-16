<?php
session_start();
//ini_set('display_errors', true);

$usersession = $_SESSION['usersession'];
$admindb = $_SESSION['s_admindb'];
date_default_timezone_set($_SESSION['s_timezone']);
$induid = $_REQUEST['uid'];

require("../db.php");
mysql_select_db($admindb) or die(mysql_error());
$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);

$cluid = $_SESSION['s_memberid'];

$moduledb = $_SESSION['s_prcdb'];
mysql_select_db($moduledb) or die(mysql_error());


$q = "select * from clinical where uid = ".$induid;
$r = mysql_query($q) or die(mysql_error());
$row = mysql_fetch_array($r);
extract ($row);
$tst = $test;
$res = $result;
$ed = explode('-',$ddate);
$d = $ed[2];
$m = $ed[1];
$y = $ed[0];
$edate = $d.'/'.$m.'/'.$y;

$moduledb = $_SESSION['s_findb'];
mysql_select_db($moduledb) or die(mysql_error());

	

?>


<html>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Edit Clinical Test</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<script language="javascript" type="text/javascript" src="../includes/datetimepick/datetimepicker.js"></script>

<script>
function post() {

	//add validation here if required.
	var dt = document.getElementById('edate').value;
	var tst = document.getElementById('test').value;
	
	var ok = "Y";
	if (dt == '') {
		alert("Please enter a date.");
		ok = "N";
		return false;
	}
	if (tst == '') {
		alert("Please enter a test.");
		ok = "N";
		return false;
	}
	
	if (ok == "Y") {
		document.getElementById('savebutton').value = "Y";
		document.getElementById('form1').submit();
	}
	
}	 
</script>
</head>


<body>
<div id="swin">
<form name="form1" id="form1" method="post" >
 <input type="hidden" name="savebutton" id="savebutton" value="N">
  <table width="400" border="0" align="center">
    <tr>
      <td colspan="3"><div align="center" class="style1"><u>Edit Clinical Test </u></div></td>
    </tr>
    <tr>
      <td class="boxlabel">Date</td>
      <td colspan="2"><input type="Text" id="edate" name="edate" maxlength="25" size="25" value="<?php echo $edate; ?>" onChange="ajaxCheckTransDate();">
        <a href="javascript:NewCal('edate')"><img src="../includes/datetimepick/cal.gif" width="16" height="16" border="0" alt="Pick a date"></a></td>
    </tr>
    <tr>
      <td class="boxlabel">Test</td>
      <td colspan="2"><input type="text" name="test" id="test" value="<?php echo $tst; ?>"></td>
    </tr>
    <tr>
      <td class="boxlabel">Result</td>
      <td colspan="2"><textarea name="tresult" id="tresult" cols="45" rows="5"><?php echo $res; ?></textarea></td>
    </tr>
	<tr>
      <td>&nbsp;</td>
	  <td colspan="2" align="right"> <input type="button" value="Save" name="save" onClick="post()"  ></td>
      </tr>
  </table>
  
<script>
	document.getElementById('<?php echo $dos; ?>').checked = true;
</script> 
  
</form>
</div>

<?php

	if($_REQUEST['savebutton'] == "Y") {
		
		$ed = explode('/',$_REQUEST['edate']);
		$d = $ed[0];
		$m = $ed[1];
		$y = $ed[2];
		$edt = $y.'-'.$m.'-'.$d;
		
		$test = $_REQUEST['test'];
		$result = $_REQUEST['tresult'];

		$moduledb = $_SESSION['s_prcdb'];;
		mysql_select_db($moduledb) or die(mysql_error());
		
		$sSQLString = "update clinical set ";
		$sSQLString .= 'test = "'.$test.'",';
		$sSQLString .= 'result = "'.$result.'",';
		$sSQLString .= 'ddate = "'.$edt.'"';
		$sSQLString .= ' where uid = '.$induid;
		
		$result = mysql_query($sSQLString) or die(mysql_error().' '.$sSQLString);
	
		?>
		<script>
		window.open("","editmembers").jQuery("#mclinicallist").trigger("reloadGrid");
		this.close();
		</script>
		<?php
		
			
	}

?>



</body>
</html>
