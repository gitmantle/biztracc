<?php
	include_once "../../includes/accesscontrol.php";
	include_once "../../includes/db.php";

	$dept = $_REQUEST['dept'];
	$prod = $_REQUEST['prod'];
	$cid = $_REQUEST['cid'];
	$con = $_REQUEST['con'];

	$sql1="SELECT `selfgen` FROM `zsys_dropdown` WHERE `id` = '$prod';";
	$rst1=mysql_query($sql1);
	$rec1=mysql_fetch_row($rst1);
		$selfgen = $rec1[0];
	mysql_free_result($rst1);

	$sql2="SELECT `createssale`,`createsjob` FROM `zsys_dropdown` WHERE `id` = '$prod';";
	$rst2=mysql_query($sql2);
	$rec2=mysql_fetch_row($rst2);
		$hassale = $rec2[0];
		$hasjob = $rec2[1];
	mysql_free_result($rst2);

	if($dept == "") {
		$sql22="SELECT `lead` FROM `lead_structure` WHERE `product` = '$prod';";
		$rst22=mysql_query($sql22);
		$rec22=mysql_fetch_row($rst22);
			$dept = $rec22[0];
		mysql_free_result($rst22);
	}
	
	$sqlszsu = "SELECT `name`, `value`, `type` FROM `zsys_udfs` LEFT JOIN `zsys_udfval` ON `id` = `pid` WHERE `cid` = '$cid' AND `provwhat` = 'brand';";
	$rstszsu = mysql_query($sqlszsu);
	$recszsu = mysql_fetch_row($rstszsu);
	$countszsu = mysql_num_rows($rstszsu);
	
	$sqlszsu2 = "SELECT `name`, `value`, `type` FROM `zsys_udfs` LEFT JOIN `zsys_udfval` ON `id` = `pid` WHERE `cid` = '$cid' AND `provwhat` = 'me';";
	$rstszsu2 = mysql_query($sqlszsu2);
	$recszsu2 = mysql_fetch_row($rstszsu2);
	$countszsu2 = mysql_num_rows($rstszsu2);
?>


<table width="100%">
	<tr>
		<td class="boxheaderl" colspan="4" height="25">&nbsp;&nbsp;<big>Lead Form</big></td>
	</tr>
<?php
	if( $countszsu + $countszsu2 > '0'){
		echo "<tr>";
			if($recszsu[0] > '0'){
				echo "<td class=\"boxheaderl\" height=\"25\">&nbsp;&nbsp;".$recszsu[3]." ".$recszsu[0]."</td>";
				if($recszsu[1] != ""){
					echo "<td class=\"boxheaderl\" height=\"25\">&nbsp;&nbsp;<b>".$recszsu[1]."</b></td>";
				}else{
					echo "<td class=\"boxheaderl\" height=\"25\">&nbsp;&nbsp;<b>Not Known</b></td>";
				}
			}
			if($recszsu2[0] > '0'){
				echo "<td class=\"boxheaderl\" height=\"25\">&nbsp;&nbsp;".$recszsu2[3]." ".$recszsu2[0]."</td>";
				if($recszsu2[1] != ""){
					echo "<td class=\"boxheaderl\" height=\"25\">&nbsp;&nbsp;<b>".$recszsu2[1]."</b></td>";
				}else{
					echo "<td class=\"boxheaderl\" height=\"25\">&nbsp;&nbsp;<b>Not Known</b></td>";
				}
			}
		echo "</tr>";
	}
?>	
</table>

