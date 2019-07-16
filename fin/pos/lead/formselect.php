<?php
	include_once "../../includes/db.php";
	$source = $_REQUEST['source'];
	$dept = $_REQUEST['dept'];
	$prod = $_REQUEST['prod'];
	$cid = $_REQUEST['cid'];
	//print_r($_REQUEST);
?>

<table width="100%">
<tr><td height="25" colspan="6" class="boxheaderl"> <big><big> Select Lead Form </big></big> </td></tr>
<tr>
	<td class="altheader"> Source </td>
	<td class="boxcontentnbl">
		<?php
			$sql="SELECT `item`,`id` FROM `zsys_dropdown` WHERE `list` = 'leadsource' AND `deleted` != '1' ORDER BY `item` ASC ;";
			$rst=mysql_query($sql);
			echo "<select id=\"source\" name=\"lsource\">"; //onchange=\"loadLeadFormSelect(this.options[this.selectedIndex].value, $('dept').value, $('prod').value)\">";
			echo "<option></option>";
			while($rec=mysql_fetch_row($rst)) {
				echo "<option value=\"".$rec[1]."\" "; if($source == $rec[1]) echo "selected"; echo ">".$rec[0]."</option>";
			}
		?>
	</td>
	<td class="altheader"> Department </td>
	<td class="boxcontentnbl">
		<?php
			$sql4="SELECT `item`,`id` FROM `zsys_dropdown` WHERE `list` = 'departments' AND `lead` = '1' AND `deleted` != '1' ORDER BY `item` ASC;";
			$rst4=mysql_query($sql4);
	
			echo "<select id=\"dept\" name=\"dept\" onchange=\"loadLeadFormSelect($('source').value, this.options[this.selectedIndex].value, $('prod').value)\">";
			echo "<option></option>";
			while($rec4=mysql_fetch_row($rst4)) {				
				echo "<option value=\"".$rec4[1]."\""; if($dept == $rec4[1]) echo "selected"; echo ">".$rec4[0]."</option>";
			}
		?>
	</td>
	<td class="altheader"> Product </td>
	<td class="boxcontentnbl">
	<?php
		$sql="SELECT `item`,`id` FROM `zsys_dropdown` WHERE `list` = 'leadproducts' AND `pid` = '$dept';";
		$rst=mysql_query($sql);
		echo "<select id=\"prod\" name=\"prod\" onchange=\"loadLeadForm($('dept').value, this.options[this.selectedIndex].value, '')\">";
		echo "<option></option>";
		while($rec=mysql_fetch_row($rst)) {
			$name = $rec[0];
			$prod_id = $rec[1];
			echo "<option value=\"$prod_id\" "; if($prod == $rec[1]) echo "selected"; echo ">$name</option>";
		}
		echo "</select>";
	?>
	</td>
</tr>
</table>
