
<body>

<?php
session_start();
$tid = $_REQUEST['tid'];

require("../../db.php");
$moduledb = $_SESSION['s_prcdb'];
mysql_select_db($moduledb) or die(mysql_error());

$q = 'delete from requirements where req_id = '.$tid;
$r = mysql_query($q) or die(mysql_error().' '.$q);
?>

</body>