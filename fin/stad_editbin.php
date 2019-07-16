<?php
session_start();
$coyidno = $_SESSION['s_coyid'];

$findb = $_SESSION['s_findb'];

$binid = $_REQUEST['binid'];
$b = explode('~',$binid);
$lid = $b[0];
$bid = $b[1];
$ic = $b[2];

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query('select * from '.$findb.'.stkbins where itemcode = "'.$ic.'" and locid = '.$lid);
$row = $db->single();
if (!empty($row)) {
	extract($row);
} else {
	$itemcode = $ic;
	$row = "";
	$shelf = "";
	$bin = "";
	$db->query('insert into '.$findb.'.stkbins (itemcode,locid) values ("'.$ic.'",'.$lid.')');
	$db->execute();
}

$db->closeDB();

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Edit Storage Location</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">

<style type="text/css">
<!--
.style1 {font-size: large}
-->
</style>
</head>


<body>
<div id="swin">
<form name="form1" method="post" >
  <table width="490" border="0" align="center">
    <tr>
      <td colspan="2"><div align="center" class="style1"><u>Edit Storage Location </u></div></td>
    </tr>
    <tr>
      <td class="boxlabel">Item Code</td>
      <td><input type="text" name="tcode" id="tcode" value="<?php echo $itemcode; ?>" readonly></td>
      </tr>
    <tr>
      <td class="boxlabel">Row</td>
      <td><input type="text" name="trow" id="trow" value="<?php echo $row; ?>"></td>
    </tr>
    <tr>
      <td class="boxlabel">Shelf</td>
      <td><input type="text" name="tshelf" id="tshelf" value="<?php echo $shelf; ?>"></td>
    </tr>
    <tr>
      <td class="boxlabel">Bin</td>
      <td><input type="text" name="tbin" id="tbin" value="<?php echo $bin; ?>"></td>
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
		

			$row = $_REQUEST['trow'];
			$shelf = $_REQUEST['tshelf'];
			$bin = $_REQUEST['tbin'];
			
			include_once("../includes/DBClass.php");
			$dbb = new DBClass();

			$dbb->query("update ".$findb.".stkbins set row = '".$row."', shelf = '".$shelf."', bin = '".$bin."' where itemcode = '".$ic."' and locid = ".$lid);
			$dbb->execute();
			$dbb->closeDB();
			
	
			?>
				<script>
				window.open("","strep_stklocqty").jQuery("#stklocqtylist").trigger("reloadGrid");
				this.close()
				</script>
			<?php
	
	}
?>
 

</body>
</html>

