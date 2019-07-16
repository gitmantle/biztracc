<?php
session_start();

$uid = $_REQUEST['uid'];

$findb = $_SESSION['s_findb'];

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from ".$findb.".taxtypes where uid = ".$uid);
$row = $db->single();
extract($row);
$dg = $defgst;
$dn = $defn_t;

// populate default gst list
    $arr = array('No', 'Yes');
	$defgst_options = '';
    for($i = 0; $i < count($arr); $i++)	{
		if ($arr[$i] == $dg) {
			$selected = 'selected="selected"';
		} else {
			$selected = '';
		}	
		$defgst_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
 	}
// populate default no tax list
    $arr = array('No', 'Yes');
	$defn_t_options = '';
    for($i = 0; $i < count($arr); $i++)	{
		if ($arr[$i] == $dn) {
			$selected = 'selected="selected"';
		} else {
			$selected = '';
		}	
		$defn_t_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
 	}

$db->closeDB();

$theme = $_SESSION['deftheme'];
require_once("../includes/backgrounds.php");

?>


<html>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Edit a Trading Tax Type</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<link type="text/css" href="../includes/jquery/themes/<?php echo $theme; ?>/jquery.ui.all.css" rel="stylesheet" />
<script src="../includes/jquery/js/jquery.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery-ui-custom.min.js" type="text/javascript"></script>
<script type="text/javascript" src="../includes/jquery/ui/jquery.ui.widget.js"></script>


<style type="text/css">
<!--
.style1 {font-size: large}
-->
</style>
</head>

<body>

<div id="bwin">
<form name="form1" method="post" >

  <table width="500" border="0" align="center">
    <tr>
      <td colspan="2"><div align="center" class="style1"><u>Edit a Trading Tax Type </u></div></td>
    </tr>
    <tr>
      <td class="boxlabel">Tax Code</td>
      <td><input name="code" type="text" id="code"  size="5" maxlength="3" value="<?php echo $tax; ?>"
></td>
    </tr>
	<tr>
	<td class="boxlabel">Description</td>
	<td><input type="text" name="desc" id="desc" value="<?php echo $description; ?>"
></td>
	</tr>	
    <tr>
      <td class="boxlabel">Percentage</td>
      <td><input type="text" name="pcent" id="pcent" value="<?php echo $taxpcent; ?>"
 >% </td>
    </tr>
    <tr>
      <td class="boxlabel">Is this default tax type</td>
      <td><select name="defgst" id="defgst">
		<?php echo $defgst_options; ?>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabel">Is this default no tax type</td>
      <td><select name="defn_t" id="defn_t">
		<?php echo $defn_t_options; ?>
      </select></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td align="right"><input type="submit" value="Save" name="save" ></td>
    </tr>
  </table>
</form>
</div>
<?php

	if(isset($_POST['save'])) {
		if ($_REQUEST['code'] == '') {
			echo '<script>';
			echo 'alert("Please enter a tax code.")';
			echo '</script>';	
		} else {	
		
		$tax = $_REQUEST['code'];
		$desc = $_REQUEST['desc'];
		$pcent = $_REQUEST['pcent'];
		$defgst = $_REQUEST['defgst'];
		$defn_t = $_REQUEST['defn_t'];
		
		include_once("../includes/DBClass.php");
		$db = new DBClass();
		
		if ($defgst == 'Yes') {
			$db->query("update ".$findb.".taxtypes set defgst = 'N'");
			$db->execute();
		}
		if ($defn_t == 'Yes') {
			$db->query("update ".$findb.".taxtypes set defn_t = 'N'");
			$db->execute();
		}
		
		$db->query("update ".$findb.".taxtypes set tax = :tax, description = :description, taxpcent = :taxpcent, defgst = :defgst, defn_t = :defn_t where uid = :uid");
		$db->bind(':tax', $tax);
		$db->bind(':description', $desc);
		$db->bind(':taxpcent', $pcent);
		$db->bind(':defgst', $defgst);
		$db->bind(':defn_t', $defn_t);
		$db->bind(':uid', $uid);
		
		$db->execute();
		
		$db->closeDB();
		
		?>
		<script>
		window.open("","hs_setup").jQuery("#gstlist").trigger("reloadGrid");
		this.close();
		</script>
		<?php
		
		}
	
	}

?>


</body>
</html>
