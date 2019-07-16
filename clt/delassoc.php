<?php
ini_set('display_errors', true);
session_start();
$dbs = "ken47109_kenny";

require("../db.php");
mysql_select_db($dbs) or die(mysql_error());

$associd = $_REQUEST['asid'];
$from = $_REQUEST['from'];
$memid = $_SESSION['s_memberid'];
$partnerid = $_SESSION['s_partnerid'];


?>



<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Delete Associaton</title>
</head>

<body> 

<?php
	if ($from == 'm') {
		$q = "delete from assoc_xref where member_id = ".$memid." and of_id = ".$associd;
		$r = mysql_query($q) or die(mysql_error().$q);
	
		$q = "delete from assoc_xref where member_id = ".$associd." and of_id = ".$memid;
		$r = mysql_query($q) or die(mysql_error().$q);
	} else {
		$q = "delete from assoc_xref where member_id = ".$partnerid." and of_id = ".$associd;
		$r = mysql_query($q) or die(mysql_error().$q);
	
		$q = "delete from assoc_xref where member_id = ".$associd." and of_id = ".$partnerid;
		$r = mysql_query($q) or die(mysql_error().$q);
	}

?>

	<script>
			var from = '<?php echo $from; ?>';
			if (from == 'm') {
				window.open("","editmembers").jQuery("#massociationslist").trigger("reloadGrid");
			} 
			if (from == 'p') {
				window.open("","editmembers").jQuery("#passociationslist").trigger("reloadGrid");
			}
			this.close();		
    </script>

</body>
</html>
