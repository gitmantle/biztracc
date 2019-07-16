<?php
session_start();

//ini_set('display_errors', true);

include_once("../includes/DBClass.php");
$db = new DBClass();

$cltdb = $_SESSION['s_cltdb'];

$db->query("select menu_id,level,label,a1,a2,a3,a4,a5,a6,a7,a8,a9,a10,a11,a12,a13,a14,a15,a16,a17,a18,a19,a20 from ".$cltdb.".c_menu order by morder");
$rows = $db->resultset();

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Update Menu Groups</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">

<script>

function createRequestObject(){
	var request_o; //declare the variable to hold the object.
	var browser = navigator.appName; //find the browser name
	if(browser == "Microsoft Internet Explorer"){
		request_o = new ActiveXObject("Microsoft.XMLHTTP");
	}else{
		request_o = new XMLHttpRequest();
	}
	return request_o; //return the object
}

var inven = createRequestObject();

function update(value,uid,field) {
	var urlvar = "hs_savemenug.php?value="+value+"&uid="+uid+"&field="+field+"&RandomKey=" + Math.random() * Date.parse(new Date());
	
	inven.open('get', urlvar);
	
	inven.onreadystatechange = disp_res;
	inven.send(null);
}

function disp_res()
{
	if(inven.readyState == 4){ //Finished loading the response
		var response = inven.responseText;
	
		if(response != "") {
			//alert(response);
		}
	}
}




</script>

<style type="text/css">
<!--
.style1 {font-size: small}
-->
</style>
</head>

<body>
		<form name="form1" method="post" action="hs_savemenug.php">
		<table border="0" cellspacing="1" cellpadding="2" >
		<tr bgcolor="#0099FF">
		<th>User Groups </th>
		<th>1</th>
		<th>2</th>
		<th>3</th>
		<th>4</th>
		<th>5</th>
		<th>6</th>
		<th>7</th>
		<th>8</th>
		<th>9</th>
		<th>10</th>
		<th>11</th>
		<th>12</th>
		<th>13</th>
		<th>14</th>
		<th>15</th>
		<th>16</th>
		<th>17</th>
		<th>18</th>
		<th>19</th>
		<th>20</th>
		</tr>
		<?php
		foreach ($rows as $row) {
			extract($row);

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
		<td><div align="left"><span class="style1"><?php echo $gap.$label; ?></span></div></td>
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
$db->closeDB();
?>
</body>
</html>
