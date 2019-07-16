<?php
session_start();

$findb = $_SESSION['s_findb'];

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select bedate,yrdate from ".$findb.".globals");
$row = $db->single();
extract($row);

$dt = explode('-',$yrdate);
$y = $dt[0];
$m = $dt[1];
$d = $dt[2];
$ddate = $d.'/'.$m.'/'.$y;

$today = date('Y-m-d');

$db->closeDB();

?>


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Reverse Depreciation</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">

<script>

window.name = "fa_revdep";

</script>

<style type="text/css">
<!--
.style1 {font-size: large}
-->
</style>
</head>

<body>

<form name="form1" id="form1" method="post" action="">
  <input type="hidden" name="yrdate" id="yrdate" value="<?php echo $yrdate; ?>">
  <input type="hidden" name="today" id="today" value="<?php echo $today; ?>">
  <table width="700" border="1" align="center" cellpadding="3" cellspacing="1">
    <tr>
      <td><div align="center" class="style1">Reverse Depreciation</div></td>
    </tr>
    <tr>
        <td><?php include "getfassets.php"; ?></td>
    </tr>
  </table>
</form>

</body>
</html>


