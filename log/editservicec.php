<?php
session_start();

$vn = $_SESSION['s_vehicleno'];
$sid = $_REQUEST['id'];

require_once('../db.php');

$moduledb = $_SESSION['s_logdb'];
mysql_select_db($moduledb) or die(mysql_error());

$q = "select * from vehicles where vehicleno = '".$vn."'";
$r = mysql_query($q) or die (mysql_error());
$row = mysql_fetch_array($r);
extract($row);
$cd = explode('-',$cofdate);
$cof = $cd[2].'/'.$cd[1].'/'.$cd[0];
$mk = $make;


$q = "select * from servicec where service_id = ".$sid;
$r = mysql_query($q) or die (mysql_error());
$row = mysql_fetch_array($r);
extract($row);
$sd = explode('-',$ddate);
$servdate = $sd[2].'/'.$sd[1].'/'.$sd[0];

// populate No/Yes  drop downs
$arr = array('No', 'Yes');
$check1_options = "";
for($i = 0; $i < count($arr); $i++)	{
	if ($arr[$i] == $check1) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}	
	$check1_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
}
$arr = array('No', 'Yes');
$check2_options = "";
for($i = 0; $i < count($arr); $i++)	{
	if ($arr[$i] == $check2) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}	
	$check2_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
}
$arr = array('No', 'Yes');
$check3_options = "";
for($i = 0; $i < count($arr); $i++)	{
	if ($arr[$i] == $check3) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}	
	$check3_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
}
$arr = array('No', 'Yes');
$check4_options = "";
for($i = 0; $i < count($arr); $i++)	{
	if ($arr[$i] == $check4) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}	
	$check4_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
}
$arr = array('No', 'Yes');
$check5_options = "";
for($i = 0; $i < count($arr); $i++)	{
	if ($arr[$i] == $check5) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}	
	$check5_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
}
$arr = array('No', 'Yes');
$check6_options = "";
for($i = 0; $i < count($arr); $i++)	{
	if ($arr[$i] == $check6) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}	
	$check6_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
}
$arr = array('No', 'Yes');
$check7_options = "";
for($i = 0; $i < count($arr); $i++)	{
	if ($arr[$i] == $check7) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}	
	$check7_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
}
$check8_options = "";
for($i = 0; $i < count($arr); $i++)	{
	if ($arr[$i] == $check8) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}	
	$check8_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
}$arr = array('No', 'Yes');
$grease1_options = "";
for($i = 0; $i < count($arr); $i++)	{
	if ($arr[$i] == $grease1) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}	
	$grease1_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
}
$grease2_options = "";
for($i = 0; $i < count($arr); $i++)	{
	if ($arr[$i] == $grease2) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}	
	$grease2_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
}
$grease3_options = "";
for($i = 0; $i < count($arr); $i++)	{
	if ($arr[$i] == $grease3) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}	
	$grease3_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
}
$grease4_options = "";
for($i = 0; $i < count($arr); $i++)	{
	if ($arr[$i] == $grease4) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}	
	$grease4_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
}
$grease5_options = "";
for($i = 0; $i < count($arr); $i++)	{
	if ($arr[$i] == $grease5) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}	
	$grease5_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
}
$grease6_options = "";
for($i = 0; $i < count($arr); $i++)	{
	if ($arr[$i] == $grease6) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}	
	$grease6_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
}
$grease7_options = "";
for($i = 0; $i < count($arr); $i++)	{
	if ($arr[$i] == $grease7) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}	
	$grease7_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
}
$grease8_options = "";
for($i = 0; $i < count($arr); $i++)	{
	if ($arr[$i] == $grease8) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}	
	$grease8_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
}
$chassis1_options = "";
for($i = 0; $i < count($arr); $i++)	{
	if ($arr[$i] == $chassis1) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}	
	$chassis1_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
}
$chassis2_options = "";
for($i = 0; $i < count($arr); $i++)	{
	if ($arr[$i] == $chassis2) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}	
	$chassis2_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
}
$chassis3_options = "";
for($i = 0; $i < count($arr); $i++)	{
	if ($arr[$i] == $chassis3) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}	
	$chassis3_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
}
$chassis4_options = "";
for($i = 0; $i < count($arr); $i++)	{
	if ($arr[$i] == $chassis4) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}	
	$chassis4_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
}
$chassis5_options = "";
for($i = 0; $i < count($arr); $i++)	{
	if ($arr[$i] == $chassis5) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}	
	$chassis5_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
}
$visual1_options = "";
for($i = 0; $i < count($arr); $i++)	{
	if ($arr[$i] == $visual1) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}	
	$visual1_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
}
$visual2_options = "";
for($i = 0; $i < count($arr); $i++)	{
	if ($arr[$i] == $visual2) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}	
	$visual2_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
}
$visual3_options = "";
for($i = 0; $i < count($arr); $i++)	{
	if ($arr[$i] == $visual3) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}	
	$visual3_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
}
$visual4_options = "";
for($i = 0; $i < count($arr); $i++)	{
	if ($arr[$i] == $visual4) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}	
	$visual4_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
}
$visual5_options = "";
for($i = 0; $i < count($arr); $i++)	{
	if ($arr[$i] == $visual5) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}	
	$visual5_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
}
$exhaust1_options = "";
for($i = 0; $i < count($arr); $i++)	{
	if ($arr[$i] == $exhaust1) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}	
	$exhaust1_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
}
$exhaust2_options = "";
for($i = 0; $i < count($arr); $i++)	{
	if ($arr[$i] == $exhaust2) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}	
	$exhaust2_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
}
$exhaust3_options = "";
for($i = 0; $i < count($arr); $i++)	{
	if ($arr[$i] == $exhaust3) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}	
	$exhaust3_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
}
$drive1_options = "";
for($i = 0; $i < count($arr); $i++)	{
	if ($arr[$i] == $drive1) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}	
	$drive1_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
}
$drive2_options = "";
for($i = 0; $i < count($arr); $i++)	{
	if ($arr[$i] == $drive2) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}	
	$drive2_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
}
$drive3_options = "";
for($i = 0; $i < count($arr); $i++)	{
	if ($arr[$i] == $drive3) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}	
	$drive3_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
}
$steering1_options = "";
for($i = 0; $i < count($arr); $i++)	{
	if ($arr[$i] == $steering1) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}	
	$steering1_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
}
$steering2_options = "";
for($i = 0; $i < count($arr); $i++)	{
	if ($arr[$i] == $steering2) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}	
	$steering2_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
}
$steering3_options = "";
for($i = 0; $i < count($arr); $i++)	{
	if ($arr[$i] == $steering3) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}	
	$steering3_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
}
$suspension1_options = "";
for($i = 0; $i < count($arr); $i++)	{
	if ($arr[$i] == $suspension1) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}	
	$suspension1_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
}
$suspension2_options = "";
for($i = 0; $i < count($arr); $i++)	{
	if ($arr[$i] == $suspension2) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}	
	$suspension2_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
}
$suspension3_options = "";
for($i = 0; $i < count($arr); $i++)	{
	if ($arr[$i] == $suspension3) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}	
	$suspension3_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
}
$suspension4_options = "";
for($i = 0; $i < count($arr); $i++)	{
	if ($arr[$i] == $suspension4) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}	
	$suspension4_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
}
$suspension5_options = "";
for($i = 0; $i < count($arr); $i++)	{
	if ($arr[$i] == $suspension5) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}	
	$suspension5_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
}
$suspension6_options = "";
for($i = 0; $i < count($arr); $i++)	{
	if ($arr[$i] == $suspension6) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}	
	$suspension6_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
}
$suspension7_options = "";
for($i = 0; $i < count($arr); $i++)	{
	if ($arr[$i] == $suspension7) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}	
	$suspension7_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
}
$air1_options = "";
for($i = 0; $i < count($arr); $i++)	{
	if ($arr[$i] == $air1) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}	
	$air1_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
}
$air2_options = "";
for($i = 0; $i < count($arr); $i++)	{
	if ($arr[$i] == $air2) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}	
	$air2_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
}
$air3_options = "";
for($i = 0; $i < count($arr); $i++)	{
	if ($arr[$i] == $air3) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}	
	$air3_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
}
$fuel1_options = "";
for($i = 0; $i < count($arr); $i++)	{
	if ($arr[$i] == $fuel1) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}	
	$fuel1_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
}
$fuel2_options = "";
for($i = 0; $i < count($arr); $i++)	{
	if ($arr[$i] == $fuel2) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}	
	$fuel2_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
}
$body1_options = "";
for($i = 0; $i < count($arr); $i++)	{
	if ($arr[$i] == $body1) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}	
	$body1_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
}
$body2_options = "";
for($i = 0; $i < count($arr); $i++)	{
	if ($arr[$i] == $body2) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}	
	$body2_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
}
$body3_options = "";
for($i = 0; $i < count($arr); $i++)	{
	if ($arr[$i] == $body3) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}	
	$body3_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
}
$body4_options = "";
for($i = 0; $i < count($arr); $i++)	{
	if ($arr[$i] == $body4) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}	
	$body4_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
}
$turn1_options = "";
for($i = 0; $i < count($arr); $i++)	{
	if ($arr[$i] == $turn1) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}	
	$turn1_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
}
$turn2_options = "";
for($i = 0; $i < count($arr); $i++)	{
	if ($arr[$i] == $turn2) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}	
	$turn2_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
}
$turn3_options = "";
for($i = 0; $i < count($arr); $i++)	{
	if ($arr[$i] == $turn3) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}	
	$turn3_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
}
$turn4_options = "";
for($i = 0; $i < count($arr); $i++)	{
	if ($arr[$i] == $turn4) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}	
	$turn4_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
}
$electric1_options = "";
for($i = 0; $i < count($arr); $i++)	{
	if ($arr[$i] == $electric1) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}	
	$electric1_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
}
$electric2_options = "";
for($i = 0; $i < count($arr); $i++)	{
	if ($arr[$i] == $electric2) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}	
	$electric2_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
}
$electric3_options = "";
for($i = 0; $i < count($arr); $i++)	{
	if ($arr[$i] == $electric3) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}	
	$electric3_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
}
$electric4_options = "";
for($i = 0; $i < count($arr); $i++)	{
	if ($arr[$i] == $electric4) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}	
	$electric4_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
}
$electric5_options = "";
for($i = 0; $i < count($arr); $i++)	{
	if ($arr[$i] == $electric5) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}	
	$electric5_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
}
$electric6_options = "";
for($i = 0; $i < count($arr); $i++)	{
	if ($arr[$i] == $electric6) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}	
	$electric6_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
}
$electric7_options = "";
for($i = 0; $i < count($arr); $i++)	{
	if ($arr[$i] == $electric7) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}	
	$electric7_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
}
$electric8_options = "";
for($i = 0; $i < count($arr); $i++)	{
	if ($arr[$i] == $electric8) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}	
	$electric8_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
}
$gen1_options = "";
for($i = 0; $i < count($arr); $i++)	{
	if ($arr[$i] == $gen1) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}	
	$gen1_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
}
$gen2_options = "";
for($i = 0; $i < count($arr); $i++)	{
	if ($arr[$i] == $gen2) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}	
	$gen2_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
}
$gen3_options = "";
for($i = 0; $i < count($arr); $i++)	{
	if ($arr[$i] == $gen3) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}	
	$gen3_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
}
$gen4_options = "";
for($i = 0; $i < count($arr); $i++)	{
	if ($arr[$i] == $gen4) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}	
	$gen4_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
}
$gen5_options = "";
for($i = 0; $i < count($arr); $i++)	{
	if ($arr[$i] == $gen5) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}	
	$gen5_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
}
$gen6_options = "";
for($i = 0; $i < count($arr); $i++)	{
	if ($arr[$i] == $gen6) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}	
	$gen6_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
}
$gen7_options = "";
for($i = 0; $i < count($arr); $i++)	{
	if ($arr[$i] == $gen7) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}	
	$gen7_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
}
$gen8_options = "";
for($i = 0; $i < count($arr); $i++)	{
	if ($arr[$i] == $gen8) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}	
	$gen8_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
}
$replace1_options = "";
for($i = 0; $i < count($arr); $i++)	{
	if ($arr[$i] == $replace1) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}	
	$replace1_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
}
$replace2_options = "";
for($i = 0; $i < count($arr); $i++)	{
	if ($arr[$i] == $replace2) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}	
	$replace2_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
}
$replace3_options = "";
for($i = 0; $i < count($arr); $i++)	{
	if ($arr[$i] == $replace3) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}	
	$replace3_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
}
$replace4_options = "";
for($i = 0; $i < count($arr); $i++)	{
	if ($arr[$i] == $replace4) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}	
	$replace4_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
}
$replace5_options = "";
for($i = 0; $i < count($arr); $i++)	{
	if ($arr[$i] == $replace5) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}	
	$replace5_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
}
$replace6_options = "";
for($i = 0; $i < count($arr); $i++)	{
	if ($arr[$i] == $replace6) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}	
	$replace6_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
}
$replace7_options = "";
for($i = 0; $i < count($arr); $i++)	{
	if ($arr[$i] == $replace7) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}	
	$replace7_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
}
$replace8_options = "";
for($i = 0; $i < count($arr); $i++)	{
	if ($arr[$i] == $replace8) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}	
	$replace8_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
}
$road1_options = "";
for($i = 0; $i < count($arr); $i++)	{
	if ($arr[$i] == $road1) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}	
	$road1_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
}

