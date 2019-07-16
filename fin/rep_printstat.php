<?php
session_start();
$usersession = $_SESSION['usersession'];

$_SESSION['watermark'] = 'N';

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];

$findb = $_SESSION['s_findb'];

date_default_timezone_set($_SESSION['s_timezone']);

$edate = date("d/m/Y");
$edateh = date("Y-m-d");

$db->query("select lstatdt,pstatdt,lastmedate from ".$findb.".globals");
$row = $db->single();
extract($row);
$curdat = $lstatdt;
$monthend = $lastmedate;
$dt = explode('-',$curdat);
$y = $dt[0];
$m = $dt[1];
$d = $dt[2];
$stmtdate = $d.'/'.$m.'/'.$y;
$stmtdateh = $curdat;
$dt = explode('-',$monthend);
$y = $dt[0];
$m = $dt[1];
$d = $dt[2];
$medate = $d.'/'.$m.'/'.$y;

$db->closeDB();

$theme = $_SESSION['deftheme'];
require_once("../includes/backgrounds.php");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Debtors Statements</title>
<link rel="stylesheet" type="text/css" href="../includes/flatpickr_date/dist/flatpickr.min.css">
<script src="../includes/flatpickr_date/dist/flatpickr.js"></script>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">

<script type="text/javascript">

	window.name = "rep_printstats";

</script>

</head>

<body>
<form name="form1" method="post" >
<br>
  <table width="880" border="0" align="center" cellpadding="3">
  <tr>
  	<td colspan="2" align="center" bgcolor="<?php echo $bghead; ?>"><label style="color: <?php echo $thfont; ?>; font-size:14px;">Debtor Statement Parameters</label></td>
  </tr>
  <tr>
    <td colspan="2" class="boxlabelleft">The last time the Month End routine was run was on <?php echo $medate; ?>. Only continue if the aging of balances is up to date.</td>
    </tr>
  <tr>
    <td colspan="2" class="boxlabelleft">The last time the Statements were run was on <?php echo $stmtdate; ?>.</td>
    </tr>
  <tr>
  	<td colspan="2" align="center" bgcolor="<?php echo $bghead; ?>"><label style="color: <?php echo $thfont; ?>; font-size:14px;">This Statement run will be from 
    <input type="Text" id="sdate" name="sdate" maxlength="25" size="25" value="<?php echo $stmtdateh; ?>" class="flatpickr" data-alt-input="true" data-alt-format="F j, Y">
    to 
    <input type="Text" id="edate" name="edate" maxlength="25" size="25" value="<?php echo $edateh; ?>" class="flatpickr" data-alt-input="true" data-alt-format="F j, Y">    
	</td>
  </tr>
  <tr>
    <td class="boxlabelleft">From Debtor</td>
    <td ><input type="text" name="fromdr" id="fromdr" size="45" readonly >
      <img src="../images/Search.gif" width="16" height="16" alt="Lookup" id="drsearchfrom" onclick="transvisibledrfrom()"> </div></td>
  </tr>
  <tr>
   	<td class="boxlabelleft">To Debtor</td>
      <td ><input type="text" name="todr" id="todr" size="45" readonly >
          <img src="../images/Search.gif" width="16" height="16" alt="Lookup" id="drsearchto" onclick="transvisibledrto()"> </div></td>
  </tr>
  <tr>
  	<td class="boxlabelleft"><input type="radio" name="range" id="nonzero" value="nonzero" checked/>
  	  Print statements for debtors with non zero balances</td>
    <td class="boxlabelleft">&nbsp;</td>
  </tr>
  <tr>
    <td class="boxlabelleft"><input type="radio" name="range" id="trans" value="trans" />
      Print statements for debtors with non zero balances but with transactions during the period</td>
    <td >&nbsp;</td>
  </tr>
  <tr>
    <td class="boxlabelleft"><input type="radio" name="range" id="all" value="all" />
      Print statements for all debtors in the selected range</td>
    <td >&nbsp;</td>
  </tr>
  <tr>
    <td class="boxlabelleft">Include Credit Statements?</td>
    <td ><select name="creditstat" id="creditstat">
      <option value="No">No</option>
      <option value="Yes">Yes</option>
    </select></td>
  </tr>
  <tr>
  <tr>
    <td class="boxlabelleft">Comment to be printed on statements (max 200 characters)</td>
    <td><textarea name="comment" id="comment" cols="50" rows="4"></textarea></td>
  </tr>
  <tr>
    <td class="boxlabelleft">&nbsp;</td>
    <td >&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2"class="boxlabelleft">The cutoff date for statements is set in Housekeeping -&gt; Maintenance -&gt; Setup. Once all statements have been run for the current month, run the Month End Routine from the Housekeeping menu. This will set the next date and age all the Debtors balances.</td>
    </tr>
  <tr>
  	<td>&nbsp;</td>
   	<td align="right"><input type="button" value="Run" name="run"  onClick="statement()" ></td>
 	</tr>
  </table>
  
  <div id="drselectfrom" style="position:absolute;visibility:hidden;top:125px;left:500px;height:239px;width:451px;background-color:<?php echo $bgcolor; ?>;border-width:thick thick thick thick; border-color:<?php echo $bghead; ?>; border-style:solid;">
    <table>
      <tr>
        <td><input type="text" id="searchdrfrom" size="50" onkeypress="doSearchdrfrom()" /></td>
        <td align="right"><img src="../images/close.png" width="16" height="16" alt="Lookup" id="fromclose" onclick="sboxhidedrfrom()">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2"><?php include "selectdrfrom.php"; ?></td>
      </tr>
    </table>
  </div>
  
  <div id="drselectto" style="position:absolute;visibility:hidden;top:150px;left:500px;height:239px;width:451px;background-color:<?php echo $bgcolor; ?>;border-width:thick thick thick thick; border-color:<?php echo $bghead; ?>; border-style:solid;">
    <table>
      <tr>
        <td><input type="text" id="searchdrto" size="50" onkeypress="doSearchdrto()" /></td>
        <td align="right"><img src="../images/close.png" width="16" height="16" alt="Lookup" id="toclose" onclick="sboxhidedrto()">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2"><?php include "selectdrto.php"; ?></td>
      </tr>
    </table>
  </div>
  
 <script>
 	document.getElementById("edate").flatpickr({
		onChange: function(dateObj, dateStr, instance) {
		}
	});
 </script>
 <script>
 	document.getElementById("sdate").flatpickr({
		onChange: function(dateObj, dateStr, instance) {
		}
	});
 </script>
        
</form>
</body>
</html>