<?php
	include_once "../../includes/accesscontrol.php";
	include_once "../../includes/db.php";
	
	$cid = $_REQUEST['cid'];

	$title = $_REQUEST['title'];
	$pos = $_REQUEST['position'];
	if($pos == "") { $pos = "Unknown"; }
	$fname = $_REQUEST['fname'];
	$fname = addslashes($fname);
	$lname = $_REQUEST['lname'];
	$lname = addslashes($lname);
	$phone = $_REQUEST['phone'];
	$mobile = $_REQUEST['mobile'];
	$fax = $_REQUEST['fax'];
	$email = $_REQUEST['email'];
	$notes = $_REQUEST['notes'];
	$notes = addslashes($notes);

	$sql="INSERT INTO `contacts` (`id`,`pid`,`title`,`fname`,`lname`,`phone`,"
		."`fax`,`mobile`,`email`,`position`,`notes`) "
		."VALUES ('0','$cid','$title','$fname','$lname','$phone','$fax','$mobile',"
		."'$email','$pos','$notes');";
	$rst=mysql_query($sql) or die("dead");
	$last = mysql_insert_id();
	
	echo $cid.",".$last;
?>