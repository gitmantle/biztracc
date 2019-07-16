<?php
	include_once "../../includes/db.php";
	
	$cid = $_REQUEST['cid'];
	$con = $_REQUEST['con'];
	
	$sql="SELECT `companyname`,`tradingname`,`cori` FROM `companies` WHERE `id` = '$cid';";
	$rst=mysql_query($sql);
	$rec=mysql_fetch_row($rst);
		$cname = $rec[0];
		$tname = $rec[1];
		$cori = $rec[2];
	mysql_free_result($rst);
	
	if($cori == "i"){
		$header = "Client Name / Also Known As";
	}else{
		$header = "Legal / Trading Name";
	}
	
	$sql="SELECT `fname`,`lname` FROM `contacts` WHERE `id` = '$con';";
	$rst=mysql_query($sql);
	$rec=mysql_fetch_row($rst);
		$name = $rec[0]." ".$rec[1];
	mysql_free_result($rst);
?>

<table width="100%">
<tr>
	<td class="altheader"><?php echo $header; ?></td>
	<td class="boxcontentnbl"><?php echo $cname." / ".$tname; ?></td>
	<td class="altheader">Contact Name</td>
	<td class="boxcontentnbl">
	<select id="econ" onchange="changeCon(this.value,<?php echo $cid ?>);">
	<option></option>
	<?php
	$sql="SELECT `id`,`fname`,`lname` FROM `contacts` WHERE `pid` = '$cid' AND `deleted` != '1';";
	$rst=mysql_query($sql);
	while($rec=mysql_fetch_row($rst)) {
		$gconid = $rec[0];
		$name1 = $rec[1];
		$name2 = $rec[2];
		$name = $name1." ".$name2;
				
		echo "<option value=\"$gconid\""; if($gconid == $con) { echo "selected"; } echo ">$name</option>";
	}
	echo "<option value=\"-1\">Add New Contact</option>";
	?>
	</select>
	</td>
</tr>
</table>

<input type="hidden" id="cid" name="cid" value="<?php echo $cid; ?>">
<input type="hidden" id="con" name="con" value="<?php echo $con; ?>">

