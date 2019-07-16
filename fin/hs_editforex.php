<?php
session_start();
//ini_set('display_errors', true);

$usersession = $_SESSION['usersession'];
$id = $_REQUEST['uid'];
$findb = $_SESSION['s_findb'];

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from ".$findb.".forex where uid = ".$id);
$row = $db->single();
extract($row);

// populate symbol list
    $arr = array('$', '£', '€', 'R', '¥');
	$symbol_options = '';
    for($i = 0; $i < count($arr); $i++)	{
		if ($arr[$i] == $symbol) {
			$selected = 'selected="selected"';
		} else {
			$selected = '';
		}	
		$symbol_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
 	}
	
// populate local list
    $arr = array('No', 'Yes');
	$local_options = '';
    for($i = 0; $i < count($arr); $i++)	{
		if ($arr[$i] == $def_forex) {
			$selected = 'selected="selected"';
		} else {
			$selected = '';
		}	
		$local_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
 	}	

$db->closeDB();

?>

<html>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Edit Forex</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">

<style type="text/css">
<!--
.style1 {font-size: large}
-->
</style>

<script>

function post() {

	//add validation here if required.
	var code = document.getElementById('code').value;
	var ok = "Y";
	if (code == "") {
		alert("Please enter a currency code.");
		ok = "N";
		return false;
	}
	
	if (ok == "Y") {
		document.getElementById('savebutton').value = "Y";
		document.getElementById('fx').submit();
	}
	
	
}


</script>


</head>


<body>
<div id="mwin">
<form name="fx" id="fx" method="post" >
 	<input type="hidden" name="savebutton" id="savebutton" value="N">

  <table width="590" border="0" align="center">
    <tr>
      <td colspan="2"><div align="center" class="style1"><u>Edit a Currency </u></div></td>
    </tr>
    <tr>
      <td class="boxlabel">3 letter Currency Code</td>
      <td><input name="code" type="text" id="code"  size="5" maxlength="3" value="<?php echo $currency; ?>" ></td>
    </tr>
	<tr>
	<td class="boxlabel">Description</td>
	<td><input type="text" name="desc" id="desc" value="<?php echo $descript; ?>" ></td>
	</tr>	
    <tr>
      <td class="boxlabel">Symbol</td>
      <td><select name="symbol" id="symbol"><?php echo $symbol_options;?></select></td>
    </tr>
    <tr>
      <td class="boxlabel">Exchange Rate</td>
      <td><input type="text" name="rate" id="rate"  value="<?php echo $rate; ?>" > </td>
    </tr>
    <tr>
      <td class="boxlabel">Is this the Local Currency</td>
      <td><select name="def_fx" id="def_fx">
		<?php echo $local_options; ?>
      </select></td>
    </tr>    <tr>
      <td>&nbsp;</td>
      <td align="right"><input type="button" value="Save" name="save" id="save" onclick="post()"></td>
    </tr>
  </table>
</form>
</div>

<?php

	if(isset($_REQUEST['savebutton']) && $_REQUEST['savebutton'] == "Y") {
	
		$code = strtoupper($_REQUEST['code']);
		$desc = $_REQUEST['desc'];
		$rate = $_REQUEST['rate'];
		$def_fx = $_REQUEST['def_fx'];
		$symbol = $_REQUEST['symbol'];
		
		include_once("../includes/DBClass.php");
		$db = new DBClass();
		
		if ($def_fx == 'Yes') {
			$db->query("update ".$findb.".forex set def_forex = 'No'");
			$db->execute();
		}
		
		$db->query("update ".$findb.".forex set currency = :currency, rate = :rate, descript = :descript, symbol = :symbol, def_forex = :def_forex where uid = :uid");
		$db->bind(':currency', $code);
		$db->bind(':rate', $rate);
		$db->bind(':descript', $desc);
		$db->bind(':symbol', $symbol);
		$db->bind(':def_forex', $def_fx);
		$db->bind(':uid', $id);
		
		$db->execute();
		
		$db->closeDB();
	
		?>
		<script>
		window.open("","hsforex").jQuery("#forexlist").trigger("reloadGrid");
		this.close();
		</script>
		<?php
		
	}

?>


</body>
</html>