<table width="99%">
<?php
	$sql = "SELECT * FROM `lead_structure` WHERE `product` = '$prod' AND `lead` = '$dept' AND (`show` = 'lead' OR `show` = 'both') ORDER BY `position` ASC";
	$rst = mysql_query($sql);
	$qcount = mysql_num_rows($rst);

	$sql3="SELECT `name`,`rate`,`call_out`,`gst`,`pretty_name` FROM `lab_rates` WHERE `dept_id` = '$dept';";
	$rst3=mysql_query($sql3);
	while($rec3=mysql_fetch_row($rst3)) {
		$$rec3[0] = "<option value=\"".$rec3[4]."\">".$rec3[4]." $".round($rec3[2]*$rec3[3],2)." + $".round($rec3[1]*$rec3[3],2)." additional p/h + parts</option>";
	}
	$misc_rates = "<option value=\"free\">Maintenance customer</option><option value=\"free\">Warranty</option><option value=\"free\">Quoted</option>";

	while($rec=mysql_fetch_assoc($rst)) {
		$tid = $rec['id'];
		$question = $rec['question'];
		$pos = $rec['position'];
		$type = $rec['type'];
		$values = $rec['values'];
		$svalues = explode(",",$values);
		$vallen = count($svalues);
		$i = 0;

		if($type == 'T') {
			echo "<tr><td class=\"altheaderr\" width=\"60%\">$question &nbsp;&nbsp;</td>
					<td class=\"\">";
			echo "<input type=\"text\" class=\"textb\" name=\"$pos\" size=\"40\"><input type=\"hidden\" name=\"{$pos}id\" value=\"$tid\"></td></tr>";
		} if($type == 'DT') {
			echo "<tr><td class=\"altheaderr\" width=\"60%\">$question &nbsp;&nbsp;</td>
					<td class=\"boxcontentnbl\" valign=\"middle\">";
			echo "<input type=\"text\" class=\"textb\" name=\"$pos\" id=\"date".$pos."\" size=\"10\"><img src=\"../../images/calendar.jpg\" onclick=\"displayCalendar(document.getElementById('date".$pos."'),'dd/mm/yyyy',this)\">
				<input type=\"hidden\" name=\"{$pos}id\" value=\"$tid\">";

				echo "</td></tr>";
		} else if($type == "LT") {
			echo "<tr><td class=\"boxheaderl\" colspan=2>&nbsp;&nbsp;<big>$question</big> &nbsp;&nbsp;</td></tr>
			      <tr><td class=\"altheaderl\" colspan=2>";
			echo "<input type=\"text\" name=\"$pos\" class=\"textb\" size=98><input type=\"hidden\" name=\"{$pos}id\" value=\"$tid\"></td></tr>";
			echo "</td></tr>";
		} else if($type == 'S') {
			echo "<tr><td class=\"altheaderr\" width=\"60%\">$question &nbsp;&nbsp;</td>
					<td class=\"boxcontentnbl\">";
				echo "<select name=\"$pos\" class=\"norm\">";
					while($i < $vallen) {
						echo "<option>".$svalues[$i]."</option>";
						$i++;
					}
				echo "</select><input type=\"hidden\" name=\"{$pos}id\" value=\"$tid\"></td></tr>";
		} else if($type == "VSB") {
		 	echo "<tr><td class=\"altheaderr\" width=\"60%\">$question &nbsp;&nbsp;</td>
					<td class=\"boxcontentnbl\">";
				echo "<select name=\"$pos\" class=\"norm\">";
						while($i < $vallen) {
							echo $$svalues[$i];
							$i++;
						}
				echo "</select><input type=\"hidden\" name=\"{$pos}id\" value=\"$tid\"></td></tr>";
		} else if($type == "RG") {
		 	echo "<tr><td class=\"altheaderr\" width=\"60%\">$question &nbsp;&nbsp;</td>
					<td class=\"boxcontentnbl\">";
				//echo "<select name=\"$pos\" class=\"norm\">";
						while($i < $vallen) {
							echo "<input type=\"radio\" name=\"$pos\" value=\"".$svalues[$i]."\">".$svalues[$i]."&nbsp;&nbsp;";
							$i++;
						}
				echo "</select><input type=\"hidden\" name=\"{$pos}id\" value=\"$tid\"></td></tr>";
		} else if($type == 'C') {
			echo "<tr><td class=\"boxcontentnbr\">&nbsp;</td>";
			echo "<td class=\"boxcontentnbl\">";
				echo "<input type=\"checkbox\" class=\"textb\" name=\"$pos\"><input type=\"hidden\" name=\"{$pos}id\" value=\"$tid\">";
			echo "$question </td></tr>";
		} else if($type == 'TA') {
			echo "<tr>
				<td class=\"altheaderl\">&nbsp;&nbsp;$question &nbsp;&nbsp;</td>
			</tr><tr>
				<td class=\"boxcontentnbl\" colspan=\"2\">";
			echo "<textarea name=\"$pos\" class=\"textblead\" cols=140></textarea><input type=\"hidden\" name=\"{$pos}id\" value=\"$tid\"></td></tr>";
			echo "</td></tr>";
		} else if($type == 'R') {
				echo "<tr><td class=\"boxcontentnbr\"><b>$question</b> &nbsp;&nbsp;</td><td class=\"boxcontentnb\">&nbsp;</td></tr>";
			while($i < $vallen) {
				echo "<tr><td class=\"boxcontentnb\">&nbsp;</td>";
				echo "<td class=\"boxcontentnbl\"> <input type=\"radio\" name=\"$pos\" value=\"".$svalues[$i]."\"> ".$svalues[$i]." <input type=\"hidden\" name=\"{$pos}id\" value=\"$tid\"></td></tr>";
				$i++;
			}
		} else if($type == 'L') {
			echo "<tr><td class=\"boxcontentnbr\"><b>$question</b> &nbsp;&nbsp;</td><td class=\"boxcontentnb\">&nbsp;</td></tr>";
		} else if($type == 'LF') {
			if($values == "1") { $bs = "<big>$question</big>"; } else if($values == 2) { $bs = "<big><big>$question</big></big>"; } else { $bs = " $question"; }
			echo "<tr><td class=\"boxcontentnb\" colspan=\"2\"><b>$bs</b></td></tr>";
		} else if($type == 'ST') {
			echo "<tr><td class=\"boxcontentnbr\">$question &nbsp;&nbsp;</td>
					<td class=\"boxcontentnbl\"><input class=\"textb\" type=\"text\" name=\"$pos\"><input type=\"hidden\" name=\"{$pos}id\" value=\"$tid\"></td></tr>";
		} else if($type == 'H') {
			echo "<tr><td class=\"boxcontentnb\" colspan=\"2\"><hr></td></tr>";
		} else if($type == 'RGALIST') {
			//print_r($_GET);
			$sql = "SELECT `part_number`,`Category`,`sn`,`id`, pdi.`status`,pdi.`date`";
			$sql .= " FROM `product_data_inventory` pdi LEFT JOIN `product_data_technical` ON `part_number` = `PartNumber` WHERE `owner` = '".$_GET["cid"]."' AND `owner_type` = 'C' AND (pdi.status = 'Sold' OR pdi.status = 'NSI') ORDER BY `Category`,`part_number`;";
			//echo $sql;
			echo "<tr><td class=\"altheader\">$question &nbsp;&nbsp;</td></tr>";
			$result = mysql_query($sql);
			while($row = mysql_fetch_row($result))
			{
				$checked = "";
				if($row[3] == $_GET['itemid'])
					$checked = "true";
				echo "<tr><td class=\"boxcontentnbl\"><input type=\"radio\" name=\"".$pos."\" value=\"".$row[3]."\" checked=\"$checked\"><input type=\"hidden\" name=\"{$pos}id\" value=\"$tid\">".$row[0]." - ".$row[2]."</td></tr>";
			} 
		}
	}

	$userdept = $udept;

	echo "<tr><td class=\"boxcontentnb\" colspan=\"2\">";
	if($selfgen == "1" && $userdept == $dept) { echo "<input type=\"checkbox\" name=\"mylead\"> Self Assign &nbsp;"; } echo " &nbsp;<input type=\"submit\" value=\"Save Lead Information & Create Activity\" class=\"button\" id=\"slb\"></td></tr>";
	echo "</table></form>";
?>
<script>
if(document.getElementById('ritemid').value != "")
	document.getElementById(document.getElementById('ritemid').value).checked = true;
	//alert(document.getElementById('ritemid').value);
</script>
