<?php
session_start();
$findb = $_SESSION['s_findb'];
$asid = $_REQUEST['asid'];

$dt = date('Y-m-d');

//ini_set('display_errors', true);

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from ".$findb.".fixassets where uid = ".$asid);
$row = $db->single();
extract($row);
$br = $branch;


$db->closeDB();


require_once("../includes/backgrounds.php");
$theme = $_SESSION['deftheme'];

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Reverse Depreciation</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<link type="text/css" href="../includes/jquery/themes/<?php echo $theme; ?>/jquery.ui.all.css" rel="stylesheet" />

<script>

function post() {

	//add validation here if required.
	var andep = document.getElementById('andep').value;
	var revamt = document.getElementById('revamt').value;
	var ok = "Y";
	if (revamt > andep) {
		alert("Reversal amount may not be greater than the last annual depreciation.");
		ok = "N";
		return false;
	}
	
	if (ok == "Y") {
		document.getElementById('savebutton').value = "Y";
		document.getElementById('revdep').submit();
	}
}

</script>

<style type="text/css">
<!--
.style1 {font-size: large}
-->
</style>
</head>


<body>
<div id="swin">
<form name="revdep" id="revdep" method="post" >
 	<input type="hidden" name="savebutton" id="savebutton" value="N">
  <table width="480" border="0" align="center">
    <tr>
      <td colspan="2"><div align="center" class="style1"><u>Reverse Depreciation </u></div></td>
    </tr>
    <tr>
      <td class="boxlabel">Asset - <?php echo $asset; ?></td>
      <td >&nbsp;</td>
    </tr>
    <tr>
      <td class="boxlabel">Total Depreciation this year</td>
      <td><input type="text" name="andep" id="andep" value="<?php echo $anndep; ?>" readonly></td>
    </tr>
    <tr>
      <td class="boxlabel">Amount of Reversal</td>
      <td><input type="text" name="revamt" id="revamt" value="0"></td>
    </tr>
   	<tr>
	  <td>&nbsp;</td>
	  <td align="right"><input type="button" value="Post" name="save" onclick="post()" ></td>
	</tr>
  </table>
  
 <script>
	document.getElementById('revamt').focus();
 </script>
  
</form>
</div>

<?php
	if(isset($_REQUEST['savebutton']) && $_REQUEST['savebutton'] == "Y") {
		
		$revamt = $_REQUEST['revamt'];
		
		include_once("../includes/DBClass.php");
		$db = new DBClass();
		
		$db->query("update ".$findb.".glmast set obal = obal + ".$revamt." where accountno = 702 and branch = '".$br."' and sub = 0");
		$db->execute();
		
		$db->query("update ".$findb.".glmast set obal = obal - ".$revamt." where accountno = 250 and branch = '".$br."' and sub = 0");
		$db->execute();
		
		$db->query("update ".$findb.".fixassets set totdep = totdep - ".$revamt.", anndep = anndep - ".$revamt." where uid = ".$asid);
		$db->execute();
		
		// get local currency
		$db->query("select currency,rate from ".$findb.".forex where def_forex = 'Yes'");
		$row = $db->single();
		extract($row);		
		
		$db->query("select oth from ".$findb.".numbers");
		$row = $db->single();
		extract($row);
		$refno = $oth + 1;
		$db->query("update ".$findb.".numbers set oth = :refno");
		$db->bind(':refno', $refno);
		$db->execute();		
		
		$db->query("insert into ".$findb.".trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent,currency,rate,consign,your_ref) values (:ddate,:accountno,:branch,:sub,:accno,:br,:subbr,:debit,:credit,:reference,:gsttype,:descript1,:taxpcent,:currency,:rate,:consign,:your_ref)");
		$db->bind(':ddate', $dt);
		$db->bind(':accountno', 702);	
		$db->bind(':branch', $br);
		$db->bind(':sub', 0);
		$db->bind(':accno', 250);
		$db->bind(':br', $br);
		$db->bind(':subbr', 0);
		$db->bind(':debit', $revamt);	
		$db->bind(':credit', 0);
		$db->bind(':reference', 'OTH'.$refno);
		$db->bind(':gsttype', 'N-T');
		$db->bind(':descript1', 'Reverse depreciation on '.$asset);
		$db->bind(':taxpcent', 0);
		$db->bind(':currency', $currency);
		$db->bind(':rate', $rate);
		$db->bind(':consign', 1);
		$db->bind(':your_ref', '');
		
		$db->execute();	

		$db->query("insert into ".$findb.".trmain (ddate,accountno,branch,sub,accno,br,subbr,debit,credit,reference,gsttype,descript1,taxpcent,currency,rate,consign,your_ref) values (:ddate,:accountno,:branch,:sub,:accno,:br,:subbr,:debit,:credit,:reference,:gsttype,:descript1,:taxpcent,:currency,:rate,:consign,:your_ref)");
		$db->bind(':ddate', $dt);
		$db->bind(':accountno', 250);	
		$db->bind(':branch', $br);
		$db->bind(':sub', 0);
		$db->bind(':accno', 702);
		$db->bind(':br', $br);
		$db->bind(':subbr', 0);
		$db->bind(':debit', 0);	
		$db->bind(':credit', $revamt);
		$db->bind(':reference', 'OTH'.$refno);
		$db->bind(':gsttype', 'N-T');
		$db->bind(':descript1', 'Reverse depreciation on '.$asset);
		$db->bind(':taxpcent', 0);
		$db->bind(':currency', $currency);
		$db->bind(':rate', $rate);
		$db->bind(':consign', 1);
		$db->bind(':your_ref', '');
		
		$db->execute();	

		$db->closeDB();

		?>
			<script>
			window.open("","fa_revdep").jQuery("#faslist").trigger("reloadGrid");
			this.close()
			</script>
		<?php
	}
	
?>
 

</body>
</html>