date_default_timezone_set($_SESSION['s_timezone']);
$ddate = date("d/m/Y");
$hdate = date("Y-m-d");

require_once("../includes/backgrounds.php");
$theme = $_SESSION['deftheme'];


?>


<html>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Edit Service B</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<link type="text/css" href="../includes/jquery/themes/<?php echo $theme; ?>/jquery.ui.all.css" rel="stylesheet" />
<script src="../includes/jquery/js/jquery.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery-ui-custom.min.js" type="text/javascript"></script>
<script type="text/javascript" src="../includes/jquery/ui/jquery.ui.widget.js"></script>

<script>

function stopRKey(evt) {
  var evt = (evt) ? evt : ((event) ? event : null);
  var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
  if ((evt.keyCode == 13) && (node.type=="text"))  {return false;}
}


function ajaxGetWSstaff(wshop) {
	var ws = wshop.split('~');
	var id = ws[0];
	//populate workshop staff list
		$.get("includes/ajaxGetWSstaff.php", {id: id}, function(data){
			$("#serviceman").append(data);
		});	
}

function post() {

	//add validation here if required.
	var wshop = document.getElementById('wshop').value;
	var jobno = document.getElementById('jobno').value;
	var serviceman = document.getElementById('serviceman').value;
	var nextdue = document.getElementById('nextdue').value;
	
	var ok = "Y";
	if (wshop == 0) {
		alert("Please select a workshop.");
		ok = "N";
		return false;
	}
	if (jobno == "") {
		alert("Please enter a job number.");
		ok = "N";
		return false;
	}
	if (serviceman == "") {
		alert("Please select a serviceman.");
		ok = "N";
		return false;
	}
	if (nextdue == "") {
		alert("Please enter Kms when next C service due.");
		ok = "N";
		return false;
	}
	
	
	if (ok == "Y") {
		document.getElementById('savebutton').value = "Y";
		document.getElementById('form1').submit();
	}
	
	
}


