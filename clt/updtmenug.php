<?php
session_start();
$usersession = $_COOKIE['usersession'];

$dbase = $_SESSION['s_admindb'];

require("../db.php");
mysql_select_db($dbase) or die(mysql_error());

$query = "select * from sessions where session_id = ".$usersession;
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);

$table = $sub_id.'_menu';

$query = "select menu_id,level,label,a1,a2,a3,a4,a5,a6,a7,a8,a9,a10,a11,a12,a13,a14,a15,a16,a17,a18,a19,a20 from ".$table." order by morder";
$menus1 = mysql_query($query) or die(mysql_error().$query);

require_once("../includes/backgrounds.php");
$theme = $_SESSION['deftheme'];


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Update Menu Groups</title>

<style type="text/css">
<!--
.style1 {font-size: small}
-->
</style>
</head>

<body>
<div style="background-color: <?php echo $bgcolour; ?>; height: 900px;" >
		<form name="form1" method="post" action="savemenug.php">
		<table border="0" cellspacing="1" cellpadding="2" align="left">
		<tr bgcolor="<?php echo $bghead; ?>">
		<th align="left"><label style="color: <?php echo $thfont; ?>">User Groups</label></th>
		<th><label style="color: <?php echo $thfont; ?>">1</label></th>
		<th><label style="color: <?php echo $thfont; ?>">2</label></th>
		<th><label style="color: <?php echo $thfont; ?>">3</label></th>
		<th><label style="color: <?php echo $thfont; ?>">4</label></th>
		<th><label style="color: <?php echo $thfont; ?>">5</label></th>
		<th><label style="color: <?php echo $thfont; ?>">6</label></th>
		<th><label style="color: <?php echo $thfont; ?>">7</label></th>
		<th><label style="color: <?php echo $thfont; ?>">8</label></th>
		<th><label style="color: <?php echo $thfont; ?>">9</label></th>
		<th><label style="color: <?php echo $thfont; ?>">10</label></th>
		<th><label style="color: <?php echo $thfont; ?>">11</label></th>
		<th><label style="color: <?php echo $thfont; ?>">12</label></th>
		<th><label style="color: <?php echo $thfont; ?>">13</label></th>
		<th><label style="color: <?php echo $thfont; ?>">14</label></th>
		<th><label style="color: <?php echo $thfont; ?>">15</label></th>
		<th><label style="color: <?php echo $thfont; ?>">16</label></th>
		<th><label style="color: <?php echo $thfont; ?>">17</label></th>
		<th><label style="color: <?php echo $thfont; ?>">18</label></th>
		<th><label style="color: <?php echo $thfont; ?>">19</label></th>
		<th><label style="color: <?php echo $thfont; ?>">20</label></th>
		</tr>
		<?php
		while ($row_menus = mysql_fetch_array($menus1)) {
			extract($row_menus);

		?>
		
		<tr>
		<?php
		switch ($level) {
			case 0:
				$gap = '';
				break;
			case 1:
				$gap = '&nbsp;&nbsp;&nbsp;';
				break;
			case 2:
				$gap = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
				break;		
			case 3:
				$gap = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
				break;		
		}
		?>
		<td><div align="left"><span class="style1"><label style="color: <?php echo $tdfont; ?>"><?php echo $gap.$label; ?></label></span></div></td>
		<td><input type="checkbox" <?php  if($a1 == "Y") {echo " CHECKED";}?> onclick="update(this.checked,'<?php echo $menu_id; ?>','a1')"></td>
		<td><input type="checkbox" <?php  if($a2 == "Y") {echo " CHECKED";}?> onclick="update(this.checked,'<?php echo $menu_id; ?>','a2')"></td>
		<td><input type="checkbox" <?php  if($a3 == "Y") {echo " CHECKED";}?> onclick="update(this.checked,'<?php echo $menu_id; ?>','a3')"></td>
		<td><input type="checkbox" <?php  if($a4 == "Y") {echo " CHECKED";}?> onclick="update(this.checked,'<?php echo $menu_id; ?>','a4')"></td>
		<td><input type="checkbox" <?php  if($a5 == "Y") {echo " CHECKED";}?> onclick="update(this.checked,'<?php echo $menu_id; ?>','a5')"></td>
		<td><input type="checkbox" <?php  if($a6 == "Y") {echo " CHECKED";}?> onclick="update(this.checked,'<?php echo $menu_id; ?>','a6')"></td>
		<td><input type="checkbox" <?php  if($a7 == "Y") {echo " CHECKED";}?> onclick="update(this.checked,'<?php echo $menu_id; ?>','a7')"></td>
		<td><input type="checkbox" <?php  if($a8 == "Y") {echo " CHECKED";}?> onclick="update(this.checked,'<?php echo $menu_id; ?>','a8')"></td>
		<td><input type="checkbox" <?php  if($a9 == "Y") {echo " CHECKED";}?> onclick="update(this.checked,'<?php echo $menu_id; ?>','a9')"></td>
		<td><input type="checkbox" <?php  if($a10 == "Y") {echo " CHECKED";}?> onclick="update(this.checked,'<?php echo $menu_id; ?>','a10')"></td>
		<td><input type="checkbox" <?php  if($a11 == "Y") {echo " CHECKED";}?> onclick="update(this.checked,'<?php echo $menu_id; ?>','a11')"></td>
		<td><input type="checkbox" <?php  if($a12 == "Y") {echo " CHECKED";}?> onclick="update(this.checked,'<?php echo $menu_id; ?>','a12')"></td>
		<td><input type="checkbox" <?php  if($a13 == "Y") {echo " CHECKED";}?> onclick="update(this.checked,'<?php echo $menu_id; ?>','a13')"></td>
		<td><input type="checkbox" <?php  if($a14 == "Y") {echo " CHECKED";}?> onclick="update(this.checked,'<?php echo $menu_id; ?>','a14')"></td>
		<td><input type="checkbox" <?php  if($a15 == "Y") {echo " CHECKED";}?> onclick="update(this.checked,'<?php echo $menu_id; ?>','a15')"></td>
		<td><input type="checkbox" <?php  if($a16 == "Y") {echo " CHECKED";}?> onclick="update(this.checked,'<?php echo $menu_id; ?>','a16')"></td>
		<td><input type="checkbox" <?php  if($a17 == "Y") {echo " CHECKED";}?> onclick="update(this.checked,'<?php echo $menu_id; ?>','a17')"></td>
		<td><input type="checkbox" <?php  if($a18 == "Y") {echo " CHECKED";}?> onclick="update(this.checked,'<?php echo $menu_id; ?>','a18')"></td>
		<td><input type="checkbox" <?php  if($a19 == "Y") {echo " CHECKED";}?> onclick="update(this.checked,'<?php echo $menu_id; ?>','a19')"></td>
		<td><input type="checkbox" <?php  if($a20 == "Y") {echo " CHECKED";}?> onclick="update(this.checked,'<?php echo $menu_id; ?>','a20')"></td>
		</tr>
		
		<?php
		}
		
		?>
		</table>
		</form>
		<?php
        mysql_free_result($menus1);
        ?>
</div>

</body>
</html>
