<?php
session_start();
$findb = $_SESSION['s_findb'];

//ini_set('display_errors', true);

require_once("../includes/backgrounds.php");
$theme = $_SESSION['deftheme'];

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Add Percentage Markup</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<link type="text/css" href="../includes/jquery/themes/<?php echo $theme; ?>/jquery.ui.all.css" rel="stylesheet" />


<style type="text/css">
<!--
.style1 {font-size: large}
-->
</style>
</head>


<body>
<div id="swin">
<form name="form1" method="post" >
  <table width="460" border="0" align="center">
    <tr>
      <td colspan="2"><div align="center" class="style1"><u>Add Price Band</u></div></td>
    </tr>
    <tr>
      <td class="boxlabel">Price Band Description</td>
      <td><input name="pcentname" type="text" id="pcentname"  size="40" maxlength="40"></td>
      </tr>
    <tr>
      <td class="boxlabel">Percentage Markup on Cost</td>
      <td><input type="text" name="pcent" id="pcent"></td>
    </tr>
	<tr>
	  <td class="boxlabel">Set Price</td>
	  <td ><input type="text" name="setprice" id="setprice"></td>
	  </tr>
	<tr>
	  <td>&nbsp;</td>
	  <td align="right"><input type="submit" value="Save" name="save" ></td>
	  </tr>
  </table>
  
 <script>
	document.getElementById('pcentname').focus();
 </script>
  
  
</form>
</div>

<?php
	if(isset($_POST['save'])) {
		
		if ($_REQUEST['pcentname'] == '') {
			echo '<script>';
			echo 'alert("Please enter a price band name.")';
			echo '</script>';	
		} else {	
		
			$bandname = ucwords(trim($_REQUEST['pcentname']));
			$pcentage = $_REQUEST['pcent'];
			$setprice = $_REQUEST['setprice'];
			
			include_once("../includes/DBClass.php");
			$db = new DBClass();
			
			$db->query("insert into ".$findb.".stkpricepcent (priceband,pcent,setprice) values (:priceband,:pcent,:setprice)");
			$db->bind(':priceband', $bandname);
			$db->bind(':pcent', $pcentage);
			$db->bind(':setprice', $setprice);
			$db->execute();
			$db->closeDB();
	
			?>
				<script>
				window.open("","updtpcents").jQuery("#stkpcentlist").trigger("reloadGrid");
				this.close()
				</script>
			<?php
		}
	
	}
?>
 

</body>
</html>

