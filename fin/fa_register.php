<?php
session_start();

//ini_set('display_errors', true);

$usersession = $_SESSION['usersession'];

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];
$subscriber = $subid;
$sname = $row['uname'];

$fafile = 'ztmp'.$user_id.'_assets';

$findb = $_SESSION['s_findb'];

$db->query("drop table if exists ".$findb.".".$fafile);
$db->execute();

// create temporary asset register table
$db->query("create table ".$findb.".".$fafile." (uid integer(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, aname varchar(45), acost decimal(16,2) default 0, adepn decimal(16,2) default 0, abv decimal(16,2) default 0, atot decimal(16,2) default 0, Totline char(1) default 'N', rate decimal(5,2) default 0, bought date default '0000-00-00') engine InnoDB"); 
$db->execute();

$today = date("d-m-Y");

$head = "Asset Register as at " . $today;
$_SESSION['s_asheading'] = $head;

$db->query("select hcode,heading from ".$findb.".assetheadings where heading != 'SPARE'");
$rows = $db->resultset();
foreach ($rows as $row) {
	extract($row);
	$group = $hcode;
	$fahname = $heading;
	$db->query("select sum(cost) as totcost, sum(totdep) as totad from ".$findb.".fixassets where hcode = '".$group."'");
	$rowfa = $db->single();
	extract($rowfa);	
	$totalcost = ($totcost == "") ? 0 : $totcost;
	$totaldepn = ($totad == "") ? 0 : $totad;
	$totalbv = $totalcost - $totaldepn;
	$db->query("insert into ".$findb.".".$fafile." (aname,atot,Totline) values ('".$fahname."',".$totalbv.",'Y')");
	$db->execute();
	$db->query("select asset,cost,totdep,rate,bought from ".$findb.".fixassets where hcode = '".$group."' and (cost + totdep != 0) order by asset");
	$rowsa = $db->resultset();
	foreach ($rowsa as $rowfa) {
		extract($rowfa);
		$bv = $cost - $totdep;
		$asname = '  '.$asset;
		$db->query("insert into ".$findb.".".$fafile." (aname,acost,adepn,abv,Totline,rate,bought) values ('".$asname."',".$cost.",".$totdep.",".$bv.",'N',".$rate.",'".$bought."')");
		$db->execute();
	}

}

$db->closeDB();

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Asset Register</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<script type="text/javascript">
	var heading = '<?php echo $head; ?>';

	var x = 0, y = 0; // default values	
 	x = window.screenX +5;
  	y = window.screenY +265;
	
	function fa2pdf() {
		window.open('fa2pdf.php?heading='+heading,'fapdf','toolbar=0,scrollbars=1,height=500,width=1020,resizeable=yes,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	}
	
	function fa2xl() {
		window.open('fa2excel.php?heading='+heading,'faxl','toolbar=0,scrollbars=1,height=500,width=1020,resizeable=yes,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	}
	

</script>

<style type="text/css">
<!--
.style2 {font-size: 12px}
-->
</style>
</head>

<body>

    <table align="center">
        <tr>
	        <td><?php include "getassets.php"; ?></td>
        </tr>
	</table>		



</body>
</html>
