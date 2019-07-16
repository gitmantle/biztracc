<?php
session_start();
$findb = $_SESSION['s_findb'];

$theme = $_SESSION['deftheme'];
require_once("../includes/backgrounds.php");

?>


<html>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Add a Branch</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<link type="text/css" href="../includes/jquery/themes/<?php echo $theme; ?>/jquery.ui.all.css" rel="stylesheet" />
<script src="../includes/jquery/js/jquery.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery-ui-custom.min.js" type="text/javascript"></script>
<script type="text/javascript" src="../includes/jquery/ui/jquery.ui.widget.js"></script>


<style type="text/css">
<!--
.style1 {font-size: large}
-->
</style>
</head>


<body>
<div id="swin">
<form name="form1" method="post" >

  <table width="500" border="0" align="center">
    <tr>
      <td colspan="2"><div align="center" class="style1"><u>Add a Branch </u></div></td>
    </tr>
	<tr>
	<td class="boxlabel">Branch Name</td>
	<td><input type="text" name="desc" id="desc" ></td>
	</tr>	
    <tr>
      <td>&nbsp;</td>
      <td align="right"><input type="submit" value="Save" name="save" ></td>
    </tr>
  </table>
</form>
</div>

<?php

	if(isset($_POST['save'])) {
	
		if ($_REQUEST['desc'] == '') {
			echo '<script>';
			echo 'alert("Please enter a branch Name.")';
			echo '</script>';	
		} else {	
		
		$brname = ucwords(trim($_REQUEST['desc']));
		
		include_once("../includes/DBClass.php");
		$db = new DBClass();
		
		$db->query("select * from sessions where session = :vusersession");
		$db->bind(':vusersession', $usersession);
		$row = $db->single();
		$subid = $row['subid'];
		$user_id = $row['user_id'];
		$subscriber = $subid;
		$sname = $row['uname'];
		$userip = $row['userip'];
		
		$db->query("insert into ".$findb.".branch (branchname) values ('".$brname."')");
		$db->execute();
		$cd = $db->lastInsertId();
		$code = 1000 + $cd;
		$db->query("update ".$findb.".branch set branch = '".$code."' where uid = ".$cd);
		$db->execute();
		
		// insert system accounts into glmast 
			
		$rows = array(
				array( 'LIB', 'CREDITORS CONTROL', 851, $code, 0, 0.00, 0.00, 0.00, 0.00, 'N', 'N', 'Y', 'N', 20, 'Y', 'Y', 0, 0),
				array( 'OAS', 'DEBTORS CONTROL', 801, $code, 0, 0.00, 0.00, 0.00, 0.00, 'N', 'N', 'Y', 'N', 20, 'Y', 'Y', 0, 0),
				array( 'INV', 'FIXED ASSETS CONTROL', 701, $code, 0, 0.00, 0.00, 0.00, 0.00, 'N', 'N', 'Y', 'N', 20, 'Y', 'Y', 0, 0),
				array( 'INV', 'ACCUMULATED DEPRECIATION', 702, $code, 0, 0.00, 0.00, 0.00, 0.00, 'N', 'N', 'Y', 'N', 20, 'Y', 'Y', 0, 0),
				array( 'SIN', 'ROUNDING', 99, $code, 0, 0.00, 0.00, 0.00, 0.00, 'N', 'N', 'Y', 'N', 20, 'Y', 'Y', 0, 0),
				array( 'EQT', 'INTER BRANCH TRANSFER', 997, $code, 0, 0.00, 0.00, 0.00, 0.00, 'N', 'N', 'Y', 'N', 20, 'Y', 'Y', 0, 0),
				array( 'EXP', 'DEPRECIATION', 250, $code, 0, 0.00, 0.00, 0.00, 0.00, 'N', 'N', 'Y', 'N', 20, 'Y', 'Y', 0, 0),
				array( 'EXP', 'PAYROLL', 500, $code, 0, 0.00, 0.00, 0.00, 0.00, 'N', 'N', 'Y', 'N', 20, 'Y', 'Y', 0, 0),
				array( 'EXP', 'Net Payroll', 500, $code, 1, 0.00, 0.00, 0.00, 0.00, 'N', 'N', 'Y', 'N', 20, 'N', 'Y', 0, 0),
				array( 'EXP', 'Tax Deducted', 500, $code, 2, 0.00, 0.00, 0.00, 0.00, 'N', 'N', 'Y', 'N', 20, 'N', 'Y', 0, 0),
				array( 'EXP', 'Other Deductions', 500, $code, 3, 0.00, 0.00, 0.00, 0.00, 'N', 'N', 'Y', 'N', 20, 'N', 'Y', 0, 0),
				array( 'LIB', 'PROVISION FOR PAY', 880, $code, 0, 0.00, 0.00, 0.00, 0.00, 'N', 'N', 'Y', 'N', 20, 'N', 'Y', 0, 0),
				array( 'EQT', 'RETAINED EARNINGS', 998, $code, 0, 0.00, 0.00, 0.00, 0.00, 'N', 'N', 'Y', 'N', 20, 'N', 'Y', 0, 0),
				array( 'EQT', 'JOURNAL', 999, $code, 0, 0.00, 0.00, 0.00, 0.00, 'N', 'N', 'Y', 'N', 20, 'N', 'Y', 0, 0),
				array( 'BAN', 'BANK', 751, $code, 0, 0.00, 0.00, 0.00, 0.00, 'N', 'N', 'Y', 'N', 20, 'N', 'Y', 0, 0),
				array( 'BAN', 'CASH ON HAND', 755, $code, 0, 0.00, 0.00, 0.00, 0.00, 'N', 'N', 'Y', 'N', 20, 'N', 'Y', 0, 0),
				array( 'LIB', 'CREDIT CARDS', 860, $code, 0, 0.00, 0.00, 0.00, 0.00, 'N', 'N', 'Y', 'N', 20, 'N', 'Y', 0, 0),
				array( 'COS', 'OPENING STOCK', 181, $code, 0, 0.00, 0.00, 0.00, 0.00, 'N', 'N', 'Y', 'N', 20, 'N', 'Y', 0, 0),
				array( 'COS', 'CLOSING STOCK', 187, $code, 0, 0.00, 0.00, 0.00, 0.00, 'N', 'N', 'Y', 'N', 20, 'N', 'Y', 0, 0),
				array( 'EXP', 'STOCK ADJUSTMENT', 699, $code, 0, 0.00, 0.00, 0.00, 0.00, 'N', 'N', 'Y', 'N', 20, 'N', 'Y', 0, 0),
				array( 'OAS', 'STOCK ON HAND', 825, $code, 0, 0.00, 0.00, 0.00, 0.00, 'N', 'N', 'Y', 'N', 20, 'N', 'Y', 0, 0),
				array( 'COS', 'WORK IN PROGRESS', 190, $code, 0, 0.00, 0.00, 0.00, 0.00, 'N', 'N', 'Y', 'N', 20, 'N', 'Y', 0, 0),
				array( 'LIB', 'TRADING TAX PAYABLE', 870, $code, 0, 0.00, 0.00, 0.00, 0.00, 'N', 'N', 'Y', 'N', 20, 'N', 'Y', 0, 0),
				array( 'LIB', 'TRADING TAX DUE ON PAYMENT', 871, $code, 0, 0.00, 0.00, 0.00, 0.00, 'N', 'N', 'Y', 'N', 20, 'N', 'Y', 0, 0),
				array( 'INC', 'DISCOUNT ON SALES', 76, $code, 0, 0.00, 0.00, 0.00, 0.00, 'N', 'N', 'Y', 'N', 20, 'N', 'Y', 0, 0),
				array( 'COS', 'DISCOUNT ON PURCHASES', 186, $code, 0, 0.00, 0.00, 0.00, 0.00, 'N', 'N', 'Y', 'N', 20, 'N', 'Y', 0, 0),
				array( 'INC', 'OVERS & UNDERS ON EXCHANGE', 79, $code, 0, 0.00, 0.00, 0.00, 0.00, 'N', 'N', 'Y', 'N', 20, 'N', 'Y', 0, 0),
				array( 'XXX', 'JOURNAL DUMMY ACCOUNT', 1000, $code, 0, 0.00, 0.00, 0.00, 0.00, 'N', 'N', 'Y', 'N', 20, 'Y', 'Y', 0, 0),
				array( 'XXX', 'ACCUMULATED DEPRECIATION', 5000, $code, 0, 0.00, 0.00, 0.00, 0.00, 'N', 'N', 'Y', 'N', 20, 'N', 'Y', 0, 0)
	  
		);
		
		$db->query("insert into ".$findb.".glmast (grp, account, accountno, branch, sub, obal, obalm, prevbal, lastyear, recon, blocked, active, paygst, sc, ctrlacc, system, ird, ird2) VALUES (:grp, :account, :accountno, :branch, :sub, :obal, :obalm, :prevbal, :lastyear, :recon, :blocked, :active, :paygst, :sc, :ctrlacc, :system, :ird, :ird2)");
		foreach ($rows as $item) {
			$db->bind(':grp', $item[0]);
			$db->bind(':account', $item[1]);
			$db->bind(':accountno', $item[2]);
			$db->bind(':branch', $item[3]);
			$db->bind(':sub', $item[4]);
			$db->bind(':obal', $item[5]);
			$db->bind(':obalm', $item[6]);
			$db->bind(':prevbal', $item[7]);
			$db->bind(':lastyear', $item[8]);
			$db->bind(':recon', $item[9]);
			$db->bind(':blocked', $item[10]);
			$db->bind(':active', $item[11]);
			$db->bind(':paygst', $item[12]);
			$db->bind(':sc', $item[13]);
			$db->bind(':ctrlacc', $item[14]);
			$db->bind(':system', $item[15]);
			$db->bind(':ird', $item[16]);
			$db->bind(':ird2', $item[17]);
			$db->execute();
		}		
			

		$db->closeDB();

		?>
		<script>
		window.open("","ad_branches").jQuery("#branchlist").trigger("reloadGrid");
		this.close();
		</script>
		<?php
		
		}
	}

?>


</body>
</html>
