<?php
session_start();
date_default_timezone_set($_SESSION['s_timezone']);

$ddate = date("d/m/Y");
$ddateh = date("Y-m-d");
$usersession = $_SESSION['usersession'];

$_SESSION['s_select'] = '~~';

$ddate = date("d/m/Y");
$ddateh = date("Y-m-d");
$usersession = $_SESSION['usersession'];

include_once("../includes/DBClass.php");
$db_trd = new DBClass();

$db_trd->query("select * from sessions where session = :vusersession");
$db_trd->bind(':vusersession', $usersession);
$row = $db_trd->single();
$subid = $row['subid'];
$user_id = $row['user_id'];

$table = 'ztmp'.$user_id.'_journal';

$findb = $_SESSION['s_findb'];

$db_trd->query("drop table if exists ".$findb.".".$table);
$db_trd->execute();

$db_trd->query("create table ".$findb.".".$table." ( uid int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, account varchar(45),note varchar(45),debit double(16,2) default 0,credit double(16,2) default 0,accno int(11) default 0,subac int(11) default 0,brac char(4),ddate date default '0000-00-00',reference char(9),acindex int(10),currency char(3) default '',rate decimal(7,3) default 0,drgst char(1) default 'N', crgst char(1) default 'N', your_ref varchar(30) default '')  engine myisam");
$db_trd->execute();
$db_trd->closeDB();

require_once("../includes/backgrounds.php");
$theme = $_SESSION['deftheme'];


?>


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

<title>Journal Transactions</title>

<link rel="stylesheet" type="text/css" href="../includes/flatpickr_date/dist/flatpickr.min.css">
<script src="../includes/flatpickr_date/dist/flatpickr.js"></script>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<script src="includes/ajaxgetref.js"></script>
<script type="text/javascript" src="includes/ajaxCheckTransDate.js"></script>

<script>

	window.name = "tr_journal";

</script>


