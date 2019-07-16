<?php
session_start();

$sessionid = session_id();

error_reporting (E_ALL ^ E_NOTICE ^ E_WARNING);

//$_SESSION['s_server'] = "vmcp13.digitalpacific.com.au";
$_SESSION['s_server'] = "localhost";
$_SESSION['s_admindb'] = "infinint_infin8";
$_SESSION['s_prcdb'] = "infinint_med42_22";
$_SESSION['s_findb'] = "infinint_fin42_22";
$_SESSION['s_cltdb'] = "infinint_sub42";

include_once("../includes/DBClass.php");
$db = new DBClass();

$prcdb = $_SESSION['s_prcdb'];
$findb = $_SESSION['s_findb'];
$cltdb = $_SESSION['s_cltdb'];

// populate depot drop down
$db->query("select * from ".$prcdb.".depots");
$rows = $db->resultset();
$depot_options = "<option value=\"0\">Select Depot</option>";
foreach ($rows as $row) {
	extract($row);
	if ($depot_id == $mdepot) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}
	$depot_options .= '<option value="'.$depot_id.'"'.$selected.'>'.$depot.', '.$stown.'</option>';
}
	
// populate generic list
    $arr = array('No', 'Yes');
	$generic_options = '';
    for($i = 0; $i < count($arr); $i++)	{
		$selected = '';
		$generic_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
 	}

// populate title list
    $arr = array('Mr', 'Mrs', 'Ms', 'Miss', 'Master', 'Dr', 'Professor', 'Reverend');
	$mtitle_options = '';
    for($i = 0; $i < count($arr); $i++)	{
		$mtitle_options .= '<option value="'.$arr[$i].'">'.$arr[$i].'</option>';
 	}

// populate day list
    $arr = array('01','02','03','04','05','06','07','08','09','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24','25','26','27','28','29','30','31');
	$mday_options = "<option value=\"00\">00</option>";
    for($i = 0; $i < count($arr); $i++)	{
		$mday_options .= '<option value="'.$arr[$i].'">'.$arr[$i].'</option>';
 	}

// populate month list
    $arr = array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12');
	$mmonth_options = "<option value=\"00\">00</option>";
    for($i = 0; $i < count($arr); $i++)	{
		$mmonth_options .= '<option value="'.$arr[$i].'">'.$arr[$i].'</option>';
 	}

// populate year list
	$thisyear = date("Y");
	$myear_options = "";
    for($i = $thisyear - 120; $i < $thisyear - 50; $i++)	{
		$myear_options .= '<option value="'.$i.'">'.$i.'</option>';
 	}
	$myear_options .= "<option value=\"0000\" selected>0000</option>";
    for($i = $thisyear - 50; $i < $thisyear + 1; $i++)	{
		$myear_options .= '<option value="'.$i.'">'.$i.'</option>';
 	}

// populate gender list
    $arr = array('Male', 'Female');
	$mgender_options = '';
    for($i = 0; $i < count($arr); $i++)	{
		$mgender_options .= '<option value="'.$arr[$i].'" >'.$arr[$i].'</option>';
 	}

$thisyear = date('Y');

$rectable = 'ztmp_reqmed';
$_SESSION['s_rectable'] = $rectable;


$db->query("create table if not exists ".$prcdb.".".$rectable." (uid integer(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, guid varchar(50), itemid int(11), item varchar(100), dosage varchar(8), qty smallint(4) default 0, cost decimal(16,2) default 0, monthqty smallint(4), totcost decimal(16,2) default 0, entered date) engine myisam"); 
$db->execute();

$weekago = date('Y-m-d', strtotime('-7 days'));
$db->query("delete from ".$prcdb.".".$rectable." where entered < '".$weekago."'");
$db->execute();

$db->closeDB();

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Online Application</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<link type="text/css" href="../includes/jquery/themes/cupertino/jquery.ui.all.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="../includes/jquery/themes/ui.jqgrid.css" />
<script src="../includes/jquery/js/jquery.js" type="text/javascript"></script>
<script src="../includes/jquery/js/i18n/grid.locale-en.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery-ui-custom.min.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery.jqGrid.min.js" type="text/javascript"></script>
<script type="text/javascript" src="../includes/jquery/ui/jquery.ui.widget.js"></script>