</script>

<style type="text/css">
<!--
.style1 {font-size: large}
-->
</style>
</head>


<body>
<form name="form1" id="form1" method="post" >
 <input type="hidden" name="savebutton" id="savebutton" value="N">
 <table width="970" border="0" align="center" cellspacing="4" bgcolor="<?php echo $bgcolor; ?>">
    <tr>
      <td colspan="4" bgcolor="<?php echo $bghead; ?>" align="center"><label style="color: <?php echo $thfont; ?>"><strong> B Service for <?php echo $vn; ?></strong></label></td>
    </tr>
    <tr>
      <td class="boxlabelleft">Workshop
      <input type="text" name="wshop" id="wshop" value="<?php echo $workshop; ?>" readonly></td>
      <td class="boxlabelleft">Reg No
      <input type="text" name="regno" id="regno" value="<?php echo $regno; ?>" readonly></td>
      <td class="boxlabelleft">Job No
        <input type="text" name="jobno" id="jobno" value="<?php echo $jobno; ?>" readonly></td>
      <td class="boxlabelleft">Job Date
      <input type="text" name="ddate" id="ddate" value="<?php echo $servdate; ?>" readonly></td>
    </tr>
    <tr>
      <td class="boxlabelleft">Make
      <input type="text" name="make" id="make" value="<?php echo $mk; ?>" readonly></td>
      <td class="boxlabelleft">COF
      <input type="text" name="cofdue" id="cofdue" readonly value="<?php echo $cof; ?>"></td>
      <td class="boxlabelleft">Hub Km
      <input type="text" name="hubo" id="hubo"  readonly value="<?php echo $hubo; ?>"></td>
      <td class="boxlabelleft">Speedo Km
      <input type="text" name="speedo" id="speedo"  readonly value="<?php echo $speedo; ?>"></td>
    </tr>
 </table>
 <table width="970" border="0" align="center" cellspacing="4" bgcolor="<?php echo $bgcolor; ?>">
    <tr bgcolor="#FFFF33">
      <td colspan="4" align="center">This check sheet is to be used in conjunction with manufacturers specifications and service requirements</td>
    </tr>
    <tr>
      <td class="boxlabelleft">CHECK</td>
      <td class="boxlabelleft">OK / Done</td>
      <td class="boxlabelleft">FRONT &amp; REAR SUSPENSION</td>
      <td class="boxlabelleft">OK / Done</td>
    </tr>
    <tr>
      <td class="boxlabelleft" bgcolor="#99FFFF">Valve clearances &amp; adjust</td>
      <td class="boxlabelleft" bgcolor="#99FFFF"><select name="check1" id="check1">
			<?php echo $check1_options; ?>
        </select></td>
      <td class="boxlabelleft" bgcolor="#99FF99">Spring leaves &amp; clamps</td>
      <td class="boxlabelleft" bgcolor="#99FF99"><select name="suspension1" id="suspension1">
			<?php echo $suspension1_options; ?>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabelleft" bgcolor="#99FFFF">Air filter</td>
      <td class="boxlabelleft" bgcolor="#99FFFF"><select name="check2" id="check2">
			<?php echo $check2_options; ?>
      </select></td>
      <td class="boxlabelleft" bgcolor="#99FF99">Air bags - chafing / mounting</td>
      <td class="boxlabelleft" bgcolor="#99FF99"><select name="suspension2" id="suspension2">
			<?php echo $suspension2_options; ?>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabelleft" bgcolor="#99FFFF">Check &amp; re-pack front wheel bearings</td>
      <td class="boxlabelleft" bgcolor="#99FFFF"><select name="check3" id="check3">
			<?php echo $check3_options; ?>
      </select></td>
      <td class="boxlabelleft" bgcolor="#99FF99">Air bags - leveling valve</td>
      <td class="boxlabelleft" bgcolor="#99FF99"><select name="suspension3" id="suspension3">
			<?php echo $suspension3_options; ?>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabelleft" bgcolor="#99FFFF">Remove belts check fan hub &amp; bearings</td>
      <td class="boxlabelleft" bgcolor="#99FFFF"><select name="check4" id="check4">
			<?php echo $check4_options; ?>
      </select></td>
      <td class="boxlabelleft" bgcolor="#99FF99">Brackets, shackles, pins &amp; bushes</td>
      <td class="boxlabelleft" bgcolor="#99FF99"><select name="suspension4" id="suspension4">
			<?php echo $suspension4_options; ?>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabelleft" bgcolor="#99FFFF">Clutch &amp; adjust if required</td>
      <td class="boxlabelleft" bgcolor="#99FFFF"><select name="check5" id="check5">
			<?php echo $check5_options; ?>
      </select></td>
      <td class="boxlabelleft" bgcolor="#99FF99">U bolts &amp; centre bolts</td>
      <td class="boxlabelleft" bgcolor="#99FF99"><select name="suspension5" id="suspension5">
			<?php echo $suspension5_options; ?>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabelleft" bgcolor="#99FFFF">Clutch &amp; brake fluid level</td>
      <td class="boxlabelleft" bgcolor="#99FFFF"><select name="check6" id="check6">
			<?php echo $check6_options; ?>
      </select></td>
      <td class="boxlabelleft" bgcolor="#99FF99">Torque rods &amp; panhard rods</td>
      <td class="boxlabelleft" bgcolor="#99FF99"><select name="suspension6" id="suspension6">
			<?php echo $suspension6_options; ?>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabelleft" bgcolor="#99FFFF">Battery condition &amp; level</td>
      <td class="boxlabelleft" bgcolor="#99FFFF"><select name="check7" id="check7">
			<?php echo $check7_options; ?>
      </select></td>
      <td class="boxlabelleft" bgcolor="#99FF99">Spring mounts &amp; hangars</td>
      <td class="boxlabelleft" bgcolor="#99FF99"><select name="suspension7" id="suspension7">
			<?php echo $suspension7_options; ?>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabelleft" bgcolor="#99FFFF">Top up auto lube if required</td>
      <td class="boxlabelleft" bgcolor="#99FFFF"><select name="check8" id="check8">
        <?php echo $check7_options; ?>
      </select></td>
      <td class="boxlabelleft" >AIR SYSTEM</td>
      <td class="boxlabelleft" >&nbsp;</td>
    </tr>
    <tr>
      <td class="boxlabelleft" >GREASE</td>
      <td class="boxlabelleft" >&nbsp;</td>
      <td class="boxlabelleft" bgcolor="#99FF99">Air leaks - applied &amp; released</td>
      <td class="boxlabelleft" bgcolor="#99FF99"><select name="air1" id="air1">
			<?php echo $air1_options; ?>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabelleft" bgcolor="#99FFFF">Turntable plate, pivots &amp; jaws</td>
      <td class="boxlabelleft" bgcolor="#99FFFF"><select name="grease1" id="grease1">
			<?php echo $grease1_options; ?>
      </select></td>
      <td class="boxlabelleft" bgcolor="#99FF99">Tank &amp; mounting</td>
      <td class="boxlabelleft" bgcolor="#99FF99"><select name="air2" id="air2">
			<?php echo $air2_options; ?>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabelleft" bgcolor="#99FFFF">Driveline</td>
      <td class="boxlabelleft" bgcolor="#99FFFF"><select name="grease2" id="grease2">
			<?php echo $grease2_options; ?>
      </select></td>
      <td class="boxlabelleft" bgcolor="#99FF99">Hose &amp; pipework chafing</td>
      <td class="boxlabelleft" bgcolor="#99FF99"><select name="air3" id="air3">
			<?php echo $air3_options; ?>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabelleft" bgcolor="#99FFFF">Kingpins</td>
      <td class="boxlabelleft" bgcolor="#99FFFF"><select name="grease3" id="grease3">
			<?php echo $grease3_options; ?>
      </select></td>
      <td class="boxlabelleft" >FUEL SYSTEM</td>
      <td class="boxlabelleft" >&nbsp;</td>
    </tr>
    <tr>
      <td class="boxlabelleft" bgcolor="#99FFFF">Steering joints</td>
      <td class="boxlabelleft" bgcolor="#99FFFF"><select name="grease4" id="grease4">
			<?php echo $grease4_options; ?>
      </select></td>
      <td class="boxlabelleft" bgcolor="#99FF99">Leaks</td>
      <td class="boxlabelleft" bgcolor="#99FF99"><select name="fuel1" id="fuel1">
			<?php echo $fuel1_options; ?>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabelleft" bgcolor="#99FFFF">Supsension pivots</td>
      <td class="boxlabelleft" bgcolor="#99FFFF"><select name="grease5" id="grease5">
			<?php echo $grease5_options; ?>
      </select></td>
      <td class="boxlabelleft" bgcolor="#99FF99">Pipework tank &amp; mounting</td>
      <td class="boxlabelleft" bgcolor="#99FF99"><select name="fuel2" id="fuel2">
			<?php echo $fuel2_options; ?>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabelleft" bgcolor="#99FFFF">Clutch &amp; throttle linkage</td>
      <td class="boxlabelleft" bgcolor="#99FFFF"><select name="grease6" id="grease6">
			<?php echo $grease6_options; ?>
      </select></td>
      <td class="boxlabelleft" >MUDGUARDS &amp; BODY</td>
      <td class="boxlabelleft" >&nbsp;</td>
    </tr>
    <tr>
      <td class="boxlabelleft" bgcolor="#99FFFF">Lubricate doors &amp; hinges</td>
      <td class="boxlabelleft" bgcolor="#99FFFF"><select name="grease7" id="grease7">
			<?php echo $grease7_options; ?>
      </select></td>
      <td class="boxlabelleft" bgcolor="#99FF99">Mountings &amp; brackets</td>
      <td class="boxlabelleft" bgcolor="#99FF99"><select name="body1" id="body1">
			<?php echo $body1_options; ?>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabelleft" bgcolor="#99FFFF">Any other pivots &amp; linkages</td>
      <td class="boxlabelleft" bgcolor="#99FFFF"><select name="grease8" id="grease8">
			<?php echo $grease8_options; ?>
      </select></td>
      <td class="boxlabelleft" bgcolor="#99FF99">Mudflaps</td>
      <td class="boxlabelleft" bgcolor="#99FF99"><select name="body2" id="body2">
			<?php echo $body2_options; ?>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabelleft">CHASSIS</td>
      <td class="boxlabelleft">&nbsp;</td>
      <td class="boxlabelleft" bgcolor="#99FF99">Body cracks</td>
      <td class="boxlabelleft" bgcolor="#99FF99"><select name="body3" id="body3">
			<?php echo $body3_options; ?>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabelleft" bgcolor="#99FFFF">Check &amp; adjust front wheel bearings</td>
      <td class="boxlabelleft" bgcolor="#99FFFF"><select name="chassis1" id="chassis1">
			<?php echo $chassis1_options; ?>
      </select></td>
      <td class="boxlabelleft" bgcolor="#99FF99">Mezz floor security &amp; condition</td>
      <td class="boxlabelleft" bgcolor="#99FF99"><select name="body4" id="body4">
			<?php echo $body4_options; ?>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabelleft" bgcolor="#99FFFF">Check king pins</td>
      <td class="boxlabelleft" bgcolor="#99FFFF"><select name="chassis2" id="chassis2">
			<?php echo $chassis2_options; ?>
      </select></td>
      <td class="boxlabelleft">TURNTABLE / RINGFEEDER</td>
      <td class="boxlabelleft">&nbsp;</td>
    </tr>
    <tr>
      <td class="boxlabelleft" bgcolor="#99FFFF">Check &amp; adjust brakes</td>
      <td class="boxlabelleft" bgcolor="#99FFFF"><select name="chassis3" id="chassis3">
			<?php echo $chassis3_options; ?>
      </select></td>
      <td class="boxlabelleft" bgcolor="#99FF99">Mounting bolts</td>
      <td class="boxlabelleft" bgcolor="#99FF99"><select name="turn1" id="turn1">
			<?php echo $turn1_options; ?>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabelleft" bgcolor="#99FFFF">Check &amp; adjust clutch</td>
      <td class="boxlabelleft" bgcolor="#99FFFF"><select name="chassis4" id="chassis4">
			<?php echo $chassis4_options; ?>
      </select></td>
      <td class="boxlabelleft" bgcolor="#99FF99">Cracks</td>
      <td class="boxlabelleft" bgcolor="#99FF99"><select name="turn2" id="turn2">
			<?php echo $turn2_options; ?>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabelleft" bgcolor="#99FFFF">Cross members - cracks / loose</td>
      <td class="boxlabelleft" bgcolor="#99FFFF"><select name="chassis5" id="chassis5">
			<?php echo $chassis5_options; ?>
      </select></td>
      <td class="boxlabelleft" bgcolor="#99FF99">Fifth wheel service per schedule</td>
      <td class="boxlabelleft" bgcolor="#99FF99"><select name="turn3" id="turn3">
			<?php echo $turn3_options; ?>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabelleft">VISUAL INSPECTION OF</td>
      <td class="boxlabelleft">&nbsp;</td>
      <td class="boxlabelleft" bgcolor="#99FF99">Ringfeeder operation</td>
      <td class="boxlabelleft" bgcolor="#99FF99"><select name="turn4" id="turn4">
			<?php echo $turn4_options; ?>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabelleft" bgcolor="#99FFFF">Engine oil leaks</td>
      <td class="boxlabelleft" bgcolor="#99FFFF"><select name="visual1" id="visual1">
			<?php echo $visual1_options; ?>
      </select></td>
      <td class="boxlabelleft">ELECTRICAL</td>
      <td class="boxlabelleft">&nbsp;</td>
    </tr>
    <tr>
      <td class="boxlabelleft" bgcolor="#99FFFF">Transmission oil leaks</td>
      <td class="boxlabelleft" bgcolor="#99FFFF"><select name="visual2" id="visual2">
			<?php echo $visual2_options; ?>
      </select></td>
      <td class="boxlabelleft" bgcolor="#99FF99">Headlamps - both beams</td>
      <td class="boxlabelleft" bgcolor="#99FF99"><select name="electric1" id="electric1">
			<?php echo $electric1_options; ?>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabelleft" bgcolor="#99FFFF">Diff &amp; axle oil leaks</td>
      <td class="boxlabelleft" bgcolor="#99FFFF"><select name="visual3" id="visual3">
			<?php echo $visual3_options; ?>
      </select></td>
      <td class="boxlabelleft" bgcolor="#99FF99">Park lights</td>
      <td class="boxlabelleft" bgcolor="#99FF99"><select name="electric2" id="electric2">
			<?php echo $electric2_options; ?>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabelleft" bgcolor="#99FFFF">Power steering system &amp; steering box</td>
      <td class="boxlabelleft" bgcolor="#99FFFF"><select name="visual4" id="visual4">
			<?php echo $visual4_options; ?>
      </select></td>
      <td class="boxlabelleft" bgcolor="#99FF99">Turn indicators - front, side &amp; rear</td>
      <td class="boxlabelleft" bgcolor="#99FF99"><select name="electric3" id="electric3">
			<?php echo $electric3_options; ?>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabelleft" bgcolor="#99FFFF">All drive belts</td>
      <td class="boxlabelleft" bgcolor="#99FFFF"><select name="visual5" id="visual5">
			<?php echo $visual5_options; ?>
      </select></td>
      <td class="boxlabelleft" bgcolor="#99FF99">Roof lights</td>
      <td class="boxlabelleft" bgcolor="#99FF99"><select name="electric4" id="electric4">
			<?php echo $electric4_options; ?>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabelleft">EXHAUST</td>
      <td class="boxlabelleft">&nbsp;</td>
      <td class="boxlabelleft" bgcolor="#99FF99">Tail lights</td>
      <td class="boxlabelleft" bgcolor="#99FF99"><select name="electric5" id="electric5">
			<?php echo $electric5_options; ?>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabelleft" bgcolor="#99FFFF">Leaks</td>
      <td class="boxlabelleft" bgcolor="#99FFFF"><select name="exhaust1" id="exhaust1">
			<?php echo $exhaust1_options; ?>
      </select></td>
      <td class="boxlabelleft" bgcolor="#99FF99">Brake lights</td>
      <td class="boxlabelleft" bgcolor="#99FF99"><select name="electric6" id="electric6">
			<?php echo $electric6_options; ?>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabelleft" bgcolor="#99FFFF">Pipwork &amp; clamps</td>
      <td class="boxlabelleft" bgcolor="#99FFFF"><select name="exhaust2" id="exhaust2">
			<?php echo $exhaust2_options; ?>
      </select></td>
      <td class="boxlabelleft" bgcolor="#99FF99">Number plate light</td>
      <td class="boxlabelleft" bgcolor="#99FF99"><select name="electric7" id="electric7">
			<?php echo $electric7_options; ?>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabelleft" bgcolor="#99FFFF">Muffler &amp; mountings</td>
      <td class="boxlabelleft" bgcolor="#99FFFF"><select name="exhaust3" id="exhaust3">
			<?php echo $exhaust3_options; ?>
      </select></td>
      <td class="boxlabelleft" bgcolor="#99FF99">Reflectors</td>
      <td class="boxlabelleft" bgcolor="#99FF99"><select name="electric8" id="electric8">
			<?php echo $electric8_options; ?>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabelleft">DRIVE LINE</td>
      <td class="boxlabelleft">&nbsp;</td>
      <td class="boxlabelleft">GENERAL</td>
      <td class="boxlabelleft">&nbsp;</td>
    </tr>
    <tr>
      <td class="boxlabelleft" bgcolor="#99FFFF">Universal joints</td>
      <td class="boxlabelleft" bgcolor="#99FFFF"><select name="drive1" id="drive1">
			<?php echo $drive1_options; ?>
      </select></td>
      <td class="boxlabelleft" bgcolor="#99FF99">Wheels, nuts / studs</td>
      <td class="boxlabelleft" bgcolor="#99FF99"><select name="gen1" id="gen1">
			<?php echo $gen1_options; ?>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabelleft" bgcolor="#99FFFF">Hangar bearing</td>
      <td class="boxlabelleft" bgcolor="#99FFFF"><select name="drive2" id="drive2">
			<?php echo $drive2_options; ?>
      </select></td>
      <td class="boxlabelleft" bgcolor="#99FF99">Tyres - damage, wear, match</td>
      <td class="boxlabelleft" bgcolor="#99FF99"><select name="gen2" id="gen2">
        <?php echo $gen2_options; ?>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabelleft" bgcolor="#99FFFF">Diff flange</td>
      <td class="boxlabelleft" bgcolor="#99FFFF"><select name="drive3" id="drive3">
			<?php echo $drive3_options; ?>
      </select></td>
      <td class="boxlabelleft" bgcolor="#99FF99">Engine mounts</td>
      <td class="boxlabelleft" bgcolor="#99FF99"><select name="gen3" id="gen3">
        <?php echo $gen3_options; ?>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabelleft">STEERING</td>
      <td class="boxlabelleft">&nbsp;</td>
      <td class="boxlabelleft" bgcolor="#99FF99">Door hinges, catches &amp; locks</td>
      <td class="boxlabelleft" bgcolor="#99FF99"><select name="gen4" id="gen4">
        <?php echo $gen4_options; ?>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabelleft" bgcolor="#99FFFF">Steering box adjustment</td>
      <td class="boxlabelleft" bgcolor="#99FFFF"><select name="steering1" id="steering1">
			<?php echo $steering1_options; ?>
      </select></td>
      <td class="boxlabelleft" bgcolor="#99FF99">Wiper blades &amp; arms</td>
      <td class="boxlabelleft" bgcolor="#99FF99"><select name="gen5" id="gen5">
        <?php echo $gen5_options; ?>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabelleft" bgcolor="#99FFFF">Steering box mounting</td>
      <td class="boxlabelleft" bgcolor="#99FFFF"><select name="steering2" id="steering2">
			<?php echo $steering2_options; ?>
      </select></td>
      <td class="boxlabelleft" bgcolor="#99FF99">Driver controls &amp; instruments</td>
      <td class="boxlabelleft" bgcolor="#99FF99"><select name="gen6" id="gen6">
        <?php echo $gen6_options; ?>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabelleft" bgcolor="#99FFFF">Tie rod &amp; drag link ends</td>
      <td class="boxlabelleft" bgcolor="#99FFFF"><select name="steering3" id="steering3">
			<?php echo $steering3_options; ?>
      </select></td>
      <td class="boxlabelleft" bgcolor="#99FF99">Tail lift &amp; hoist hydraulic oil level</td>
      <td class="boxlabelleft" bgcolor="#99FF99"><select name="gen7" id="gen7">
        <?php echo $gen7_options; ?>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabelleft">REPLACE</td>
      <td class="boxlabelleft">&nbsp;</td>
      <td class="boxlabelleft" bgcolor="#99FF99">Tail lift &amp; hoist operation</td>
      <td class="boxlabelleft" bgcolor="#99FF99"><select name="gen8" id="gen8">
        <?php echo $gen8_options; ?>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabelleft" bgcolor="#99FFFF">Engine oil &amp; filter</td>
      <td class="boxlabelleft" bgcolor="#99FFFF"><select name="replace1" id="replace1">
        <?php echo $replace1_options; ?>
      </select></td>
      <td class="boxlabelleft">&nbsp;</td>
      <td class="boxlabelleft">&nbsp;</td>
    </tr>
    <tr>
      <td class="boxlabelleft" bgcolor="#99FFFF">Fuel filter</td>
      <td class="boxlabelleft" bgcolor="#99FFFF"><select name="replace2" id="replace2">
        <?php echo $replace2_options; ?>
      </select></td>
      <td class="boxlabelleft">&nbsp;</td>
      <td class="boxlabelleft">&nbsp;</td>
    </tr>
    <tr>
      <td class="boxlabelleft" bgcolor="#99FFFF">Transmission oil &amp;filter</td>
      <td class="boxlabelleft" bgcolor="#99FFFF"><select name="replace3" id="replace3">
        <?php echo $replace3_options; ?>
      </select></td>
      <td class="boxlabelleft">&nbsp;</td>
      <td class="boxlabelleft">&nbsp;</td>
    </tr>
    <tr>
      <td class="boxlabelleft" bgcolor="#99FFFF">Power steering oil &amp; filter</td>
      <td class="boxlabelleft" bgcolor="#99FFFF"><select name="replace4" id="replace4">
        <?php echo $replace4_options; ?>
      </select></td>
      <td class="boxlabelleft">&nbsp;</td>
      <td class="boxlabelleft">&nbsp;</td>
    </tr>
    <tr>
      <td class="boxlabelleft" bgcolor="#99FFFF">Differential oil</td>
      <td class="boxlabelleft" bgcolor="#99FFFF"><select name="replace5" id="replace5">
        <?php echo $replace5_options; ?>
      </select></td>
      <td class="boxlabelleft">&nbsp;</td>
      <td class="boxlabelleft">&nbsp;</td>
    </tr>
    <tr>
      <td class="boxlabelleft" bgcolor="#99FFFF">Coolant at 24 months &amp; filter</td>
      <td class="boxlabelleft" bgcolor="#99FFFF"><select name="replace6" id="replace6">
        <?php echo $replace6_options; ?>
      </select></td>
      <td class="boxlabelleft">&nbsp;</td>
      <td class="boxlabelleft">&nbsp;</td>
    </tr>
    <tr>
      <td class="boxlabelleft" bgcolor="#99FFFF">Clutch &amp; brake fluid at 24 months</td>
      <td class="boxlabelleft" bgcolor="#99FFFF"><select name="replace7" id="replace7">
        <?php echo $replace7_options; ?>
      </select></td>
      <td class="boxlabelleft">&nbsp;</td>
      <td class="boxlabelleft">&nbsp;</td>
    </tr>
    <tr>
      <td class="boxlabelleft" bgcolor="#99FFFF">Air dryer desiccant at 24 months</td>
      <td class="boxlabelleft" bgcolor="#99FFFF"><select name="replace8" id="replace8">
        <?php echo $replace8_options; ?>
      </select></td>
      <td class="boxlabelleft">ROAD TEST</td>
      <td class="boxlabelleft">&nbsp;</td>
    </tr>
    <tr>
      <td class="boxlabelleft">COMMENTS</td>
      <td class="boxlabelleft">&nbsp;</td>
      <td class="boxlabelleft" bgcolor="#FF9900">Fill out lube sticker - use SPEEDO Km</td>
      <td class="boxlabelleft" bgcolor="#FF9900"><select name="road1" id="road1">
        <?php echo $road1_options; ?>
      </select></td>
    </tr>
    <tr>
      <td colspan="4" bgcolor="#99FFFF" class="boxlabelleft"><textarea name="comments" id="comments" cols="110" rows="2"><?php echo $comments; ?></textarea></td>
    </tr>
	<tr>
      <td colspan="3" class="boxlabelleft">Serviceman
      <input type="text" name="serviceman" id="serviceman" value="<?php echo $serviceman; ?>" readonly>
      Next C Service due at Kms
      <input type="text" name="nextdue" id="nextdue" value="<?php echo $servicedue; ?>"></td>
      <td align="right"><input type="button" value="Save" name="save" onClick="post()"  ></td>
      </tr>
  	</table>
