<?php
session_start();
$usersession = $_SESSION['usersession'];

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];

$findb = $_SESSION['s_findb'];
$snfile = 'ztmp'.$user_id.'_sn';

$db->query("drop table if exists ".$findb.".".$snfile);
$db->execute();

$db->query("create table ".$findb.".".$snfile." (itemcode varchar(30) default '', item varchar(100) default '', serialno varchar(50) default '', ddate date, ref_in varchar(15) default '', ref_out varchar(15) default '', location varchar(40) default '', branch varchar(25) default '')  engine myisam");
$db->execute();

$db->query("select s.itemcode,s.item,s.serialno,s.date,s.ref_no,s.sold,b.branchname,l.location from ".$findb.".stkserials s, ".$findb.".stklocs l, ".$findb.".branch b where s.locationid = l.uid and s.branch = b.branch");
$rows = $db->resultset();
if (count($rows) > 0) {
	foreach ($rows as $row) {
		extract($row);
		$db->query("insert into ".$findb.".".$snfile." (itemcode,item,serialno,ddate,ref_in,ref_out,location,branch) values (:itemcode,:item,:serialno,:ddate,:ref_in,:ref_out,:location,:branch)");
		$db->bind(':itemcode', $itemcode);
		$db->bind(':item', $item);
		$db->bind(':serialno', $serialno);
		$db->bind(':ddate', $date);
		$db->bind(':ref_in', $ref_no);
		$db->bind(':ref_out', $sold);
		$db->bind(':location', $location);
		$db->bind(':branch', $branchname);
		
		$db->execute();
	}
}

// Add uid
$db->query("alter table ".$findb.".".$snfile." add `uid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY");
$db->execute();

$db->closeDB();

$theme = $_SESSION['deftheme'];
require_once("../includes/backgrounds.php");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Track Serial Numbers</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<link type="text/css" href="../includes/jquery/themes/<?php echo $theme; ?>/jquery.ui.all.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="../includes/jquery/themes/ui.jqgrid.css" />
<script src="../includes/jquery/js/jquery.js" type="text/javascript"></script>
<script src="../includes/jquery/js/i18n/grid.locale-en.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery-ui-custom.min.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery.jqGrid.min.js" type="text/javascript"></script>
<script type="text/javascript" src="../includes/jquery/ui/jquery.ui.widget.js"></script>
<script>

	window.name = "viewsn";


</script>

</head>

<body>
<form name="form1" method="post" >
<br>
  <table width="190" border="0" align="center" >
  <tr>
        <td colspan="2"><?php include "getsns.php"; ?></td>
  </tr>
  </table>
  
  
</form>
</body>
</html>