<script>

window.name = 'onlineapp';

function calcagem() {
	var d = new Date();
	var curr_day = d.getDate();
	var curr_month = d.getMonth()+1;
	var curr_year = d.getFullYear();
	var birthYear = document.getElementById('myear').value;
	var birthMonth = document.getElementById('mdobmonth').value;
	var birthDay = document.getElementById('mdobday').value;
	var age = curr_year - birthYear;
	if (curr_month < birthMonth || (curr_month == birthMonth && curr_day < birthDay)) {
		age = age - 1;
	}
	
	document.getElementById('mage').value = age;
}

function checkage(from) {
	var yr = document.getElementById('mage').value;
	if (isNaN(yr)) {
		alert('Age must be numeric');
		return false;
	}
	var d = new Date();
	var thisyear = d.getFullYear();
	var dobyr = document.getElementById('myear').value;
	if (dobyr == '0000') {
		var byear = thisyear - yr;
		document.getElementById('myear').value = byear;
	} else {
		var yearentered = document.getElementById('myear').value;
		if ((thisyear - yr) != yearentered) {
			alert('Age and Year of Birth do not correlate')
			calcagem();
		}
	}
}


function sameadd() {
	document.getElementById('pad1').value = document.getElementById('sad1').value;
	document.getElementById('pad2').value = document.getElementById('sad2').value;
	document.getElementById('ptown').value = document.getElementById('stown').value;
	document.getElementById('ppostcode').value = document.getElementById('spostcode').value;
	document.getElementById('pcountry').value = document.getElementById('scountry').value;
}

