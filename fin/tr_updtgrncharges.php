<?php
session_start();
$usersession = $_SESSION['usersession'];

$tgrn = $_SESSION['s_uncostgrn'];
$tp = $_REQUEST['tp'];				  
$findb = $_SESSION['s_findb'];

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];

$chargefile = 'ztmp'.$user_id.'_charges';

// ensure all invtrans lines have same currency
$db->query("select distinct currency from ".$findb.".invtrans where ref_no = '".$tgrn."'");
$rows = $db->resultset();
$numrows = $db->rowcount();

if ($numrows > 1) {
	echo '<script>';
	echo 'alert("Please ensure all lines in this GRN have the same currency");';
	echo 'this.close();';
	echo '</script>';
}

// get currency for this grn line
$db->query("select currency,rate from ".$findb.".invhead where ref_no = '".$tgrn."'");
$row = $db->single();
extract($row);
$grncurrency = $currency;
$grnrate = $rate;

$db->query("select tempcost from ".$findb.".invtrans where ref_no = '".$tgrn."' and tempcost = 0");
$row = $db->resultset();
$numrows = $db->rowcount();

if ($numrows > 0) {
	echo '<script>';
	echo 'alert("Please cost all rows in this GRN");';
	echo 'this.close();';
	echo '</script>';

} else {

	// get the charges in  local currencies
	$db->query("select sum(charge / rate) as lchgs from ".$findb.".".$chargefile);
	$row = $db->single();
	$lcharges = $row['lchgs'];
	
	if ($lcharges > 0) {
		// by value
		if ($tp == 'val') {
			$db->query("select sum(tempcost) as tcost from ".$findb.".invtrans where ref_no = '".$tgrn."'");
			$row = $db->single();
			$totcost = $row['tcost'];
			
			$db->query("select uid, tempcost, taxpcent,quantity from ".$findb.".invtrans where ref_no = '".$tgrn."'");
			$rows = $db->resultset();
			
			foreach($rows as $row) {
				$cst = $row['tempcost'];
				$id = $row['uid'];
				//$qt = $row['quantity'];
				//$tx = $row['taxpcent'];
				$lchg = round($cst * $lcharges/$totcost, 2);
				//$tx = round($cst*$tx/100,2);
				//$pr = round($cst/$qt,2);

				//$db->query("update ".$findb.".invtrans set charges = ".$chg.", price = ".$pr.", tax = ".$tx.", value = tempcost where uid = ".$id);
				$db->query("update ".$findb.".invtrans set charges = ".$lchg." where uid = ".$id);
				$db->execute();	
			}
			
		} else {
			
			// by quantity
			$db->query("select sum(quantity) as tqty from ".$findb.".invtrans where ref_no = '".$tgrn."'");
			$row = $db->single();
			$totqty = $row['tqty'];
			
			$db->query("select uid, tempcost, taxpcent,quantity from ".$findb.".invtrans where ref_no = '".$tgrn."'");
			$rows = $db->resultset();
			
			foreach($rows as $row) {
				//$cst = $row['tempcost'];
				$id = $row['uid'];
				$qt = $row['quantity'];
				//$tx = $row['taxpcent'];
				$lchg = round($qt * $lcharges/$totqty, 2);
				//$tx = round($cst*$tx/100,2);
				//$pr = round($cst/$qt,2);
				//$db->query("update ".$findb.".invtrans set charges = ".$chg.", price = ".$pr.", tax = ".$tx.", value = tempcost where uid = ".$id);
				$db->query("update ".$findb.".invtrans set charges = ".$lchg." where uid = ".$id);
				$db->execute();	
			}
			
		}
	
	}
}

$db->closeDB();
			
?>

