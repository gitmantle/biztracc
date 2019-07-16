<?php
session_start();

//ini_set('display_errors', true);

$mid = $_SESSION['s_memberid'];
$coyidno = $_SESSION['s_coyid'];
$sno = $_REQUEST['sno'];					   

include_once("../includes/DBClass.php");
$db = new DBClass();

$cltdb = $_SESSION['s_cltdb'];

$db->query("select subname from ".$cltdb.".client_company_xref where client_id = ".$mid." and drsub = ".$sno);
$row = $db->single();
extract($row);
$sname = $subname;

$db->closeDB();

$theme = $_SESSION['deftheme'];
require_once("../includes/backgrounds.php");


?>


<html>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Add a Debtor Sub Account</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">


<style type="text/css">
<!--
.style1 {font-size: large}
.style2 {color: #FF0000}
-->
</style>
</head>


<body>

<div id="swin">

<form name="form1" method="post" >
<br>
  <table width="600" border="0" align="center">
    <tr>
      <td colspan="2"><div align="center" class="style1 style3"><u>Edit a Debtor Sub Account</u></div></td>
    </tr>
    <tr>
      <td class="boxlabel" >Sub Account Name</td>
      <td ><input name="acname" type="text" id="acname"  size="45" maxlength="45" value="<?php echo $sname; ?>"></td>
    </tr>
    <tr>
    <td>&nbsp;</td>
      <td><input type="submit" value="Save" name="save" ></td>
    </tr>
  </table>
</form>


<script>
document.forms[0].acname.focus();
</script>

</div>

<?php
	if(isset($_POST['save'])) {
		$ok = 'Y';
		
		if ($_REQUEST['acname'] == '') {
			echo '<script>';
			echo 'alert("Please enter a sub account.")';
			echo '</script>';	
			$ok = 'N';
		}

	if ($ok == 'Y') {
		$sname = $_REQUEST['acname'];
		
		include_once("../includes/DBClass.php");
		$db = new DBClass();
		
		$db->query("update ".$cltdb.".client_company_xref set subname = '".$sname."' where client_id = ".$mid." and drsub = ".$sno);
		$db->execute();
		$db->closeDB();
		
		?>
			<script>
			window.open("","ad_updtdr").jQuery("#drlist").trigger("reloadGrid");
			this.close();
			</script>
		<?php
	
		}	

	}

?>
 


</body>
</html>