function addreq() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +375;
	var generic = document.getElementById('mgeneric').value;
	window.open('addreq.php?generic='+generic,'addreq','toolbar=0,scrollbars=1,height=200,width=600,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}  

function delmed(id) {
	  if (confirm("Are you sure you want to delete this line")) {
		var qtable = document.getElementById('qtable').value;  
		$.get("ajax/ajaxdelmed.php", {tid: id, qtable: qtable}, function(data){$("#reqlist").trigger("reloadGrid")});
	  }
}

function post() {

	//add validation here if required.
	var fn = document.getElementById('fname').value;
	var ln = document.getElementById('lname').value;
	var a1 = document.getElementById('sad1').value;
	var tn = document.getElementById('stown').value;

	var ok = "Y";
	if (fn == '') {
		alert("Please enter a first name.");
		ok = "N";
		return false;
	}
	if (ln == '') {
		alert("Please enter a last name.");
		ok = "N";
		return false;
	}
	if (a1 == '') {
		alert("Please enter a first line of address.");
		ok = "N";
		return false;
	}
	if (tn == '') {
		alert("Please enter a town.");
		ok = "N";
		return false;
	}
	
	if (ok == "Y") {
		document.getElementById('savebutton').value = "Y";
		document.getElementById('onapp').submit();
	}
	
}	 

</script>

</head>

<body>
<form name="onapp" id="onapp" method="post">
 <input type="hidden" name="savebutton" id="savebutton" value="N">
 <input type="hidden" name="qtable" id="qtable" value="<?php echo $rectable; ?>">
<div id="wrapper" >

    <div id="mainheader">
        <div id="mainlefttop"><img src="../images/cmed4u_large.png" width="700" height="60"></div>
    </div>
    
 <div id="details" style="float:left;visibility:visible;height:360px;width:1000px;background-image:url('../images/back.jpg');">
 	<table width="900" align="center">
    	<tr>
        	<td colspan="9" align="center"><h2>Online application for Membership of Cmeds4U Services</h2></td>
        </tr>
        <tr>
            <td colspan="9" ><hr size="4" width="880" align="center" />  </td>
        </tr>
        <tr>
        	<td style="font-size:14px">Firstname  </td>
        	<td colspan="4"><input type="text" name="fname" id="fname" /></td>
        	<td><span style="font-size:14px">Lastname</span></td>
        	<td><input type="text" name="lname" id="lname" /></td>
        	<td style="font-size:14px">Title</td>
        	<td><select name="mtitle" id="mtitle" tabindex="1">
        	  <?php echo $mtitle_options;?>
      	  </select></td>
        </tr>
        <tr>
          <td style="font-size:14px">Date of Birth</td>
          <td colspan="6"  style="font-size:14px">
            dd&nbsp;
            <select name="mdobday" id="mdobday" tabindex="6">
              <?php echo $mday_options;?>
            </select>
            &nbsp;mm
            <select name="mdobmonth" id="mdobmonth" tabindex="7">
              <?php echo $mmonth_options;?>
            </select>
            &nbsp;yyyy
            <select name="myear" id="myear" tabindex="8" onchange="calcagem()">
              <?php echo $myear_options;?>
            </select>
            &nbsp;Age
            <input name="mage" type="text" id="mage" size="3" maxlength="3" value="0" onchange="checkage()" />
          </td>
          <td  style="font-size:14px">Gender</td>
          <td  style="font-size:14px"><select name="mgender" id="mgender">
            <?php echo $mgender_options;?>
          </select></td>
        </tr>
        <tr>
          <td style="font-size:14px">Address</td>
          <td colspan="6" style="font-size:14px">Delivery - Street</td>
          <td colspan="2" style="font-size:14px">Postal - PO Box</td>
        </tr>
        <tr>
          <td style="font-size:12px">Line 1</td>
          <td colspan="6"><input type="text" name="sad1" id="sad1" size="50"/></td>
          <td colspan="2"><input type="text" name="pad1" id="pad1" size="50" placeholder="PO Box"/></td>
        </tr>
        <tr>
          <td style="font-size:12px">Line 2</td>
          <td colspan="6"><input type="text" name="sad2" id="sad2" size="50"/></td>
          <td colspan="2"><input type="text" name="pad2" id="pad2" size="50"/></td>
        </tr>
        <tr>
          <td style="font-size:12px">Town</td>
          <td colspan="6"><input type="text" name="stown" id="stown" size="50"/></td>
          <td colspan="2"><input type="text" name="ptown" id="ptown" size="50"/></td>
        </tr>
        <tr>
          <td style="font-size:12px">Postcode</td>
          <td colspan="6"><input type="text" name="spostcode" id="spostcode" size="25"/></td>
          <td colspan="2"><input type="text" name="ppostcode" id="ppostcode" size="25"/></td>
        </tr>
        <tr>
          <td style="font-size:12px">Country</td>
          <td colspan="6"><input type="text" name="scountry" id="scountry" size="50"/></td>
          <td colspan="2"><input type="text" name="pcountry" id="pcountry" size="50"/></td>
        </tr>
        <tr>
          <td style="font-size:12px">Email</td>
          <td colspan="6"><input type="text" name="email" id="email" size="50"/></td>
          <td colspan="2">&nbsp;</td>
        </tr>
        <tr>
          <td style="font-size:12px">Phone</td>
          <td style="font-size:12px">Country</td>
          <td><input name="country_code" type="text" id="country_code"  size="10" maxlength="15" placeholder="+999"/></td>
          <td style="font-size:12px">Area</td>
          <td><input name="area_code" type="text" id="area_code"  size="10" maxlength="10" /></td>
          <td style="font-size:12px">Number</td>
          <td><input type="text" name="phone" id="phone" size="20"/></td>
          <td style="font-size:12px">Mobile</td>
          <td><input type="text" name="mobile" id="mobile" size="30"/></td>
        </tr>
        <tr>
          <td colspan="9" style="font-size:12px">applies to Cmeds4U for the supply of the medicines listed below for which the applicant agrees to pay in advance on the terms and conditions described in the following section.</td>
        </tr>
    </table>
 </div>
 <p>&nbsp;</p>
  <div id="agreement" style="margin-left:auto;margin-right:auto;visibility:visible;height:105px;width:900px;font-size:12px;overflow:scroll;background-image:url('../images/back.jpg');">
  Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi. Nam liber tempor cum soluta nobis eleifend option congue nihil imperdiet doming id quod mazim placerat facer possim assum. Typi non habent claritatem insitam; est usus legentis in iis qui facit eorum claritatem. Investigationes demonstraverunt lectores legere me lius quod ii legunt saepius. Claritas est etiam processus dynamicus, qui sequitur mutationem consuetudium lectorum. Mirum est notare quam littera gothica, quam nunc putamus parum claram, anteposuerit litterarum formas humanitatis per seacula quarta decima et quinta decima. Eodem modo typi, qui nunc nobis videntur parum clari, fiant sollemnes in futurum.
  </div>
 
 <div id="medicine" style="float:left;visibility:visible;height:205px;width:1000px;background-image:url('../images/back.jpg');">
	<table width="900" align="center">
    	<tr>
    	  <td style="font-size:12px">I will accept generic medicines where available &nbsp; <select name="mgeneric" id="mgeneric"><?php echo $generic_options;?></select></td>
        	<td><input type="button" name="breq" id="breq" value="Add a medicine to your list" onclick="addreq()"/></td>
        </tr>
		<tr>
            <td colspan="2"><?php include "getrequirements.php" ?></td>
        </tr>
	</table>
 </div> 
 
  
 <div id="remainder" style="float:left;visibility:visible;height:160px;width:1000px;background-image:url('../images/back.jpg');">
	 <table width="900" align="center">
    	<tr>
        	<td style="font-size:12px">Preferred Depot for collection</td>
        	<td colspan="3" style="font-size:12px"><select name="mdepot" id="mdepot"><?php echo $depot_options;?></select></td>
       	</tr>
    	<tr>
    	  <td style="font-size:12px">Doctor</td>
    	  <td colspan="3" style="font-size:12px"><input type="text" name="doc" id="doc"  size="70"/></td>
   	    </tr>
    	<tr>
    	  <td style="font-size:12px">Doctor's Address</td>
    	  <td colspan="3" style="font-size:12px"><input type="text" name="docadd" id="docadd"  size="70"/></td>
   	    </tr>
    	<tr>
    	  <td style="font-size:12px">Doctor's Phone Number</td>
    	  <td style="font-size:12px"><input type="text" name="docphone" id="docphone" /></td>
    	  <td style="font-size:12px">Doctor's mobile</td>
    	  <td><input type="text" name="docmob" id="docmob" /></td>
  	    </tr>
    	<tr>
    	  <td style="font-size:12px">Doctor's email</td>
    	  <td colspan="3" style="font-size:12px"><input type="text" name="docemail" id="docemail" size="70"/></td>
   	    </tr>
    	<tr>
    	  <td colspan="2" style="font-size:14px">By clicking this button, you agree to the foregoing:-</td>
    	  <td colspan="2"><input type="button" name="agree" id="agree" value="I accept the terms and conditions of this application" onclick="post()"/></td>
  	  </tr>
     </table>

 </div>
  <p>&nbsp;</p>
<div id="footer"; align="center" style="width:1000px">
         © Murray Russell 2014 - <?php echo $thisyear; ?>
    </div>
  
</div>
</form>

<?php

	if($_REQUEST['savebutton'] == "Y") {
		
		$fn = ucwords($_REQUEST['fname']);
		$ln = ucwords($_REQUEST['lname']);
		$sa1 = ucwords($_REQUEST['sad1']);
		$sa2 = ucwords($_REQUEST['sad2']);
		$ad2 = '';
		$spc = $_REQUEST['spostcode'];
		$stn = ucwords($_REQUEST['stown']);
		$sct = ucwords($_REQUEST['scountry']);
		$pa1 = $_REQUEST['pad1'];
		$pa2 = $_REQUEST['pad2'];
		$ppc = $_REQUEST['ppostcode'];
		$ptn = ucwords($_REQUEST['ptown']);
		$pct = ucwords($_REQUEST['pcountry']);
		$gen = $_REQUEST['mgeneric'];
		$dep = $_REQUEST['mdepot'];
		$mob = $_REQUEST['mobile'];
		$em = $_REQUEST['email'];
		$ph = $_REQUEST['phone'];
		$doc = ucwords($_REQUEST['doc']);
		$docadd = ucwords($_REQUEST['docadd']);
		$docphone = $_REQUEST['docphone'];
		$docmobile = $_REQUEST['docmob'];
		$docemail = $_REQUEST['docemail'];
		$title = $_REQUEST['mtitle'];
		$dob = $_REQUEST['myear'].'-'.$_REQUEST['mdobmonth'].'-'.$_REQUEST['mdobday'];
		$age = $_REQUEST['mage'];
		$position = '';
		$occupation = '';
		$gender = $_REQUEST['mgender'];
		$memtype = 'M';
		$middlename = '';
		$preferredname = '';
		$sub_id = 1;
		
		include_once("../includes/DBClass.php");
		$db = new DBClass();
		
		
		$db->query("insert into ".$cltdb.".members (title,dob,age,firstname,middlename,lastname,preferredname,occupation,gender,position,membertype,sub_id) values (:title,:dob,:age,:firstname,:middlename,:lastname,:preferredname,:occupation,:gender,:position,:membertype,:sub_id)");
		$db->bind(':title', $title);
		$db->bind(':dob', $dob);
		$db->bind(':age', $age);					   
		$db->bind(':firstname', $fn);
		$db->bind(':middlename', $middlename);
		$db->bind(':lastname', $ln);					   
		$db->bind(':preferredname', $preferredname);
		$db->bind(':occupation', $occupation);
		$db->bind(':gender', $gender);					   
		$db->bind(':position', $position);
		$db->bind(':membertype', $memtype);					   
		$db->bind(':sub_id', $sub_id);

		$db->execute();
		
		$cluid = $db->lastInsertId();
		$_SESSION['s_memberid'] = $cluid;
		
		$db->query("update ".$cltdb.".members set generics = '".$gen."', depot = '".$dep."', doctor = '".$doc."', draddress = '".$docadd."', drphone = '".$docphone."', drmobile = '".$docmobile."', dremail = '".$docemail."' where member_id = ".$cluid);
		$db->execute();

		//****************************************************************
		// add member as debtor of company
		//****************************************************************
		$tid = $cluid;
		
		$dracno = 30000000 + $tid;
		
		$db->query("select lastname from ".$cltdb.".members where member_id = ".$tid);
		$row = $db->single();
		extract($row);
		
			
		$SQLString = "insert into ".$cltdb.".client_company_xref (client_id,company_id,drno,sortcode,member) values ";
		$SQLString .= "(".$tid.",";
		$SQLString .= "1,";
		$SQLString .= $dracno.",'";
		$SQLString .= $lastname.$dracno."-0','";
		$SQLString .= $lastname."')";
		
		$db->query($SQLString);
		$db->execute();

		// Add address
		
		$loc = 'Street';
		$address_type_id = 3;
		$client_id = $cluid;
		$street_no = '';
		$ad1 = ucwords(strtolower($sa1));
		$suburb = ucwords(strtolower($sa2));
		$town = ucwords(strtolower($stn));
		$postcode = $spc;
		$country = $sct;
		$preferredp = '';
		$preferredv = '';
		$sub_id = 1;
		$staff_id = 0;
		
		$db->query("insert into ".$cltdb.".addresses (member_id,staff_id,location,address_type_id,street_no,ad1,ad2,suburb,town,postcode,preferredp,preferredv,country) values (:member_id,:staff_id,:location,:address_type_id,:street_no,:ad1,:ad2,:suburb,:town,:postcode,:preferredp,:preferredv,:country)");
		$db->bind(':member_id', $client_id);
		$db->bind(':staff_id', $staff_id);
		$db->bind(':location', $loc);					   
		$db->bind(':address_type_id', $address_type_id);
		$db->bind(':street_no', $street_no);
		$db->bind(':ad1', $ad1);					   
		$db->bind(':ad2', $ad2);
		$db->bind(':suburb', $suburb);
		$db->bind(':town', $town);					   
		$db->bind(':postcode', $postcode);
		$db->bind(':preferredp', $preferredp);					   
		$db->bind(':preferredv', $preferredv);
		$db->bind(':country', $country);

		$db->execute();
		
		if ($ptn <> '' ) {
			$loc = 'Postal';
			$address_type_id = 4;
			$client_id = $cluid;
			$street_no = '';
			$ad1 = ucwords(strtolower($pa1));
			$suburb = ucwords(strtolower($pa2));
			$town = ucwords(strtolower($ptn));
			$postcode = $ppc;
			$country = $pct;
			$preferredp = '';
			$preferredv = '';
			$sub_id = 1;
			$staff_id = 0;
			
			$db->query("insert into ".$cltdb.".addresses (member_id,staff_id,location,address_type_id,street_no,ad1,ad2,suburb,town,postcode,preferredp,preferredv,country) values (:member_id,:staff_id,:location,:address_type_id,:street_no,:ad1,:ad2,:suburb,:town,:postcode,:preferredp,:preferredv,:country)");
			$db->bind(':member_id', $client_id);
			$db->bind(':staff_id', $staff_id);
			$db->bind(':location', $loc);					   
			$db->bind(':address_type_id', $address_type_id);
			$db->bind(':street_no', $street_no);
			$db->bind(':ad1', $ad1);					   
			$db->bind(':ad2', $ad2);
			$db->bind(':suburb', $suburb);
			$db->bind(':town', $town);					   
			$db->bind(':postcode', $postcode);
			$db->bind(':preferredp', $preferredp);					   
			$db->bind(':preferredv', $preferredv);
			$db->bind(':country', $country);
	
			$db->execute();
		
		} else {
			$loc = 'Street';
			$address_type_id = 4;
			$client_id = $cluid;
			$street_no = '';
			$ad1 = ucwords(strtolower($sa1));
			$suburb = ucwords(strtolower($sa2));
			$town = ucwords(strtolower($stn));
			$postcode = $spc;
			$country = $sct;
			$preferredp = '';
			$preferredv = '';
			$sub_id = 1;
			$staff_id = 0;
			
			$db->query("insert into ".$cltdb.".addresses (member_id,staff_id,location,address_type_id,street_no,ad1,ad2,suburb,town,postcode,preferredp,preferredv,country) values (:member_id,:staff_id,:location,:address_type_id,:street_no,:ad1,:ad2,:suburb,:town,:postcode,:preferredp,:preferredv,:country)");
			$db->bind(':member_id', $client_id);
			$db->bind(':staff_id', $staff_id);
			$db->bind(':location', $loc);					   
			$db->bind(':address_type_id', $address_type_id);
			$db->bind(':street_no', $street_no);
			$db->bind(':ad1', $ad1);					   
			$db->bind(':ad2', $ad2);
			$db->bind(':suburb', $suburb);
			$db->bind(':town', $town);					   
			$db->bind(':postcode', $postcode);
			$db->bind(':preferredp', $preferredp);					   
			$db->bind(':preferredv', $preferredv);
			$db->bind(':country', $country);
	
			$db->execute();
		
		}
				
		// add comms

		if ($ph <> '') {
			$comms_type_id = 1;
			$client_id = $cluid;
			$uid = $cluid;
			$country_code = $_REQUEST['country_code'];
			$area_code = $_REQUEST['area_code'];
			$comm = $ph;
			$preferred = '';
			$staff_id = 0;
	
			$comm1 = str_replace(" ","",$comm);
			$comm2 = str_replace("-","",$comm1);
			$comm3 = str_replace(")","",$comm2);
			$comm4 = str_replace("(","",$comm3);
								   
			$SQLString = "insert into ".$cltdb.".comms (member_id,staff_id,comms_type_id,country_code,area_code,preferred,comm,comm2) values ";
			$SQLString .= "(".$client_id.",";
			$SQLString .= $staff_id.",";
			$SQLString .= $comms_type_id.",'";
			$SQLString .= $country_code."','";
			$SQLString .= $area_code."','";
			$SQLString .= $preferred."','";
			$SQLString .= $comm."','";
			$SQLString .= $comm4."')";
			
			$db->query($SQLString);
			$db->execute();
			
		}
		
		if ($mob <> '') {
			$comms_type_id = 3;
			$client_id = $cluid;
			$uid = $cluid;
			$country_code = '';
			$area_code = '';
			$comm = $mob;
			$preferred = '';
			$staff_id = 0;
	
			$comm1 = str_replace(" ","",$comm);
			$comm2 = str_replace("-","",$comm1);
			$comm3 = str_replace(")","",$comm2);
			$comm4 = str_replace("(","",$comm3);
								   
			$SQLString = "insert into ".$cltdb.".comms (member_id,staff_id,comms_type_id,country_code,area_code,preferred,comm,comm2) values ";
			$SQLString .= "(".$client_id.",";
			$SQLString .= $staff_id.",";
			$SQLString .= $comms_type_id.",'";
			$SQLString .= $country_code."','";
			$SQLString .= $area_code."','";
			$SQLString .= $preferred."','";
			$SQLString .= $comm."','";
			$SQLString .= $comm4."')";
			
			$db->query($SQLString);
			$db->execute();
			
		}
		
		if ($em <> '') {
			$comms_type_id = 4;
			$client_id = $cluid;
			$uid = $cluid;
			$country_code = '';
			$area_code = '';
			$comm = $em;
			$preferred = '';
			$staff_id = 0;
	
			$comm1 = str_replace(" ","",$comm);
			$comm2 = str_replace("-","",$comm1);
			$comm3 = str_replace(")","",$comm2);
			$comm4 = str_replace("(","",$comm3);
								   
			$SQLString = "insert into ".$cltdb.".comms (member_id,staff_id,comms_type_id,country_code,area_code,preferred,comm,comm2) values ";
			$SQLString .= "(".$client_id.",";
			$SQLString .= $staff_id.",";
			$SQLString .= $comms_type_id.",'";
			$SQLString .= $country_code."','";
			$SQLString .= $area_code."','";
			$SQLString .= $preferred."','";
			$SQLString .= $comm."','";
			$SQLString .= $comm4."')";
			
			$db->query($SQLString);
			$db->execute();
		}

		// add medicines

		$db->query("insert into ".$prcdb.".requirements (patientid,medicineid,dosage,qty,sub_id ) select ".$cluid.", itemid,dosage,qty,1 from ".$prcdb.".".$rectable." where guid = '".$sessionid."'");
		$db->execute();
		
		// and remove the records from rectable
		$db->query("delete from ".$prcdb.".".$rectable." where  guid = '".$sessionid."'");
		$db->execute();

		// calculate total cost plus gst

		$mdb = $_SESSION['s_prcdb'];
		$fdb = $_SESSION['s_findb'];
		
		$db->query("select ".$mdb.".requirements.dosage,".$mdb.".requirements.qty,".$fdb.".stkmast.item,".$fdb.".stkmast.noinunit,case ".$fdb.".stkmast.setsell when 0 then ".$fdb.".stkmast.avgcost else ".$fdb.".stkmast.setsell end as cost,".$fdb.".stkmast.deftax from ".$mdb.".requirements,".$fdb.".stkmast where ".$mdb.".requirements.medicineid = ".$fdb.".stkmast.itemid and ".$mdb.".requirements.patientid = ".$cluid);
		$rows = $db->resultset();
			
		$totcost = 0;
		foreach ($rows as $row) {
			extract($row);
				
			// calculate quantity required
				
			switch ($dosage) {
				case 'Month';
					$qtyreq = $qty;
				break;
				case 'Week';
					$qtyreq = ($qty * 4);
				break;
				case 'Day';
					$qtyreq = ($qty * 28);
				break;
			}
				
			// get relevant tax	
	
			$db->query("select taxpcent from ".$findb.".taxtypes where uid = ".$deftax);
			$rowt = $db->single();
			extract($rowt);
			
			// calculate unit/packs required
				
			$unitsreq = ceil($qtyreq/$noinunit);
			$mcost = ($unitsreq * $cost) * (1 + ($taxpcent / 100));
				
			$totcost = $totcost + $mcost;
		}	
		$totcost = round($totcost,2);
		$_SESSION['s_totcost'] = $totcost;		
		
		$db->closeDB();
		
	?>
    
		<script>
		var x = 0, y = 0;
		x = window.screenX +5;
		y = window.screenY +200;
		window.open('finalise.php','fn','toolbar=0,scrollbars=1,height=500,width=900,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
		this.close();
		</script>
	<?php
	
	}
?>

</body>
</html>