</form>

<?php

	if($_REQUEST['savebutton'] == "Y") {
		$wshop = $_REQUEST['wshop'];
		$w = explode('~',$wshop);
		$workshop = addslashes($w[1]);
		$jobno = $_REQUEST['jobno'];
		$ddate = $_REQUEST['ddateh'];
		$hubo = $_REQUEST['hubo'];
		$speedo = $_REQUEST['speedo'];
		$check1 = $_REQUEST['check1'];
		$check2 = $_REQUEST['check2'];
		$check3 = $_REQUEST['check3'];
		$check4 = $_REQUEST['check4'];
		$check5 = $_REQUEST['check5'];
		$check6 = $_REQUEST['check6'];
		$check7 = $_REQUEST['check7'];
		$check8 = $_REQUEST['check8'];
		$grease1 = $_REQUEST['grease1'];
		$grease2 = $_REQUEST['grease2'];
		$grease3 = $_REQUEST['grease3'];
		$grease4 = $_REQUEST['grease4'];
		$grease5 = $_REQUEST['grease5'];
		$grease6 = $_REQUEST['grease6'];
		$grease7 = $_REQUEST['grease7'];
		$grease8 = $_REQUEST['grease8'];
		$chassis1 = $_REQUEST['chassis1'];
		$chassis2 = $_REQUEST['chassis2'];
		$chassis3 = $_REQUEST['chassis3'];
		$chassis4 = $_REQUEST['chassis4'];
		$chassis5 = $_REQUEST['chassis5'];
		$visual1 = $_REQUEST['visual1'];
		$visual2 = $_REQUEST['visual2'];
		$visual3 = $_REQUEST['visual3'];
		$visual4 = $_REQUEST['visual4'];
		$visual5 = $_REQUEST['visual5'];
		$exhaust1 = $_REQUEST['exhaust1'];
		$exhaust2 = $_REQUEST['exhaust2'];
		$exhaust3 = $_REQUEST['exhaust3'];
		$drive1 = $_REQUEST['drive1'];
		$drive2 = $_REQUEST['drive2'];
		$drive3 = $_REQUEST['drive3'];
		$steering1 = $_REQUEST['steering1'];
		$steering2 = $_REQUEST['steering2'];
		$steering3 = $_REQUEST['steering3'];
		$suspension1 = $_REQUEST['suspension1'];
		$suspension2 = $_REQUEST['suspension2'];
		$suspension3 = $_REQUEST['suspension3'];
		$suspension4 = $_REQUEST['suspension4'];
		$suspension5 = $_REQUEST['suspension5'];
		$suspension6 = $_REQUEST['suspension6'];
		$suspension7 = $_REQUEST['suspension7'];
		$air1 = $_REQUEST['air1'];
		$air2 = $_REQUEST['air2'];
		$air3 = $_REQUEST['air3'];
		$fuel1 = $_REQUEST['fuel1'];
		$fuel2 = $_REQUEST['fuel2'];
		$body1 = $_REQUEST['body1'];
		$body2 = $_REQUEST['body2'];
		$body3 = $_REQUEST['body3'];
		$body4 = $_REQUEST['body4'];
		$turn1 = $_REQUEST['turn1'];
		$turn2 = $_REQUEST['turn2'];
		$turn3 = $_REQUEST['turn3'];
		$turn4 = $_REQUEST['turn4'];
		$electric1 = $_REQUEST['electric1'];
		$electric2 = $_REQUEST['electric2'];
		$electric3 = $_REQUEST['electric3'];
		$electric4 = $_REQUEST['electric4'];
		$electric5 = $_REQUEST['electric5'];
		$electric6 = $_REQUEST['electric6'];
		$electric7 = $_REQUEST['electric7'];
		$electric8 = $_REQUEST['electric8'];
		$gen1 = $_REQUEST['gen1'];
		$gen2 = $_REQUEST['gen2'];
		$gen3 = $_REQUEST['gen3'];
		$gen4 = $_REQUEST['gen4'];
		$gen5 = $_REQUEST['gen5'];
		$gen6 = $_REQUEST['gen6'];
		$gen7 = $_REQUEST['gen7'];
		$gen8 = $_REQUEST['gen8'];
		$replace1 = $_REQUEST['replace1'];
		$replace2 = $_REQUEST['replace2'];
		$replace3 = $_REQUEST['replace3'];
		$replace4 = $_REQUEST['replace4'];
		$replace5 = $_REQUEST['replace5'];
		$replace6 = $_REQUEST['replace6'];
		$replace7 = $_REQUEST['replace7'];
		$replace8 = $_REQUEST['replace8'];
		$road1 = $_REQUEST['road1'];
		$comments = $_REQUEST['comments'];
		$serviceman = addslashes($_REQUEST['serviceman']);
		$nextdue = $_REQUEST['nextdue'];
		
		$q = "update servicec set ";
		$q .= "check1 = '".$check1."',check2 = '".$check2."',check3 = '".$check3."',check4 = '".$check4."',check5 = '".$check5."',check6 = '".$check6."',check7 = '".$check7."',check8 = '".$check8."',";
		$q .= "grease1 = '".$grease1."',grease2 = '".$grease2."',grease3 = '".$grease3."',grease4 = '".$grease4."',grease5 = '".$grease5."',grease6 = '".$grease6."',grease7 = '".$grease7."',grease8 = '".$grease8."',";
		$q .= "chassis1 = '".$chassis1."',chassis2 = '".$chassis2."',chassis3 = '".$chassis3."',chassis4 = '".$chassis4."',chassis5 = '".$chassis5."',";
		$q .= "visual1 = '".$visual1."',visual2 = '".$visual2."',visual3 = '".$visual3."',visual4 = '".$visual4."',visual5 = '".$visual5."',";
		$q .= "exhaust1 = '".$exhaust1."',exhaust2 = '".$exhaust2."',exhaust3 = '".$exhaust3."',";
		$q .= "drive1 = '".$drive1."',drive2 = '".$drive2."',drive3 = '".$drive3."',";
		$q .= "steering1 = '".$steering1."',steering2 = '".$steering2."',steering3 = '".$steering3."',";
		$q .= "suspension1 = '".$suspension1."',suspension2 = '".$suspension2."',suspension3 = '".$suspension3."',suspension4 = '".$suspension4."',suspension5 = '".$suspension5."',suspension6 = '".$suspension6."',suspension7 = '".$suspension7."',";
		$q .= "air1 = '".$air1."',air2 = '".$air2."',air3 = '".$air3."',";
		$q .= "fuel1 = '".$fuel1."',fuel2 = '".$fuel2."',";
		$q .= "body1 = '".$body1."',body2 = '".$body2."',body3 = '".$body3."',body4 = '".$body4."',";
		$q .= "turn1 = '".$turn1."',turn2 = '".$turn2."',turn3 = '".$turn3."',body4 = '".$turn4."',";
		$q .= "electric1 = '".$electric1."',electric2 = '".$electric2."',electric3 = '".$electric3."',electric4 = '".$electric4."',electric5 = '".$electric5."',electric6 = '".$electric6."',electric7 = '".$electric7."',electric8 = '".$electric8."',";
		$q .= "gen1 = '".$gen1."',gen2 = '".$gen2."',gen3 = '".$gen3."',gen4 = '".$gen4."',gen5 = '".$gen5."',gen6 = '".$gen6."',gen7 = '".$gen7."',gen8 = '".$gen8."',";
		$q .= "replace1 = '".$replace1."',replace2 = '".$replace2."',replace3 = '".$replace3."',replace4 = '".$replace4."',replace5 = '".$replace5."',replace6 = '".$replace6."',replace7 = '".$replace7."',replace8 = '".$replace8."',";
		$q .= "road1 = '".$road1."',comments = '".$comments."',serviceman = '".$serviceman."',servicedue = '".$nextdue."'";
		$q .= " where service_id = ".$sid;
		
		$r = mysql_query($q) or die(mysql_error().$q);

		$q = "update vehicles set servicedue = ".$nextdue." where vehicleno = '".$vn."'";
		$r = mysql_query($q) or die(mysql_error().$q);
		$q = "update service set servicedue = ".$nextdue." where uid = '".$sid."'";
		$r = mysql_query($q) or die(mysql_error().$q);
	  ?>
	  <script>
	  window.open("","maintenance").jQuery("#servicelist").trigger("reloadGrid");
	  this.close();
	  </script>
	  <?php
		
			
	}

?>


</body>
</html>
