<?php
session_start();
require("../db.php");

$moduledb = $_SESSION['s_findb'];
mysql_select_db($moduledb) or die(mysql_error());


require_once("../includes/backgrounds.php");
$theme = $_SESSION['deftheme'];


?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
<title>Update Staff</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">

<script>

window.name = 'hs_users';

</script>

</head>

<body>
  <table width="950" border="0">
    <tr>
      <td><?php include "getusers.php"; ?></td>
    </tr>
  </table>
</body>

</html>