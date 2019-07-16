<?php
session_start();
//error_reporting(0);
$usersession = $_SESSION['usersession'];

$ddate = date("d/m/Y");
$hdate = date("Y-m-d");

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];
$subscriber = $subid;
$sname = $row['uname'];

$userip = $row['userip'];
$userid = $user_id;
$pic = $userid.'pic.jpg';

$mv = $_REQUEST['mv'];
$_SESSION['s_mv'] = $mv;

require_once("includes/printfilter.php");

$ddate = date("d/m/Y");
$fltfile = "ztmp".$user_id."_filterlist";

$theme = $_SESSION['deftheme'];
require_once("../includes/backgrounds.php");

$db->closeDB();

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Filtered List</title>

<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<link type="text/css" href="../includes/jquery/themes/<?php echo $theme; ?>/jquery.ui.all.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="../includes/jquery/themes/ui.jqgrid.css" />
<script type="text/javascript" src="../includes/jquery.js"></script>

</head>

<body>
<form name="form1" id="form1" method="post" >
<div id='mwin'>
	<table align="left" border="0" cellspacing="0" cellpadding="0">
    <tr>
    <th align="center">Add Note to Bulk Members</th>
    </tr>
    <tr><td align="center"><textarea name="tanote" cols="70" rows="10"></textarea></td></tr>
    <tr><td align="left"><input type="submit" value="Save and Exit" name="save" id="save"></td></tr>
    </table>
</div>

</form>

<?php

	if(isset($_POST['save'])) {
		date_default_timezone_set($_SESSION['s_timezone']);
		$ddate = date("Y-m-d");
		$ttime = strftime("%H:%M", time());
		include_once("../includes/cltadmin.php");
		$oAct = new cltadmin;	
		$query = "select member_id from ".$fltfile;
		$result = mysql_query($query) or die(mysql_error());
		while ($row1 = mysql_fetch_array($result)) {
			extract($row1);
			$mem = $member_id;

			$oAct->client_id = $mem;
			$oAct->ddate = $ddate;
			$oAct->ttime = $ttime;
			$oAct->activity = $_REQUEST['tanote'];
			$oAct->status = 'Sealed';
			$oAct->staff_id = $user_id;
			$oAct->sub_id = $sub_id;
			$oAct->contact = 'Informative';

			$actid = $oAct->AddActivity();
		}
	?>

	<script>
	this.close();		
    </script>
	<?php
	}
?>

</body>

</html>