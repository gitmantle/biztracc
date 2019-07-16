<?php
session_start();
$usersession = $_SESSION['usersession'];

//ini_set('display_errors', true);

$sbac = $_REQUEST['sb'];
$br = $_REQUEST['br'];
$accno = $_REQUEST['accno'];
					   
$accgroup = $_SESSION['s_accgroup'];
switch ($accgroup) {
	case 'INC':
		$lower = 1;
		$upper = 80;
		$hed = "Add an Income Account";
		break;
	case 'SIN';
		$lower = 81;
		$upper = 100;
		$hed = "Add a Sundry Income Account";
		break;
	case 'COS';
		$lower = 101;
		$upper = 200;
		$hed = "Add a Cost of Sales Account";
		break;
	case 'EXP';
		$lower = 201;
		$upper = 700;
		$hed = "Add an Expense Account";
		break;
	case 'INV';
		$lower = 701;
		$upper = 750;
		$hed = "Add an Investment Account";
		break;
	case 'BAN';
		$lower = 751;
		$upper = 800;
		$hed = "Add a Bank Account";
		break;
	case 'OAS';
		$lower = 801;
		$upper = 850;
		$hed = "Add an Asset Account";
		break;
	case 'LIB';
		$lower = 851;
		$upper = 900;
		$hed = "Add a Liability Account";
		break;
	case 'EQT';
		$lower = 901;
		$upper = 999;
		$hed = "Add an Equity Account";
		break;
	default:
		echo '<script>';
		echo 'alert("Please select an account group from the left grid before adding an account");';
		echo 'this.close();';
		echo '</script>';
		break;
}


include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];
$uname = $row['uname'];

$findb = $_SESSION['s_findb'];

$db->query('select * from '.$findb.'.branch order by branchname');
$rows = $db->resultset();
// populate branches list
$branch_options = "<option value=\"\">Select Branch</option>";
foreach ($rows as $row) {
	extract($row);
	$branch_options .= "<option value=\"".$branch."\">".$branchname."</option>";
}

$db->closeDB();

$theme = $_SESSION['deftheme'];
require_once("../includes/backgrounds.php");


?>

<html>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Add an Account</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<script src="../includes/jquery/js/jquery.js" type="text/javascript"></script>

