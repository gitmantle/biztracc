<?php
session_start();
//ini_set('display_errors', true);

$dbase = $_SESSION['s_dbase'];
$adloc = $_REQUEST['adloc'];
$adtype = $_REQUEST['adtype'];
$stno = $_REQUEST['stno'];
$sad1 = $_REQUEST['sad1'];
$bad1 = $_REQUEST['bad1'];
$po = $_REQUEST['po'];
$rd = $_REQUEST['rd'];
$suburb = $_REQUEST['suburb'];
$town = $_REQUEST['town'];

	require("../../db.php");
	mysql_select_db($dbase) or die(mysql_error());	

	$pc_options = '<option value="0">Select Post Code</option>';
	switch ($adloc) {
		case 'Street':
			$query = "select* from streets where upper(street) like upper('".$sad1."%')" ;
			$result = mysql_query($query) or die($query);
			while ($row = mysql_fetch_array($result)) {
				extract($row);
				$pc_options .= '<option value="'.'s~'.$street.'~'.$suburb.'~'.$area.'~'.$postcode.'">'.$street.', '.$suburb.', '.$area.', '.$postcode.'</option>';
			}
			break;
		case 'Post Box':
			$query = "select * from boxes where upper(post_office) like upper('".$po."%')";
			$result = mysql_query($query) or die($query);
			while ($row = mysql_fetch_array($result)) {
				extract($row);
				$pc_options .= '<option value="'.'p~'.$post_office.'~'.$city.'~'.$postcode.'">'.$post_office.', '.$city.', '.$postcode.'</option>';
			}
			break;
		case 'Rural Delivery':
			$query = "select * from rural where upper(town) like upper('".$town."%')" ;
			$result = mysql_query($query) or die($query);
			while ($row = mysql_fetch_array($result)) {
				extract($row);
				$pc_options .= '<option value="'.'r~RD'.$rd.'~'.$town.'~'.$postcode.'">'.'RD'.$rd.', '.$town.', '.$postcode.'</option>';
			}
			break;
	}
	
	echo $pc_options;


?>