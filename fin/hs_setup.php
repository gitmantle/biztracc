<?php
session_start();

include_once("../includes/DBClass.php");
$db = new DBClass();

$findb = $_SESSION['s_findb'];

$db->query("select coyname,bustype1,bustype2,ad1,ad2,ad3,boxno,po,email,telno,faxno,uid from ".$findb.".globals");
$globals = $db->single(); 
extract($globals);

$db->closeDB();

require_once("../includes/backgrounds.php");
$theme = $_SESSION['deftheme'];


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<title>Company Details Setup</title>
<script type="text/javascript">

window.name = "hs_setup";

</script>
</head>
<body>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="885">
  <tr bgcolor="<?php echo $bghead; ?>">
    <td colspan="4"><label style="color: <?php echo $thfont; ?>"><strong>Setup details for <?php echo $coyname; ?> </strong></label></td>
  <tr>
    <td><div align="left">Type of Business - line 1 </div></td>
    <td><?php echo $bustype1; ?></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><div align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- line 2 </div></td>
    <td><?php echo $bustype2; ?></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><div align="left"></div></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><div align="left">Street address </div></td>
    <td><?php echo $ad1; ?></td>
    <td><div align="left">PO Box </div></td>
    <td><?php echo $boxno; ?></td>
  </tr>
  <tr>
    <td><div align="left"></div></td>
    <td><?php echo $ad2; ?></td>
    <td><div align="left">Post Office </div></td>
    <td><?php echo $po; ?></td>
  </tr>
  <tr>
    <td><div align="left">City</div></td>
    <td><?php echo $ad3; ?></td>
    <td><div align="left"></div></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><div align="left"></div></td>
    <td>&nbsp;</td>
    <td><div align="left">Phone</div></td>
    <td><?php echo $telno; ?></td>
  </tr>
  <tr>
    <td><div align="left">email</div></td>
    <td><?php echo $email; ?></td>
    <td><div align="left">Fax</div></td>
    <td><?php echo $faxno; ?></td>
  </tr>
  <tr>
    <td><div align="left"></div></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td><div align="right">
        <input type="button" name="Edit" value="Edit" onClick="editsetup(<?php echo "'".$coyid."'"; ?>)">
      </div></td>
  </tr>
</table>
</body>
</html>