<style type="text/css">
<!--
.style3 {color: #FFFFFF}
.style5 {font-size: x-small}
.style31 {	font-size: x-small
}
-->
</style>
</head>

<body>

<div id="tabs">
	<form name="trans" id="trans" method="post">
	<input type="hidden" name="Submit" id="Submit" value="true">	
    <input type="hidden" name="savebutton" id="savebutton" value="N">
	
	<table width="920" border="0" cellpadding="3" cellspacing="1" align="center">
      <tr bgcolor="<?php echo $bghead; ?>">
        <td colspan="14"><label style="color: <?php echo $thfont; ?>"><strong>Journal Transaction </strong></label></td>
      </tr>
      <tr>
        <td class="boxlabel">Date</td>
        <td ><input type="Text" id="newdate" name="newdate" maxlength="25" size="25" value="<?php echo $ddateh; ?>" class="flatpickr" data-alt-input="true" data-alt-format="F j, Y"></td>
        <td>&nbsp;</td>
        <td class="boxlabel"><div align="right">Reference</div></td>
        <td colspan="2"><input name="ref" id="newref" value="JNL" type="text" size="4" readonly>
			<input type="text" name="refno" id="newrefno" value=" " size="10" readonly onFocus="nextref()"></td>		
      </tr>
	<tr bgcolor="<?php echo $bghead; ?>">
	  <td colspan="5"><label style="color: <?php echo $thfont; ?>"><strong>New Transaction Line Details </strong></label></td>
	  <td colspan="4">
          <div align="right"></div></td>
	</tr>	  
	<tr>
	  <td width="100" id="itm" class="boxlabel">Account</td>
	  <td width="188">
	    <div align="left">
	      <select name="bgacc2dr" id="newbgacc2dr" onchange="ajaxGetACList(this.value,'dr'); return false;">
            <option>Select Ledger</option>
            <option value="GL">General Ledger</option>
            <option value="DR">Debtors Ledger</option>
            <option value="CR">Creditors Ledger</option>
            <option value="AS">Fixed Assets</option>
          </select>
		</div>
    </td>
	  <td>          <input type="text" name="DRaccount" id="DRaccount" size="40" readonly >
          <img src="../images/Search.gif" width="16" height="16" alt="Lookup" id="drsearch" onclick="sboxvisibledr()"> </td>
	  <td class="boxlabel" bgcolor="#cccccc"><label style="color:#000000;">Debit</label></td>
	  <td><input name="drvalue" id="newdrvalue" type="text" size="16" value="0.00" style="text-align: right" onFocus="this.select()" onBlur="chkdrcr('dr')"></td>
	  <td class="boxlabel" bgcolor="#ff9933"><label style="color:#000000;">Credit</label></td>
	  <td><input name="crvalue" id="newcrvalue" type="text" size="16" value="0.00" style="text-align: right" onFocus="this.select()" onBlur="chkdrcr('cr')"></td>
	</tr>
	<tr>
	  <td id="itm" class="boxlabel">Note</td>
	  <td colspan="2"><input name="description" id="newdescription" type="text" size="60" maxlength="60"></td>
	  <td>&nbsp;</td>
      <td id="ndrgst"><span class="style31">Include for <?php echo $_SESSION['s_tradtax']; ?> </span>
        <select name="dgst" id="newdrgst">
          <option value="N">Yes</option>
          <option value="Y">No</option>
        </select></td>
	  <td>&nbsp;</td>
	  <td>&nbsp;</td>
    </tr>
	<tr>
	  <td id="itm2" class="boxlabel">&nbsp;</td>
	  <td colspan="2">&nbsp;</td>
	  <td>&nbsp;</td>
	  <td>&nbsp;</td>
	  <td>&nbsp;</td>
	  <td align="right"><input type="button" name="add" id="add" value="Add" onClick="addjournal()"></td>
	  </tr>
      <tr>
      	<td colspan="7"><?php include "getjournal.php"; ?></td>
      </tr>
      
	<tr bgcolor="<?php echo $bghead; ?>">
	 <td colspan="12"><div align="right"><input name="Submit" type="button" value="Post" onClick="postJournal()"></div></td>
     </tr>
     </table>

  <div id="glselect" style="position:absolute;visibility:hidden;top:104px;left:455px;height:239px;width:451px;background-color:<?php echo $bgcolor; ?>;border-width:thick thick thick thick; border-color:<?php echo $bghead; ?>; border-style:solid;">
    <table>
      <tr>
        <td><input type="text" id="searchgl" size="50" onkeypress="doSearchgl()" /></td>
        <td align="right"><img src="../images/close.png" width="16" height="16" alt="Lookup" id="glclose" onclick="sboxhidegl()">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2"><?php include "selectgl.php"; ?></td>
      </tr>
    </table>
  </div>
  
  <div id="drselect" style="position:absolute;visibility:hidden;top:104px;left:455px;height:239px;width:451px;background-color:<?php echo $bgcolor; ?>;border-width:thick thick thick thick; border-color:<?php echo $bghead; ?>; border-style:solid;">
    <table>
      <tr>
        <td><input type="text" id="searchdr" size="50" onkeypress="doSearchdr()" /></td>
        <td align="right"><img src="../images/close.png" width="16" height="16" alt="Lookup" id="drclose" onclick="sboxhidedr()">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2"><?php include "selectdr.php"; ?></td>
      </tr>
    </table>
  </div>
  
  <div id="crselect" style="position:absolute;visibility:hidden;top:104px;left:455px;height:239px;width:451px;background-color:<?php echo $bgcolor; ?>;border-width:thick thick thick thick; border-color:<?php echo $bghead; ?>; border-style:solid;">
    <table>
      <tr>
        <td><input type="text" id="searchcr" size="50" onkeypress="doSearchcr()" /></td>
        <td align="right"><img src="../images/close.png" width="16" height="16" alt="Lookup" id="drclose" onclick="sboxhidecr()">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2"><?php include "selectcr.php"; ?></td>
      </tr>
    </table>
  </div>
  
  <div id="asselect" style="position:absolute;visibility:hidden;top:104px;left:455px;height:239px;width:451px;background-color:<?php echo $bgcolor; ?>;border-width:thick thick thick thick; border-color:<?php echo $bghead; ?>; border-style:solid;">
    <table>
      <tr>
        <td><input type="text" id="searchas" size="50" onkeypress="doSearchas()" /></td>
        <td align="right"><img src="../images/close.png" width="16" height="16" alt="Lookup" id="drclose" onclick="sboxhideas()">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2"><?php include "selectas.php"; ?></td>
      </tr>
    </table>
  </div>
 
  <script>
	document.getElementById('drsearch').style.visibility = 'hidden';
	document.getElementById('DRaccount').style.visibility = 'hidden';
	document.getElementById('ndrgst').style.visibility = 'hidden';
	//document.getElementById('ncrgst').style.visibility = 'hidden';
  </script>

 <script>
 	document.getElementById("newdate").flatpickr({
		onChange: function(dateObj, dateStr, instance) {
			ajaxCheckTransDate();	
		}
	});
 </script>
  	
</form>
</div>

</body>
</html>