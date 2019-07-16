<?php
session_start();
$usersession = $_SESSION['usersession'];

$ddate = date("d/m/Y");
$hdate = date("Y-m-d");

$admindb = $_SESSION['s_admindb'];
require("../db.php");
mysql_select_db($admindb) or die(mysql_error());
$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);

$subscriber = $subid;

require_once("../includes/backgrounds.php");
$theme = $_SESSION['deftheme'];


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Update Distribution Lists</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<link type="text/css" href="../includes/jquery/themes/<?php echo $theme; ?>/jquery.ui.all.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="../includes/jquery/themes/ui.jqgrid.css" />
<script language="javascript" type="text/javascript" src="../includes/datetimepick/datetimepicker.js"></script>


<script type="text/javascript">

window.name = "updtdistribution";

</script>
</head>
<body>
<div style="width: 1000px; height: 500px;">
  <form name="updtdist" id="updtdist" method="post" action="">
    <table width="950" border="0">
      <tr>
        <td><?php include "getDistList.php" ?></td>
        <td><?php include "getDistDetails.php" ?></td>
      </tr>
    </table>
  </form>
</div>
</script>
</body>
</html>