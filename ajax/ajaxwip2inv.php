<html>
<body>


<?php
// function to populate invoice from wip listing
session_start();
$usersession = $_SESSION['usersession'];

include_once("../includes/DBClass.php");
$db = new DBClass();


$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];


$dstring = $_REQUEST['dstring'];
if (strlen($dstring) == 0) {
	echo '<script>';
	echo 'alert("No WIP items selected");';
	echo 'this.close();';
	echo '</script>';
	return false;
}

$alines = explode(',',$dstring);
$draccno = $_REQUEST['draccno'];

$findb = $_SESSION['s_findb'];

$wiptable = 'ztmp'.$user_id.'_wip';
$table = 'ztmp'.$user_id.'_trading';
$drfile = 'ztmp'.$user_id.'_drlist';

$db->query("select priceband from ".$findb.".".$drfile." where accountno = ".$draccno);
$row = $db->single();
$priceband = $row['priceband'];

$db->query("select setprice from ".$findb.".stkpricepcent where uid = ".$priceband);
$row = $db->single();
$price = $row['setprice'];

foreach ($alines as $itm) {
	
	$db->query("select date(datestarted) as dt,hours from ".$findb.".".$wiptable." where uid = ".$itm);
	$row = $db->single();
	$start = $row['dt'];
	$hours = $row['hours'];

	$value = round($price * $hours,2);		
		
	$db->query("insert into ".$findb.".".$table." (itemcode,item,price,unit,quantity,tax,value,tot,taxindex,taxtype,taxpcent,sellacc,sellbr,sellsub,groupid,catid) values (:itemcode,:item,:price,:unit,:quantity,:tax,:value,:tot,:taxindex,:taxtype,:taxpcent,:sellacc,:sellbr,:sellsub,:groupid,:catid)");	
	$db->bind(':itemcode', 'BK');
	$db->bind(':item', 'Bookkeeping on '.$start);
	$db->bind(':price', $price);
	$db->bind(':unit', 'Hour');
	$db->bind(':quantity', $hours);
	$db->bind(':tax', 0);
	$db->bind(':value', $value);
	$db->bind(':tot', $value);
	$db->bind(':taxindex', 3);
	$db->bind(':taxtype', 'N-T');
	$db->bind(':taxpcent', 0);
	$db->bind(':sellacc', 1);
	$db->bind(':sellbr', '');
	$db->bind(':sellsub', 1);
	$db->bind(':groupid', 1);
	$db->bind(':catid', 1);

	$db->execute();

	$db->query("update ".$findb.".wip set posted = 'Yes' where uid = ".$itm);
	$db->execute();
	
}


?>

</body>
</html>
