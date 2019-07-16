<?php
session_start();

//ini_set('display_errors', true);

$accgroup = $_SESSION['s_accgroup'];
switch ($accgroup) {
	case 'INC':
		$lower = 1;
		break;
	case 'SIN';
		$lower = 81;
		break;
	case 'COS';
		$lower = 101;
		break;
	case 'EXP';
		$lower = 201;
		break;
	case 'INV';
		$lower = 701;
		break;
	case 'BAN';
		$lower = 751;
		break;
	case 'OAS';
		$lower = 801;
		break;
	case 'LIB';
		$lower = 851;
		break;
	case 'EQU';
		$lower = 901;
		break;
}

$uid = $_REQUEST['uid'];

include_once("../includes/DBClass.php");
$db = new DBClass();

$findb = $_SESSION['s_findb'];

$db->query("select * from ".$findb.".glmast where uid = ".$uid);
$row = $db->single();
extract($row);
$br = $branch;

// build menu group list
$group_options = '';
for ($n = 0; $n < 21; $n = $n+1) {
	if ($n == $sc) {
		$group_options .= "<option value=\"".$n."\"selected=\"selected\">".$n."</option>";
	} else {
		$group_options .= "<option value=\"".$n."\">".$n."</option>";
	}
}

$db->query('select branchname from '.$findb.'.branch where branch =  "'.$br.'"');
$row = $db->single();
extract($row);

$db->closeDB();

?>


<html>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Add an Account</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">

<script type="text/javascript">
	function required()	{
		var aname = document.getElementById('acname').value;
		if (aname==null||aname=="") {
			alert('Account name required');
			document.getElementById('acname').focus();
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

<div id="bwin">

<form name="form1" method="post" >
<br>
  <table width="900" border="0" align="center">
    <tr>
      <td colspan="3"><div align="center" class="style1"><u>Edit an Account </u></div></td>
    </tr>
    <tr>
      <td width="242" class="boxlabel"><div align="right">Account name</div></td>
      <td width="304"><input name="acname" type="text" id="acname"  value="<?php echo $account; ?>" size="45" maxlength="45" onchange="return required()">&nbsp;<span class="style2">*</span></td>
      <td width="340">&nbsp;</td>
    </tr>
    <tr>
      <td class="boxlabel"><div align="right">Account Number </div></td>
      <td class="boxlabelleft"><?php echo $accountno; ?></td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td class="boxlabel"><div align="right">Branch </div></td>
      <td class="boxlabelleft"><?php echo $branchname; ?></td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td class="boxlabel"><div align="right">Sub Account </div></td>
      <td class="boxlabelleft"><?php echo $sub; ?></td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td class="boxlabel"><div align="right">Does  <?php echo $_SESSION['s_tradtax']; ?> apply to this account? </div></td>
      <td><table width="200">
        <tr>
	<?php
		if ($paygst == 'N') {
          	echo '<td class="boxlabelleft"><input type="radio" name="gstapply" value="N" checked>No</td>';
        	echo '<td class="boxlabelleft"><input type="radio" name="gstapply" value="Y">Yes</td>';
		} else {
          	echo '<td class="boxlabelleft"><input type="radio" name="gstapply" value="N">No</td>';
        	echo '<td class="boxlabelleft"><input type="radio" name="gstapply" value="Y" checked>Yes</td>';
		}
	?>
        </tr>
      </table></td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td class="boxlabel">&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>	
	<?php
	if ($lower == 751) {
    echo '<tr>';
      echo '<td class="boxlabel"><div align="right">Reconcile this account? </div></td>';
      echo '<td><table width="200">';
        echo '<tr>';	
		if ($recon == 'N') {
          	echo '<td class="boxlabelleft"><input type="radio" name="recon" value="N" checked>No</td>';
        	echo '<td class="boxlabelleft"><input type="radio" name="recon" value="Y">Yes</td>';
		} else {
          	echo '<td class="boxlabelleft"><input type="radio" name="recon" value="N">No</td>';
        	echo '<td class="boxlabelleft"><input type="radio" name="recon" value="Y" checked>Yes</td>';
		}	
        echo '</tr>';
      echo '</table></td>';
    echo '</tr>';		
	}
	?>		
    <tr>
      <td class="boxlabel"><div align="right">Display details to users &gt;= user group </div></td>
      <td><select name="sec">
           <?php echo $group_options; ?>
      </select></td>
      <td>Users in groups lower than the selected will not be able to get transaction details for this account.</td>
    </tr>
    <tr>
      <td class="boxlabel"><div align="right">Block this account? </div></td>
      <td><table width="200">
        <tr>
	<?php
		if ($blocked == 'N') {
          	echo '<td class="boxlabelleft"><input type="radio" name="block" value="N" checked>No</td>';
        	echo '<td class="boxlabelleft"><input type="radio" name="block" value="Y">Yes</td>';
		} else {
          	echo '<td class="boxlabelleft"><input type="radio" name="block" value="N">No</td>';
        	echo '<td class="boxlabelleft"><input type="radio" name="block" value="Y" checked>Yes</td>';
		}
	?>
        </tr>
      </table></td>
      <td>Block this account if you do not want transactions posted to it.</td>
    </tr>
    <tr>
      <td class="boxlabel"><div align="right">Last Year's Balance (- if credit) </div></td>
      <td><input type="text" name="lastyr" value="<?php echo $lastyear; ?>"></td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td align="right"><input type="submit" value="Save" name="save" id="save" ></td>
      <td align="right">&nbsp;</td>
    </tr>
  </table>
</form>


<script>
document.forms[0].acname.focus();
</script>

</div>

<?php
	if(isset($_POST['save'])) {
		
		
		include_once("includes/accaddacc.php");
		$oGL = new accaddacc;
		
		$oGL->uid = $uid;
		if ($sub == 0) {
			$oGL->aname = strtoupper($_REQUEST['acname']);
		} else {
			$oGL->aname = ucwords($_REQUEST['acname']);
		}
		$oGL->alastyear = $_REQUEST['lastyr'];
		$oGL->ablocked = $_REQUEST['block'];
		$oGL->apaygst = $_REQUEST['gstapply'];
		$oGL->asc = $_REQUEST['sec'];

		if (isset($_REQUEST['recon'])) {
			$oGL->arecon = $_REQUEST['recon'];
		}
										
		$oGL->EditGL();
		
?>
	<script>
	window.open("","updtglaccs").jQuery("#glacclist").trigger("reloadGrid");
	this.close()
	</script>
<?php
	

	}

?>
 


</body>
</html>
