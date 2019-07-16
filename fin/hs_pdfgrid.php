<?php
session_start();

$theme = $_SESSION['deftheme'];
require_once("../includes/backgrounds.php");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>PDF Grid Template</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">

<script type="text/javascript">

	window.name = "endmonth";

</script>

</head>

<body>
<form name="form1" method="post" >
  <input type="hidden" name="curdt" id="curdt" value=<?php echo $lstatdt; ?>>
  <input type="hidden" name="lstdt" id="lstdt" value=<?php echo $pstatdt; ?>>
<br>
  <table width="880" border="0" align="center" cellpadding="3">
  <tr>
  	<td colspan="2" align="center" bgcolor="<?php echo $bghead; ?>"><label style="color: <?php echo $thfont; ?>; font-size:14px;">PDF Grid Template</label></td>
  </tr>
  <tr>
    <td colspan="2" class="boxlabelleft">Print a template, preferably on tracing paper, to assist in laying out pdf documents.</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td align="right"><input type="button" value="Print" name="run"  onClick="pdfgrid()" ></td>
  </tr>
  </table>
  

  
</form>
</body>
</html>