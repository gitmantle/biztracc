<?php
session_start();

//ini_set('display_errors', true);

$tid = $_REQUEST['uid'];
$coyidno = $_SESSION['s_coyid'];
$findb = $_SESSION['s_findb'];
$cltdb = $_SESSION['s_cltdb'];

$theme = $_SESSION['deftheme'];
require_once("../includes/backgrounds.php");

?>

<html>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Add a Creditor Sub Account</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">

<script type="text/javascript">
	function required()	{
		var aname = document.getElementById('acname').value;
		if (aname==null||aname=="") {
			alert('Account name required');
			document.getElementById('acname').focus();
		}
	}
	function checkacc() {
		var acc = document.getElementById('acno').value;
		var lower = '<?php echo $lower; ?>';
		var upper = '<?php echo $upper; ?>';

		acc = parseInt(acc);
		if (isNaN(acc)) {
			alert('Please enter a valid account number');
			document.getElementById('acno').focus();
			return;
		}
		lower = parseInt(lower);
		upper = parseInt(upper);
		if (acc < lower || acc > upper) {
			alert('Account number outside permissible range');
			document.getElementById('acno').focus();
		}
	}

</script>


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
      <td colspan="2"><div align="center" class="style1 style3"><u>Add a Creditor Sub Account</u></div></td>
    </tr>
    <tr>
      <td class="boxlabel" >Sub Account Name</td>
      <td ><input name="acname" type="text" id="acname"  size="45" maxlength="45" onchange="return required()">&nbsp;<span class="style2">*</span></td>
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

		$db->query("select client_id,crno from ".$cltdb.".client_company_xref where uid = ".$tid);
		$row = $db->single();
		extract($row);
		
		$db->query("select max(crsub) as lastsub from ".$cltdb.".client_company_xref where crno = ".$crno);
		$row = $db->single();
		if (count($row == 0)) {
			$subno = 1;
		} else {
			extract($row);
			$subno = $lastsub + 1;
		}
		
		$db->query("select lastname from ".$cltdb.".members where member_id = ".$client_id);
		$row = $db->single();
		extract($row);
			
		$db->query("insert into ".$cltdb.".client_company_xref (client_id,company_id,crno,crsub,subname,sortcode) values (:client_id,:company_id,:crno,:crsub,:subname,:sortcode)");
		$db->bind(':client_id', $client_id);
		$db->bind(':company_id', $coyidno);
		$db->bind(':crno', $crno);
		$db->bind(':crsub', $subno);
		$db->bind(':subname', $sname);
		$db->bind(':sortcode', $lastname.$crno."-".$subno);
		
		$db->execute();
		$db->closeDB();
	
		?>
			<script>
			window.open("","ad_updtcr").jQuery("#crlist").trigger("reloadGrid");
			this.close();
			</script>
		<?php
	
		}	
	}

?>

</body>
</html>