<script type="text/javascript">
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
	
	function post() {
		//add validation here if required.
		var acn = document.getElementById('acname').value;
		var ac = document.getElementById('acno').value;
		var br = document.getElementById('branch').value;
		var blk = document.getElementById('block').value;
		var gst = document.getElementById('gstapply').value;
		var sbac = "<?php echo $sbac; ?>";
		if (sbac == 'N') {
			var sb = 0;
		} else {
			var sb = document.getElementById('subac').value;
		}
		var ok = "Y";
		if (acn == '') {
			alert("Please enter an Account Name");
			ok = "N";
			return false;
		}

		if (br == '') {
			alert("Please select a Branch");
			ok = "N";
			return false;
		}
		
		if (sb == '' && sbac == 'Y') {
			alert("Please enter a Sub Account");
			ok = "N";
			return false;
		}
		
		if (ok == "Y") {
			$.get("includes/ajaxCheckAcc.php", {ac: ac, br: br, sb: sb}, function(data){
				var chk = data;
				if (chk == "duplicate") {
					alert("This account already in use");
				} else {
					document.getElementById('savebutton').value = "Y";
					document.getElementById('addgl').submit();
				}
			});		
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

<form name="addgl" id="addgl" method="post" >
 	<input type="hidden" name="savebutton" id="savebutton" value="N">
<br>
  <table width="900" border="0" align="center">
    <tr>
      <td colspan="3"><div align="center" class="style1 style3"><u><?php echo $hed; ?></u></div></td>
    </tr>
    <tr>
      <td class="boxlabel" width="237">Account Name</td>
      <td width="292"><input name="acname" type="text" id="acname"  size="45" maxlength="45" ></td>
      <td width="357">&nbsp;</td>
    </tr>
    <tr>
      <td class="boxlabel">Account Number</td>
      <?php
      if ($sbac == 'Y') {
     	echo '<td class="boxlabelleft"><input name="acno" type="text" id="acno" value="'.$accno.'" size="5" maxlength="5" readonly> ';
	  } else {	  
	    echo '<td class="boxlabelleft"><input name="acno" type="text" id="acno" value="0" size="5" maxlength="5" onchange="checkacc('.$lower.','.$upper.')">&nbsp;*in the range</span>&nbsp;'.$lower.'&nbsp;to</span>&nbsp'.$upper.'</td>';
	  }
	  ?>
      <td class="boxlabelleft">&nbsp;</td>
    </tr>
    <tr>
    	<?php
		if ($br == 'N') {
			echo '<td class="boxlabel">Branch</td>';
			echo '<td><select name="branch" id="branch">'.$branch_options.'</select></td>';
			echo '<td>&nbsp;</td>';
		} else {
			echo '<td class="boxlabel">Branch</td>';
			echo '<td class="boxlabelleft"><input type="text" id="branch" name="branch" size="25" value="'.$br.'" readonly></td>';
			echo '<td>&nbsp;</td>';
		}
		?>
	</tr>
    	<?php
		if ($sbac == 'Y') {
			echo '<tr>';
			echo '<td class="boxlabel">Sub Account (1-99)</td>';
			echo '<td><input name="subac" type="text" id="subac" value="0" size="5" maxlength="2"></td>';
			echo '<td>&nbsp;</td>';
			echo '</tr>';
		}
		?>
    <tr>
      <td class="boxlabel"><div align="right">Does  <?php echo $_SESSION['s_tradtax']; ?> apply to this account? </div></td>
      <td><table width="200">
        <tr>
          <td class="boxlabelleft"><input type="radio" name="gstapply" id="gstapply" value="N" checked>No</td>
        <td class="boxlabelleft"><input type="radio" name="gstapply" id="gstapply" value="Y">Yes</td>
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
      	echo '<td class="boxlabel"><div align="right">Reconcile this account?</div></td>';
		echo '<td><table width="200">';
		echo '<tr>';
		echo '<td><label>';
		echo '<input type="radio" name="recon" value="N">No</label></td>';
		echo '<td><input type="radio" name="recon" value="Y">Yes</td>';
		echo '</tr>';
		echo '</table></td>';   		
		echo '</tr>';
	}
	?>		
    <tr>
      <td class="boxlabel"><div align="right">Display details to users &gt;= user group </div></td>
      <td><select name="sec">
        <option value="20">20</option>
        <option value="19">19</option>
        <option value="18">18</option>
        <option value="17">17</option>
        <option value="16">16</option>
        <option value="15">15</option>
        <option value="14">14</option>
        <option value="13">13</option>
        <option value="12">12</option>
        <option value="11">11</option>
        <option value="10">10</option>
        <option value="9">9</option>
        <option value="8">8</option>
        <option value="7">7</option>
        <option value="6">6</option>
        <option value="5">5</option>
        <option value="4">4</option>
        <option value="3">3</option>
        <option value="2">2</option>
        <option value="1">1</option>
      </select></td>
      <td>Users in groups lower than the selected will not be able to get transaction details for this account.</td>
    </tr>
    <tr>
      <td class="boxlabel"><div align="right">Block this account? </div></td>
      <td><table width="200">
        <tr>
          <td class="boxlabelleft"><input type="radio" name="block" id="block"value="N" checked>No</td>
        <td class="boxlabelleft"><input type="radio" name="block" id="block" value="Y">Yes</td>
        </tr>
      </table></td>
      <td>Block this account if you do not want transactions posted to it.</td>
    </tr>
    <tr>
      <td class="boxlabel"><div align="right">Last Year's Balance (- if credit) </div></td>
      <td><input type="text" name="lastyr" value="0"></td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td align="right"><input type="button" value="Save" name="save" onclick="post()"></td>
      <td align="right">&nbsp;</td>
    </tr>
  </table>
</form>


<script>
document.forms[0].acname.focus();
</script>

</div>

<?php
	if(isset($_REQUEST['savebutton']) && $_REQUEST['savebutton'] == "Y") {
		
		include_once("../includes/DBClass.php");
		$db = new DBClass();
		
		include_once("includes/accaddacc.php");
		$oGL = new accaddacc;

		$oGL->coy = $coyid;
		
		if ($_REQUEST['subac'] == 0) {
			$oGL->aname = strtoupper(trim($_REQUEST['acname']));
		} else {
			$oGL->aname = ucwords(trim($_REQUEST['acname']));
		}
		$oGL->aaccno = $_REQUEST['acno'];
		$oGL->grp = $accgroup;
		$oGL->alastyear = $_REQUEST['lastyr'];
		$oGL->ablocked = $_REQUEST['block'];
		$oGL->apaygst = $_REQUEST['gstapply'];
		$oGL->asc = $_REQUEST['sec'];
		if ($br == 'N') {
			$oGL->abranch = $_REQUEST['branch'];
		} else {
			$oGL->abranch = $br;
		}
		if ($sbac == 'Y') {
			$oGL->asub = $_REQUEST['subac'];
		} else {
			$oGL->asub = 0;
		}
		if (isset($_REQUEST['recon'])) {
			$oGL->arecon = $_REQUEST['recon'];
		}
										
		$oGL->AddGL();
		
		$db->closeDB();
		
		?>
			<script>
			window.open("","updtglaccs").jQuery("#glacclist").trigger("reloadGrid");
			this.close();
			</script>
		<?php

	}

?>


</body>
</html>
