<?php
session_start();

date_default_timezone_set($_SESSION['s_timezone']);
$cluid = $_SESSION["s_memberid"];

$ddate = date("d/m/Y");
$ddateh = date("Y-m-d");
$usersession = $_SESSION['usersession'];

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];
$subscriber = $subid;
$sname = $row['uname'];

// populate companies drop down
$companies_options = '<option value="0">Select Company</option>';
$db->query("select distinct staff_id,coyid from access where staff_id = ".$user_id);
$rows = $db->resultset();
foreach ($rows as $row) {
	extract($row);
	$cid = $coyid;
	$db->query("select companies.coyid,companies.coyname from companies where companies.coyid = ".$cid);
	$rowsc = $db->resultsetNum();
	foreach ($rowsc as $rowc) {
		extract($rowc);
		$companies_options .= '<option value="'.$rowc[0].'~'.$rowc[1].'">'.$rowc[1].'</option>';
	}
}

$db->closeDB();

?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Get Quote Company</title>

<script>

function ok() {
	var coyno = document.getElementById('coy').value;
		
	var ok = "Y";
	if (coyno == 0) {
		alert("Please select a company.");
		ok = "N";
		return false;
	}
	
	if (ok == "Y") {
		var x = 0, y = 0; // default values	
		x = window.screenX +5;
		y = window.screenY +5;
		window.open('addquote.php?cid='+coyno,'addqt','toolbar=0,scrollbars=1,height=630,width=1020,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	}
	this.close();
}

</script>

</head>

<body>

  <div id="bc" style="position:absolute;visibility:visible;top:10px;left:20px;height:100px;width:300px;border-width:thick thick thick thick; ; border-style:solid;">
    <table>
      <tr>
          <td class="boxlabel">For Company</td>
          <td><select name="coy" id="coy">
            <?php echo $companies_options; ?>
          </select></td>
      </tr>
      <tr>
        <td colspan="2" align="right"><input type="button" name="ok" id="ok" value="OK" onClick="ok()" /></td>
      </tr>
    </table>
  </div>
 
</body>
</html>
