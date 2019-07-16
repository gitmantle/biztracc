<?php
$dbs = "tptlogs";

require("../db.php");
mysql_select_db($dbs) or die(mysql_error());

$cluid = $_REQUEST['uid'];

	$q = "delete from dockets where docket_id = ".$cluid;
	$r = mysql_query($q);


?>



<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Delete Docket</title>
</head>

<body> 



	<script>
	  window.open("","dockets").jQuery("#docketlist").trigger("reloadGrid");
	  this.close();
	</script>

</body>
</html>
