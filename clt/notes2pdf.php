<?php
session_start();
$usersession = $_SESSION['usersession'];

$cluid = $_SESSION['s_memberid'];

date_default_timezone_set($_SESSION['s_timezone']);

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$userid = $row['user_id'];
$uname = $row['uname'];
$subscriber = $subid;

$heading = $_REQUEST['heading'];

$db->query("select subname from subscribers where subid = ".$subid);
$row = $db->single();
extract($row);
$coyname = $subname;
$cltdb = $_SESSION['s_cltdb'];

$arrprint = $_REQUEST['notes'];
if (strlen($arrprint) == 0) {
	echo '<script>';
	echo 'alert("No Notes selected");';
	echo 'this.close();';
	echo '</script>';
	return false;
}

$aprint = explode(',',$arrprint);
$notefile = "ztmp".$userid."_note";

$db->query("drop table if exists ".$cltdb.".".$notefile);
$db->execute();

$db->query("create table ".$cltdb.".".$notefile." (uid integer(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, note_id int, ddate date, ttime char(10), note text, author varchar(45))  engine myisam");
$db->execute();

foreach ($aprint as $value) {
	$db->query("select activities.activities_id,activities.ddate as d,activities.ttime as t,activities.activity,users.ufname,users.ulname from ".$cltdb.".activities,users where activities.staff_id = users.uid and activities.activities_id = ".$value);
	$rows = $db->resultset();
	foreach ($rows as $row) {
		extract($row);
		
		$db->query("insert into ".$cltdb.".".$notefile." (note_id,ddate,ttime,note,author) values (:note_id,:ddate,:ttime,:note,:author)");
		$db->bind(':note_id', $activities_id);
		$db->bind(':ddate', $d);
		$db->bind(':ttime', $t);
		$db->bind(':note', $activity);
		$db->bind(':author', 'by '.$uname);
		$db->execute();
	}
}


if (file_exists('text/pdfnotes'.$userid.'.txt')) {
	unlink('text/pdfnotes'.$userid.'.txt');
}
$fp = fopen('text/pdfnotes'.$userid.'.txt', 'w'); 

$cstring = "H;H;N;".$coyname."\n";
fwrite($fp, $cstring);
$cstring = "H;H;N;".$heading."\n";
fwrite($fp, $cstring);

$db->query("select ddate,ttime,note,author from ".$cltdb.".".$notefile); 
$rows = $db->resultset();
foreach ($rows as $row) {
	extract($row);

	$ap = "B;D;N";
	$cstring = $ap.";".$ddate.";".$ttime.";".$author."\n";
	fwrite($fp, $cstring);
	
	$ap = "B;N;N";
	$note=preg_replace( '/\r\n/', ' - ', trim($note) );		
	$nt = str_replace("\'","'",$note);
	$cstring = $ap.";".$nt."\n";
	fwrite($fp, $cstring);
	
}
      
fclose($fp); 


$db->closeDB();

$host  = $_SERVER['HTTP_HOST'];
$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
$extra = "text/notespdf.php?coy=".$userid;
header("Location: http://$host$uri/$extra");
exit;



?>