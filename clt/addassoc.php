<?php
session_start();
//ini_set('display_errors', true);

$usersession = $_SESSION['usersession'];
$admindb = $_SESSION['s_admindb'];
date_default_timezone_set($_SESSION['s_timezone']);

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];
$subscriber = $subid;
$sname = $row['uname'];

$memid = $_SESSION['s_memberid'];

$cltdb = $_SESSION['s_cltdb'];

$db->query('select CONCAT_WS(" ",firstname,lastname) as fname from '.$cltdb.'.members where member_id = '.$memid);
$row = $db->single();
extract($row);

$db->closeDB();

?>

<html>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Add Association</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<script src="../includes/jquery/js/jquery.js" type="text/javascript"></script>
<script>

function getassocs() {
	var oname = document.getElementById('lname').value;
	if (oname == '') {
		alert('Please enter at least the first 2 letters of the Surname, then click on Find in Members');
		document.getElementById('lname').focus();
		return false;
	}

	var x=document.getElementById("mem");
	var listlength = document.getElementById("mem").length
	for (var i = 0; i < listlength; i ++) {
		x.remove(x[i]);
	}	

	$.get("includes/ajaxGetMembers.php", {oname: oname}, function(data){
		$("#mem").append(data);																	
	});
}

function newassociate() {
	var assoctype = document.getElementById('association').value;
	var isdependant = document.getElementById('ldependant').value;
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('addnewassociate.php?assoctype='+assoctype+'&isdependant='+isdependant,'addassoc','toolbar=0,scrollbars=1,height=500,width=1000,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	this.close();

}

function stopRKey(evt) {
  var evt = (evt) ? evt : ((event) ? event : null);
  var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
  if ((evt.keyCode == 13) && (node.type=="text"))  {return false;}
}

function post() {
	//add validation here if required.
	var comm = document.getElementById('comm').value;
	var commtype = document.getElementById('comm_type').value;
	var ok = "Y";
	if (comm == "") {
		alert("Please enter details etc.");
		ok = "N";
		return false;
	}

	if (commtype == "0") {
		alert("Please select a communication type.");
		ok = "N"
		return false;
	}

	if (ok == "Y") {
		document.getElementById('savebutton').value = "Y";
		document.getElementById('form1').submit();
	}
}

</script>

<style type="text/css">
<!--
.style1 {
	font-size: large
}
-->
</style>

</head>
<body>
<div id="mwin">
  <form name="form1" id="form1" method="post" >
    <input type="hidden" name="savebutton" id="savebutton" value="N">
    <table width="600" border="0" align="center">
      <tr>
        <td colspan="2"><div align="center" class="style1"><u>Add Association </u></div></td>
      </tr>
      <tr>
        <td colspan="2"><label style="font-size: 18px;"><?php echo $fname; ?></label></td>
      </tr>
      <tr>
        <td class="boxlabel">has the following Association</td>
        <td><select name="association" id="association">
            <option value="Child">Child</option>
            <option value="Parent">Parent</option>
            <option value="Spouse">Spouse</option>
            <option value="Grandchild">Grandchild</option>
            <option value="Grandparent">Grandparent</option>
            <option value="Sibling">Sibling</option>
            <option value="Relative">Relative</option>
            <option value="Company">Company</option>
            <option value="Stakeholder">Stakeholder</option>
            <option value="Associate">Associate</option>
            <option value="Accountant">Accountant</option>
            <option value="Lawyer">Lawyer</option>
            <option value="Account Client">Account Client</option>
            <option value="Legal Client">Legal Client</option>
            <option value="Business Partner">Business Partner</option>
            <option value="Employer">Employer</option>
            <option value="Employee">Employee</option>
            <option value="Contractor">Contractor</option>
            <option value="Contractee">Contractee</option>
            <option value="Guardian">Guardian</option>
            <option value="Ward">Ward</option>
          </select>
          <span class="compulsory">*</span></td>
      </tr>
      <tr>
        <td class="boxlabel"><div align="right">of (Enter Surname)</div></td>
        <td><input name="lname" type="text" id="lname">
          &nbsp;
          <input type="button" name="btnlname" id="btnlname" value="Find in Members" onClick="getassocs(); return false;"></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td><select name="mem" id="mem" size="10">
          </select></td>
      </tr>
      <tr>
        <td>Is this associate a dependant of the member?</td>
        <td><select name="ldependant" id="ldependant">
            <option value="N">No</option>
            <option value="Y">Yes</option>
          </select></td>
      </tr>
      <tr>
        <td colspan="2" class="boxlabel"><input type="submit" value="    Add selected Member as an associate of this Member   " name="save"></td>
      </tr>
      <tr>
        <td colspan="2" class="boxlabel">or &nbsp;
          <input type="button" value="Add a New Member and make an associate of this Member" name="newmem" onClick="newassociate();" ></td>
      </tr>
    </table>
  </form>
</div>

<script>document.onkeypress = stopRKey;</script>

<?php



	if(isset($_POST['save'])) {
		$ok = 'Y';
		if ($_REQUEST['mem'] == 0) {
			echo '<script>';
			echo 'alert("Please enter an associate.")';
			echo '</script>';	
			$ok = 'N';
		}

		if ($ok == 'Y') {	
			$associd = $_REQUEST['mem'];
			$assoctype = $_REQUEST['association'];
			$ldependant = $_REQUEST['ldependant'];
			switch ($assoctype) {
				case 'Child':
				$revassoc = 'Parent';
				break;
				case 'Parent':
				$revassoc = 'Child';
				break;
				case 'Spouse':
				$revassoc = 'Spouse';
				break;
				case 'Grandchild':
				$revassoc = 'Grandparent';
				break;
				case 'Grandparent':
				$revassoc = 'Grandchild';
				break;
				case 'Sibling':
				$revassoc = 'Sibling';
				break;
				case 'Relative':
				$revassoc = 'Relative';
				break;
				case 'Company':
				$revassoc = 'Stakeholder';
				break;
				case 'Stakeholder':
				$revassoc = 'Company';
				break;
				case 'Associate':
				$revassoc = 'Associate';
				break;
				case 'Accountant':
				$revassoc = 'Account Client';
				break;
				case 'Lawyer':
				$revassoc = 'Legal Client';
				break;
				case 'Account Client':
				$revassoc = 'Accountant';
				break;
				case 'Legal Client':
				$revassoc = 'Lawyer';
				break;
				case 'Business Partner':
				$revassoc = 'Business Partner';
				break;
				case 'Employer':
				$revassoc = 'Employee';
				break;
				case 'Employee':
				$revassoc = 'Employer';
				break;
				case 'Contractor':
				$revassoc = 'Contractee';
				break;
				case 'Contractee':
				$revassoc = 'Contractor';
				break;
				case 'Guardian':
				$revassoc = 'Ward';
				break;
				case 'Ward':
				$revassoc = 'Guardian';
				break;
			}
			
			include_once("../includes/DBClass.php");
			$db = new DBClass();

			$db->query("insert into ".$cltdb.".assoc_xref (member_id,association,dependant,of_id) values (:member_id,:association,:dependant,:of_id)");
			$db->bind(':member_id', $associd);
			$db->bind(':association', $revassoc);
			$db->bind(':dependant', 'N');
			$db->bind(':of_id', $associd);
			
			$db->execute();
			
			$db->query("insert into ".$cltdb.".assoc_xref (member_id,association,dependant,of_id) values (:member_id,:association,:dependant,:of_id)");
			$db->bind(':member_id', $memid);
			$db->bind(':association', $assoctype);
			$db->bind(':dependant', 'N');
			$db->bind(':of_id', $associd);
														   
			$db->execute();

			if ($_REQUEST['ldependant'] == 'Y') {
				$db->query("update ".$cltdb.".members set dependant = 'Y' where member_id = ".$associd);
				$db->execute();
			} else {
				$db->query("update ".$cltdb.".members set dependant = 'N' where member_id = ".$associd);
				$db->execute();
			}
			
			$db->execute();

			echo '<script>';
			echo 'window.open("","editmembers").jQuery("#massociationslist").trigger("reloadGrid");';
			echo 'this.close();';
			echo '</script>';
		}
	}

?>

</body>

</html>

