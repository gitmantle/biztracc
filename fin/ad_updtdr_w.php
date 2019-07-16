<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// get theme for this user
$theme = $_SESSION['deftheme'];
require_once("../includes/backgrounds.php");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Update Debtors</title>

<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<link type="text/css" href="../includes/jquery/themes/<?php echo $theme; ?>/jquery.ui.all.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="../includes/jquery/themes/ui.jqgrid.css" />
<link rel="stylesheet" type="text/css" media="screen" href="../includes/jquery/themes/ui.multiselect.css"/> 

<script src="../includes/jquery/js/jquery.js" type="text/javascript"></script>
<script src="../includes/jquery/js/i18n/grid.locale-en.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery-ui-custom.min.js" type="text/javascript"></script>
<script type="text/javascript" src="../includes/jquery/ui/jquery.ui.core.js"></script>
<script type="text/javascript" src="../includes/jquery/ui/jquery.ui.widget.js"></script>
<script type="text/javascript" src="../includes/jquery/ui/jquery.ui.position.js"></script>
<script type="text/javascript" src="../includes/jquery/ui/jquery.ui.menu.js"></script>
<script type="text/javascript" src="../includes/jquery/external/jquery.bgiframe-2.1.1.js"></script>
<script src="../includes/jquery/js/jquery.jqGrid.min.js" type="text/javascript"></script>
<script type="text/javascript" src="js/fin.js"></script>


<script type="text/javascript">

	window.name = "ad_updtdr_w";

</script>
</head>
<body>
<div id="bwin">
  <form name="form1" id="form1" method="post" action="">
    <table align="center" width="960">
      <tr>
        <td>Company/Last name &nbsp;
          <input type="text" id="searchlegalname" size="12" onkeydown="doSearch(arguments[0]||event)" />
          &nbsp;<img src="../images/Search.gif" alt="Search" title="Search" onclick="gridReload1()" /> &nbsp;&nbsp;</td>
          <td>Add to Debtors from this Client List</td>
      </tr>
      <tr>
        <td><?php include "getdr.php" ?></td>
        <td><?php include "getcltdr.php" ?></td>
    </table>
  </form>
</div>
</body>
</html>