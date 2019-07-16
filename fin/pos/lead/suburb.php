<?php
	include_once "../../includes/db.php";
	
	$suburb = $_REQUEST['suburb'];
	$pcode = $_REQUEST['pcode'];
	$state = $_REQUEST['state'];
	$sql1="SELECT `suburb`,`state`,`pcode` FROM `z_pcodes` WHERE `suburb` like '$suburb%' AND `pcode` like '$pcode%' AND `state` like '$state%' ORDER BY `suburb`";
	$rst1=mysql_query($sql1);
	if(mysql_num_rows($rst1) > 0) {
		echo "<select id=\"subsearch\" size=\"8\">";
		while($rec1=mysql_fetch_row($rst1)) {
			echo "<option ondblclick=\"selectSub(this.value);\">".$rec1[0].",".$rec1[1].",".$rec1[2]."</option>";
		}
		echo "</select>";
	} else
		echo "No search results found";
?>