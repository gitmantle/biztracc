<?php
session_start();

$theme = $_SESSION['deftheme'];
require_once("../includes/backgrounds.php");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Find Complainant</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<link type="text/css" href="../includes/jquery/themes/<?php echo $theme; ?>/jquery.ui.all.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="../includes/jquery/themes/ui.jqgrid.css" />
<script src="../includes/jquery/js/jquery.js" type="text/javascript"></script>
<script src="../includes/jquery/js/i18n/grid.locale-en.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery-ui-1.8.4.custom.min.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery.jqGrid.min.js" type="text/javascript"></script>
<script type="text/javascript" src="../includes/jquery/ui/jquery.ui.widget.js"></script>

<script type="text/javascript">

window.name = "findcomp";

function selcomp(memid,fname) {
	  opener.document.form1.tcomplainant.value = fname;
	  opener.document.form1.mid.value = memid;
	  this.close();
}

</script>
</head>
<body>
<form name="updtmem" id="updtmem" method="post" action="">
 <table width="470" border="0" bgcolor="<?php echo $bgcolor; ?>">
   <tr>
     <th align="center" bgcolor="<?php echo $bghead; ?>"><label style="color: <?php echo $tdfont; ?>;font-size:18px">Select Complainant</label></th>
   </tr>
   <tr>
     <td><?php include "getComplainants.php" ?></td>
   </tr>
 </table>
</form>

</body>
</html>