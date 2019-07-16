<?php
	include_once "../../includes/db.php";
	
	$search = $_REQUEST['search'];
	$type = $_REQUEST['type'];

	if(isset($_REQUEST['con']) && $type == 'c') {
		$sql="SELECT `id`,`fname`,`lname`,`position`,`phone`,`mobile`,`email` FROM `contacts` WHERE (`fname` LIKE '$search%' OR `lname` LIKE '$search%')";
		if(isset($_REQUEST['cid'])) {
			$cid = $_REQUEST['cid'];
			if($cid != "" && $cid != 0)	
				$sql.=" AND `pid` = '".$_REQUEST['cid']."'";
		}
		$sql.=" AND `deleted` != 1 LIMIT 5;";
		$select_id = "cons_results";
		$add_id = "add2";
	} else {
		$sql="SELECT `id`,`companyname`,`tradingname`,`addline1`,`addline2`,`city`,`state` FROM `companies` WHERE companies.id > 0 AND (`tradingname` LIKE '%$search%' OR `companyname` LIKE '%$search%') AND `cori` = '$type' LIMIT 5;";
		$select_id = "cs_results";
		$add_id = "add";
	}
	$rst=mysql_query($sql);

	if($type == 'i') { $div = ' - Aka -'; } else { $div = ' - T/a -'; }
	
	echo "<table width=\"100%\" border=\"0\" cellpadding=\"0\">";
	echo "<tr><td width=\"50%\" valign=\"top\" class=\"boxcontentnbl\">";
	if(mysql_num_rows($rst) > 0) {
		echo "<select id=\"$select_id\" size=\"5\">";
		
		while($rec=mysql_fetch_row($rst)) {
			if(isset($_REQUEST['con']) && $type == 'c') { //onclick=\"if(document.getElementById('$select_id').value == this.value) document.getElementById('$select_id').selectedIndex = '';\" 
				echo "<option onmouseover=\"this.style.backgroundColor='blue'; this.style.color='white'; $('add2').innerHTML = '".$rec[3]."<br />".$rec[4]."<br />".$rec[5]."<br />".$rec[6]."';\" onmouseout=\"$('add2').innerHTML = ''; this.style.backgroundColor='white'; this.style.color='black';\" value=\"".$rec[0]."\">".$rec[1]." ".$rec[2]."</option>";
			} else {
				echo "<option onmouseover=\"this.style.backgroundColor='blue'; this.style.color='white'; $('add').innerHTML = '".$rec[3]."<br>".$rec[4]."<br>".$rec[5]." ".$rec[6]."';\" onmouseout=\"$('add').innerHTML = ''; this.style.backgroundColor='white'; this.style.color='black';\" value=\"".$rec[0]."\">".$rec[1]." ".$div." ".$rec[2]."</option>";
			}
		}
		echo "</select>";
	} else {
		echo "No exsisting record(s) found - Details will be saved as new";
	}	
	echo "</td><td>";
		echo "<span id=\"$add_id\" style=\"font: 10pt arial;\"></span>";
	echo "</td></tr>";
	echo "</table>";

?>