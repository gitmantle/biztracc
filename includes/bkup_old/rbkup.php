<?php
session_start();

ini_set('display_errors', true);
$usersession = $_SESSION['usersession'];

include_once("../DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];
$subscriber = $subid;
$sname = $row['uname'];

if(isset($_SESSION['s_coyid'])) {
	$coyid = $_SESSION['s_coyid'];
}

$module = $_SESSION['s_module'];

if ($module == 'clt') {
	$hed = 'Client Management Database';
	$fl = 'Your client management backup files will all be named sub'.$subscriber.'_(date of backup)';
	$moddb = $_SESSION['s_cltdb'];
}
if ($module == 'fin') {
	$hed = 'Financial Management Database';
	$fl = 'Your financial management backup files will all be named fin'.$subscriber.'_'.$coyid.'_(date of backup)';
	$moddb = $_SESSION['s_findb'];
}
if ($module == 'log') {
	$hed = 'Trucking Database';
	$fl = 'Your logging backup files will all be named log'.$subscriber.'_'.$coyid.'_(date of backup)';
	$moddb = $_SESSION['s_logdb'];
}
if ($module == 'med') {
	$hed = 'Cmeds4u Database';
	$fl = 'Your cmeds backup files will all be named med'.$subscriber.'_'.$coyid.'_(date of backup)';
	$moddb = $_SESSION['s_meddb'];
}

/*
$table = 'ztmp'.$user_id.'_bkfiles';

$db->query("drop table if exists ".$moddb.".".$table);
$db->execute();

$db->query("create table ".$moddb.".".$table." ( uid int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, bkfile varchar(100) default '')  engine myisam");
$db->execute();


$fls = scandir("../../temp/");
$files = array();
foreach ($fls as $f) {
	if (substr($f,-3) == '.gz') {
		$files[] = $f;
	}
}


foreach ($files as $fn) {
	if ($module == 'clt') {
		$fl = explode('_',$fn);
		if ($fl[1] == 'sub'.$subscriber) {
			$db->query("insert into ".$moddb.".".$table." (bkfile) values ('".$fn."')"); 
			$db->execute();
		}
	}
	if ($module == 'fin') {
		$fl = explode('_',$fn);
		$coy = $fl[2];
		if (($fl[1] == 'fin'.$subscriber) && ($coy == $coyid)) {
			$db->query("insert into ".$moddb.".".$table." (bkfile) values ('".$fn."')"); 
			$db->execute();
		}
	}
	if ($module == 'log') {
		$fl = explode('_',$fn);
		$coy = $fl[2];
		if (($fl[1] == 'log'.$subscriber) && ($coy == $coyid)) {
			$db->query("insert into ".$moddb.".".$table." (bkfile) values ('".$fn."')"); 
			$db->execute();
		}
	}
} 

// populate backup files drop down
$db->query("select bkfile from ".$moddb.".".$table);
$rows = $db->resultset();
$bkfile_options = "<option value=\"0\">Select Backup File</option>";
foreach ($rows as $row) {
	extract($row);
	$selected = '';
	$bkfile_options .= '<option value="'.$bkfile.'"'.$selected.'>'.$bkfile.'</option>';
}

*/
$db->closeDB();

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN"  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en"  dir="ltr">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Restore Database</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">


</head>

<body>
<form >
<table align="center" width="70%">
<tr>
<td class="boxlabelcenter"><u>Restore your <?php echo $hed; ?></u></td>
</tr>
<tr>
  <td class="boxlabelcenter"><?php echo $fl; ?></td>
</tr>
<tr>
  <td align="center">This routine will restore your database from the backupfile you select. Be ABSOLUTELY SURE you want to overwrite your existing database with this data. You will LOSE ALL DATA in the existing dababase which will be replaced by the data from your backup. If you are not sure, backup your existing data first, then restore from an earlier generation of backup.</td>
</tr>
<tr>
  <td align="center">&nbsp;</td>
</tr>
<tr>
  <td align="center"><input type="submit" value="Continue" onclick="rbupload()"/></td>
</tr>
</table>
</form>
</body>