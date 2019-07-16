<?php
session_start();

date_default_timezone_set($_SESSION['s_timezone']);

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

$table = 'ztmp'.$user_id.'_trans';

$_SESSION['s_crac'] = 0;
$_SESSION['s_crsb'] = 0;

$findb = $_SESSION['s_findb'];

$db_trd->query("drop table if exists ".$findb.".".$table);
$db_trd->execute();

$db_trd->query("create table ".$findb.".".$table." ( uid int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, acc2dr int(11) default 0,subdr int(11) default 0,brdr char(4) default '',drindex int(10) default 0,acc2cr int(11) default 0,subcr int(11) default 0,brcr char(4)default '',crindex int(10) default 0,ddate date default '0000-00-00',descript1 varchar(60),reference char(9) default '',refindex int(10) default 0,amount double(16,2) default 0,depdr int(11),depbrdr char(4),depcr int(11),depbrcr char(4),nallocate int(11),tax double(16,2),taxtype char(3),taxpcent double(5,2),applytax char(1),total double(16,2) default 0, done int(11) default 0,type char(1),grn char(10),inv char(10),currency char(3), rate double(7,3),a2d varchar(45),a2c varchar(45),taxindex int(10),drgst char(1) default 'N', crgst char(1) default 'N')  engine myisam");
$db_trd->execute();

$coyid = $_SESSION['s_coyid'];
$ddate = date("d/m/Y");
$ddateh = date("Y-m-d");
$country = $_SESSION['country'];

$_SESSION['s_drac'] = 0;
$_SESSION['s_drsb'] = 0;

$db_trd->closeDB();

require_once("../includes/backgrounds.php");
$theme = $_SESSION['deftheme'];


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Unallocate Receipts</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<script>

	window.name = "ual_pay";
</script>
</head>
<body>
<form name="inv" id="inv" method="post" >
  <input type="hidden" name="savebutton" id="savebutton" value="N">
  <input type="hidden" name="trading" id="trading" value="pay">
  <input type="hidden" name="typ" id="typ" value="pay">
  <input type="hidden" name="lid" id="lid" value="0">
  <input type="hidden" name="lref" id="lref" value="">
  <input type="hidden" name="lalloc" id="lalloc" value="0">

  <table  width="960" border="0" cellpadding="3" cellspacing="1" align="center" bgcolor="<?php echo $bgcolor; ?>">
    <tr bgcolor="<?php echo $bghead; ?>">
      <td colspan="10"><label style="color: <?php echo $thfont; ?>"><strong>Unallocate allocated receipts</strong></label></td>
    </tr>
    <tr>
      <td class="boxlabel" >Debtor</td>
      <td colspan="2" ><input type="text" name="TRaccount" id="TRaccount" size="45" readonly >
          <img src="../images/Search.gif" width="16" height="16" alt="Lookup" id="drsearch" onclick="transvisibledr()"> </td>
      <td width="538" colspan="6" class="boxlabelleft" ><input type="button" name="ballocate" id="ballocate" value="List allocations against Invoices" onClick="revalloc('rec')"></td>
    </tr>
  </table>
  
  <div id="allocation" style="visibility:visible;" >
  <table  width="960" border="0" cellpadding="3" cellspacing="1" align="center" bgcolor="<?php echo $bgcolor; ?>">
    <tr>
      <td><?php include "getallocinvs.php"; ?></td>
    </tr>
  
  </table>
  </div>

  
  <div id="drselect" style="position:absolute;visibility:hidden;top:98px;left:342px;height:239px;width:451px;background-color:<?php echo $bgcolor; ?>;border-width:thick thick thick thick; border-color:<?php echo $bghead; ?>; border-style:solid;">
    <table>
      <tr>
        <td><input type="text" id="searchdr" size="50" onkeypress="doSearchdr()" /></td>
        <td align="right"><img src="../images/close.png" width="16" height="16" alt="Lookup" id="crclose" onclick="sboxhidedr()">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2"><?php include "selectrecunallocate.php"; ?></td>
      </tr>
    </table>
  </div>


  <div id="unpaypart" style="position:absolute;visibility:hidden;top:250px;left:500px;height:75px;width:250px;background-color:<?php echo $bgcolor; ?>;border-width:7px; border-color:<?php echo $bghead; ?>; border-style:outset;">
	<table width="250">
    <tr bgcolor="<?php echo $bghead; ?>">
    <td colspan="3" align="center"><label style="color: <?php echo $thfont; ?>"><strong>Unallocate payment</strong></label></td>
    <tr>
      <td>Amount<input type="text" name="partamount" id="partamount" value="0" onFocus="this.select()"></td>
      <td align="right"><input type="button" name="pamount" id="pamount" value="Save" onClick="ulpayment('rec')"></td>
    </tr>
    </table>
  
	</div>  


</form>

</body>
</html>
