<?php
	include_once "../../includes/accesscontrol.php";
	include_once "../../includes/db2.php";
?>


<table width="100%">
	<tr><td class="boxheaderl" height="25" colspan="4" style="font: 14 arial;">
	 <select id="nr_ctype" onchange="changeRelation(this.value);">
	 	<option value="c">Company</option>
		<option value="g">Gov. Department</option>
		<option value="i">Individual</option>
	 </select> Details
	</td></tr>
</table>

<div id="company">

	<table width="100%">
	<tr>
		<td width="25%" class="altheader" id="nr_legalname_label"> Legal Name : </td>
		<td width="25%"> <input type="text" id="nr_legalname" onkeyup="doCompanySearch(this.value,$('nr_ctype').value);"> </td>
		<td width="50%" rowspan="3">
			<div id="csres"></div>
		</td>
	</tr>
	<tr>
		<td width="25%" class="altheader" id="nr_tradename_label"> Trade Name : </td>
		<td width="25%"> <input type="text" id="nr_tradename" onkeyup="doCompanySearch(this.value,$('nr_ctype').value);"> </td>
	<!-- <td width="50%"></td> -->
	</tr>
	<tr>
		<td class="boxcontentnb" colspan="2">
			&nbsp;
		</td>
	</tr>
	</table>

</div>

<div id="companydetails">
	<table width="100%">
	<tr>
		<td class="boxheaderl" colspan="4" height="25">&nbsp;&nbsp;<big>Company Info</big></td>
	</tr>
	<tr>
		<td class="altheader" width="25%">Main Phone:</td>
			<td class="boxcontentnbl" width="25%"><input type="text" style="border-width:1px;border-color:gray;border-style:solid; text-size:9pt;" id="nr_comp_phone" size="20"></td>
		<td class="altheader" width="25%">Fax:</td>
			<td class="boxcontentnbl" width="25%"><input type="text" style="border-width:1px;border-color:gray;border-style:solid; text-size:9pt;" id="nr_comp_fax" size="20"></td>
	</tr>
	</table>
</div>

<div id="contact">
	<table width="100%">
	<tr>
		<td class="boxheaderl" colspan="4" height="25">&nbsp;&nbsp;<big>Contact's Details</big></td>
	</tr>
	<tr>
		<td class="altheader" width="25%">Contact's First Name</td>
		<td class="boxcontentnbl" width="25%"><input onkeyup="var cs = ($('cs_results')) ? $('cs_results').value : 0; doCompanySearch2(this.value,$('nr_ctype').value,cs);" type="text" style="border-width:1px;border-color:gray;border-style:solid; text-size:9pt;" name="fname" id="nr_con_fname" size="40"></td>
		<td colspan="2" rowspan="3">
			<div id="csearch2">

			</div>
		</td>
	</tr>
	<tr>
		<td class="altheader" width="25%">Contact's Last Name</td>
		<td class="boxcontentnbl" width="25%"><input onkeyup="var cs = ($('cs_results')) ? $('cs_results').value : 0; doCompanySearch2(this.value,$('nr_ctype').value,cs);" type="text" style="border-width:1px;border-color:gray;border-style:solid; text-size:9pt;" name="lname" id="nr_con_lname" size="40" onblur="transferName();"></td>

	</tr>
	<tr>
		<td class="altheader" width="25%">Phone:</td>
		<td class="boxcontentnbl" width="25%"><input type="text" style="border-width:1px;border-color:gray;border-style:solid; text-size:9pt;" id="nr_con_phone" size="20"></td>
	</tr>
	<tr>
		<td class="altheader" width="25%">Contact's Mobile</td>
		<td class="boxcontentnbl" width="25%"><input type="text" style="border-width:1px;border-color:gray;border-style:solid; text-size:9pt;" id="nr_con_mobile" size="40"></td>
		<td class="altheader" width="25%">Email Address:</td>
		<td class="boxcontentnbl" width="25%"><input type="text" style="border-width:1px;border-color:gray;border-style:solid; text-size:9pt;" id="nr_con_email" size="40"></td>
	</tr>
	</table>
</div>


<div id="address">
<table width="100%">
	<tr>
		<td class="boxheaderl" colspan="4" height="25">&nbsp;&nbsp;<big><?php echo $coritxt; ?> Main Address</big></td>
	</tr>
	<tr>
		<td class="altheader" width="25%">Suite / Level:</td>
		<td class="boxcontentnbl"><input type="text" style="border-width:1px;border-color:gray;border-style:solid; text-size:9pt;" id="nr_add_line1" size="40"></td>
		<td class="boxcontentnbl" colspan="2" rowspan="5" width="50%"><div id="subdiv"></div></td>
	</tr>
	<tr>
		<td class="altheader" width="25%">No. / Street:</td>
			<td class="boxcontentnbl"><input type="text" style="border-width:1px;border-color:gray;border-style:solid; text-size:9pt;" id="nr_add_line2" size="40"></td>
		<!-- <td class="boxcontentnbl">&nbsp;</td><td class="boxcontentnbl">&nbsp;</td> -->
	</tr>
	<tr>
		<td class="altheader" width="25%">City:</td>
			<td class="boxcontentnbl"><input type="text" style="border-width:1px;border-color:gray;border-style:solid; text-size:9pt;" id="nr_add_city" onfocus="showSub();" onkeyup="searchsuburb(this.value,'','');" size="40"></td>
		<!-- <td class="boxcontentnbl">&nbsp;</td><td class="boxcontentnbl">&nbsp;</td> -->
	</tr>
	<tr>
		<td class="altheader" width="25%">State:</td>
			<td class="boxcontentnbl"><input type="text" style="border-width:1px;border-color:gray;border-style:solid; text-size:9pt;" id="nr_add_state" size="40"></td>
		<!-- <td class="boxcontentnbl">&nbsp;</td><td class="boxcontentnbl">&nbsp;</td> -->
	</tr>
	<tr>
		<td class="altheader" width="25%">Postal Code:</td>
			<td class="boxcontentnbl"><input type="text" style="border-width:1px;border-color:gray;border-style:solid; text-size:9pt;" id="nr_add_pcode" size="40"></td>
		<!-- <td class="boxcontentnbl">&nbsp;</td><td class="boxcontentnbl">&nbsp;</td> -->
	</tr>
</table>
</div>
<input type="hidden" id="nr_status" value="new" style="border-width:1px;border-color:gray;border-style:solid; text-size:9pt;">


<table width="100%">
<tr><td class="boxcontentnbr"> <input type="button" class="button" value="Save Record and go to POS" onclick="saveRecord()"> </td></tr>
</table>
