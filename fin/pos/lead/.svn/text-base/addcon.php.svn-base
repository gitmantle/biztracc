<?php
	// Include the import things such as authentic and database
	include_once "../../includes/accesscontrol.php";
	include_once "../../includes/db.php";
	
	$cid = $_REQUEST['cid'];
	
	$sql="SELECT `tradingname`,`companyname` FROM `companies` WHERE `id` = '$cid';";
	$rst=mysql_query($sql);
	$rec=mysql_fetch_row($rst);
		$tname = $rec[0];
		$cname = $rec[1];
		if($tname == "") { $show_cn = $cname; }	else { $show_cn = $tname; }
	mysql_free_result($rst);
	
?>

<table width="100%">
	<tr><td class="mhl">New Contact for <?php echo $show_cn; ?></td></tr>
</table>

<table width="100%" height="75%">
<tr height="100">
	<td class="boxcontentnb" width="15%"> <img src="../../images/man.jpg"> </td>
	<td class="boxcontentnb" width="85%"> 
		<table width="100%">
		<tr>
			<td class="boxheaderl" colspan="4" height="25"><big>Contact Information</big></td>
		</tr>
		<tr>
			<td class="altheaderr" width="25%">Title <font color="Red">*</font>&nbsp;</td>
			<td class="boxcontentnbl" width="25%">
				<select id="con_title">
					<option></option>
					<option>Mr</option>
					<option>Mrs</option>
					<option>Ms</option>
					<option>Dr</option>
				</select>
			</td>
			<td class="altheaderr" width="25%">Position <font color="Red">*</font>&nbsp;</td>
			<td class="boxcontentnbl" width="25%">
				<select id="con_pos"><option></option>
				<?php
					$sqlpos = "SELECT `item` FROM `zsys_dropdown` WHERE `list` = 'position' AND `deleted` = 0 ORDER BY `item`;";
					$rstpos = mysql_query($sqlpos);
					while($recpos = mysql_fetch_row($rstpos)) {
						echo "<option>".$recpos[0]."</option>";
					}
					mysql_free_result($rstpos);
				?>
				</select>
			</td>
		</tr>
		<tr>
			<td class="altheaderr" width="25%">First Name <font color="Red">*</font> &nbsp;</td>
			<td class="boxcontentnbl" width="25%">
				<input type="text" id="con_fname" size="15">
			</td>
			<td class="altheaderr" width="25%">Last Name <font color="Red">*</font> &nbsp;</td>
			<td class="boxcontentnbl" width="25%">
				<input type="text" id="con_lname" size="15">
			</td>
		</tr>
		<tr>
			<td class="altheaderr" width="25%">DID &nbsp;</td>
			<td class="boxcontentnbl" width="25%">
				<input type="text" id="con_phone" size="15">
			</td>
			<td class="altheaderr" width="25%">Mobile &nbsp;</td>
			<td class="boxcontentnbl" width="25%">
				<input type="text" id="con_mobile" size="15">
			</td>
		</tr>
		</table>
	</td>
</tr>
<tr>
	<td class="boxcontentnb">&nbsp;</td>
	<td class="boxcontentnb" valign="top">
		<table width="100%">
		<tr>
			<td class="boxheaderl" colspan="4" height="25"><big>Additional Information</big></td>
		</tr>
		<tr>
			<td class="altheaderr" width="25%">Fax &nbsp;</td>
			<td class="boxcontentnbl" width="25%">
				<input type="text" id="con_fax" size="15">
			</td>
			<td class="altheaderr" width="25%">Email &nbsp;</td>
			<td class="boxcontentnbl" width="25%">
				<input type="text" id="con_email" size="35">
			</td>
		</tr>
		<tr>
			<td class="boxcontentnbr" width="25%" colspan="4">
				<input type="button" value="Save New Contact" onclick="saveNCon(<?php echo $cid; ?>,$('con_title').value,$('con_pos').value,$('con_fname').value,$('con_lname').value,$('con_phone').value,$('con_mobile').value,$('con_fax').value,$('con_email').value);">
			</td>
		</tr>
		</table>
	</td>
</tr>
</table>