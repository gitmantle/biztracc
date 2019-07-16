<?php
session_start();
$userid = md5(trim($_REQUEST['username']));
$password = md5(trim($_REQUEST['password']));

$dbase = 'logtracc';

$retval = '';

if (isset($_REQUEST['lastname']) && strlen($_REQUEST['lastname']) > 0) {
	$lname = $_REQUEST['lastname'];
} else {
	$retval = 'No name selected';
}

if ($retval == '') {
	

	$server = "mysql3.webhost.co.nz";
	$user = "logtracc9";
	$pwd = "dun480can";
	$connect = mysql_connect($server,$user,$pwd) or die("Check your database connection");	mysql_select_db($dbase) or die(mysql_error());
	mysql_select_db($dbase) or die(mysql_error());

	$query = "select * from users where username = '".$userid."' and upwd = '".$password."'";
	$result = mysql_query($query) or die(mysql_error());

	if (mysql_num_rows($result) > 0 ) {
		$row = mysql_fetch_array($result);
		extract($row);

		$dbase = "sub30";
		
		mysql_select_db($dbase) or die(mysql_error());


		$query = 'SELECT member_id,sub_id,lastname,firstname FROM members WHERE lastname like "'.$lname.'%" order by lastname, firstname';
		$result = mysql_query($query) or die(mysql_error().$query);
		
		$xml_output  = "<?xml version=\"1.0\"?>\n";
		$xml_output .= "<members>\n";
		
		for($x = 0 ; $x < mysql_num_rows($result) ; $x++){
			$row = mysql_fetch_assoc($result);
			$xml_output .= "\t<Member>\n";
			$xml_output .= "\t\t<member_id>" . $row[member_id] . "</member_id>\n";
			$xml_output .= "\t\t<sub_id>" . $row[sub_id] . "</sub_id>\n";
			$xml_output .= "\t\t<lastname>" . str_replace("&","and",$row[lastname]) . "</lastname>\n";
			$xml_output .= "\t\t<firstname>" . $row[firstname] . "</firstname>\n";
			$xml_output .= "\t</Member>\n";
		}
		
		$xml_output .= "</members>"; 
		
		$retval = $xml_output;
		
	} else {
		
		$retval = '10-Username and/or password invalid';
	}
}

echo $retval;

?>
