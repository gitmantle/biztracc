<?php
session_start();
$usersession = $_SESSION['usersession'];

$dbs = $_SESSION['s_admindb'];

$sbid = $_SESSION['s_subid'];

require("../db.php");
mysql_select_db($dbs) or die(mysql_error());
$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);

$q = "select * from subscribers where subid = ".$sbid;
$r = mysql_query($q) or die(mysql_error());
$row = mysql_fetch_array($r);
extract($row);
$sbname = $subname;

// populate country list
	$arr = array("Afghanistan","Albania","Algeria","Andorra","Angola","Antigua and Barbuda","Argentina","Armenia","Australia","Austria","Azerbaijan","Bahamas","Bahrain","Bangladesh","Barbados","Belarus","Belgium","Belize","Benin","Bhutan","Bolivia","Bosnia and Herzegovina","Botswana","Brazil","Brunei","Bulgaria","Burkina Faso","Burundi","Cambodia","Cameroon","Canada","Cape Verde","Central African Republic","Chad","Chile","China","Colombi","Comoros","Congo (Brazzaville)","Congo","Costa Rica","Cote dIvoire","Croatia","Cuba","Cyprus","Czech Republic","Denmark","Djibouti","Dominica","Dominican Republic","East Timor (Timor Timur)","Ecuador","Egypt","El Salvador","Equatorial Guinea","Eritrea","Estonia","Ethiopia","Fiji","Finland","France","Gabon","Gambia, The","Georgia","Germany","Ghana","Greece","Grenada","Guatemala","Guinea","Guinea-Bissau","Guyana","Haiti","Honduras","Hungary","Iceland","India","Indonesia","Iran","Iraq","Ireland","Israel","Italy","Jamaica","Japan","Jordan","Kazakhstan","Kenya","Kiribati","Korea, North","Korea, South","Kuwait","Kyrgyzstan","Laos","Latvia","Lebanon","Lesotho","Liberia","Libya","Liechtenstein","Lithuania","Luxembourg","Macedonia","Madagascar","Malawi","Malaysia","Maldives","Mali","Malta","Marshall Islands","Mauritania","Mauritius","Mexico","Micronesia","Moldova","Monaco","Mongolia","Morocco","Mozambique","Myanmar","Namibia","Nauru","Nepal","Netherlands","New Zealand","Nicaragua","Niger","Nigeria","Norway","Oman","Pakistan","Palau","Panama","Papua New Guinea","Paraguay","Peru","Philippines","Poland","Portugal","Qatar","Romania","Russia","Rwanda","Saint Kitts and Nevis","Saint Lucia","Saint Vincent","Samoa","San Marino","Sao Tome and Principe","Saudi Arabia","Senegal","Serbia and Montenegro","Seychelles","Sierra Leone","Singapore","Slovakia","Slovenia","Solomon Islands","Somalia","South Africa","Spain","Sri Lanka","Sudan","Suriname","Swaziland","Sweden","Switzerland","Syria","Taiwan","Tajikistan","Tanzania","Thailand","Togo","Tonga","Trinidad and Tobago","Tunisia","Turkey","Turkmenistan","Tuvalu","Uganda","Ukraine","United Arab Emirates","United Kingdom","United States","Uruguay","Uzbekistan","Vanuatu","Vatican City","Venezuela","Vietnam","Yemen","Zambia","Zimbabwe");
	$country_options = "<option value=\"0\">Select Country</option>";
    for($i = 0; $i < count($arr); $i++)	{
		if ($arr[$i] == $porigin) {
			$selected = 'selected="selected"';
		} else {
			$selected = '';
		}	
		$country_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
 	}




?>
<html>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Add Company</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<script src="../includes/jquery/js/jquery.js" type="text/javascript"></script>
<script>

function stopRKey(evt) {
  var evt = (evt) ? evt : ((event) ? event : null);
  var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
  if ((evt.keyCode == 13) && (node.type=="text"))  {return false;}
}

function copyemail(em) {
	document.getElementById('soemail').value = em;	
}

function checkup() {
	var um = document.getElementById('uname').value;
	var pd = document.getElementById('pwd').value;
	$.get("../includes/ajaxCheckup.php", {um: um, pd: pd}, function(data){
	  if (data == 'Y') {
		  alert('This username/password combination is already in use. Please choose another.');
		  document.getElementById('pwd').focus();
		  return false;
	  } else {
		  return true;
	  }
  });
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
<div id="bwin">

<form name="form1" method="post" >
  <table width="1080" border="0" align="center">
    <tr>
      <td colspan="5"><div align="center" class="style1"><u>Add a Company to Subscriber <?php echo $sbname; ?></u></div></td>
    </tr>
    <tr>
      <td width="212" class="boxlabel">Company Name</td>
      <td colspan="4"><input name="sname" type="text" id="sname"  size="45" maxlength="45"></td>
    </tr>
    <tr>
      <td class="boxlabel">Company Email</td>
      <td colspan="4"><input name="semail" type="text" id="semail"  size="70" maxlength="70" onBlur="copyemail(this.value)"></td>
    </tr>
    <tr>
      <td class="boxlabel">Administrator First Name</td>
      <td colspan="4"><input name="fname" type="text" id="fname"  size="45" maxlength="45"></td>
    </tr>
    <tr>
      <td class="boxlabel">Administrator Last Name</td>
      <td colspan="4"><input name="lname" type="text" id="lname"  size="45" maxlength="45"></td>
    </tr>
    <tr>
      <td class="boxlabel">Administrator Email</td>
      <td colspan="4"><input name="soemail" type="text" id="soemail"  size="70" maxlength="70"></td>
    </tr>
    <tr>
      <td class="boxlabel">Street Address</td>
      <td  class="boxlabelleft" colspan="4"><input name="sosad1" type="text" id="sosad1"  size="20" maxlength="20">
        ,
        <input name="sosad2" type="text" id="sosad2"  size="16" maxlength="20">
        ,
        <input name="sosad3" type="text" id="sosad3"  size="16" maxlength="20">
        Town
        <input name="sostown" type="text" id="sostown"  size="16" maxlength="20">
        Post Code
        <input name="sospostcode" type="text" id="sospostcode"  size="7" maxlength="7"></td>
    </tr>
    <tr>
      <td class="boxlabel">Postal Address</td>
      <td  class="boxlabelleft" colspan="4"><input name="sopad1" type="text" id="sopad1"  size="20" maxlength="20">
        ,
        <input name="sopad2" type="text" id="sopad2"  size="16" maxlength="20">
        ,
        <input name="sopad3" type="text" id="sopad3"  size="16" maxlength="20">
        Town
        <input name="soptown" type="text" id="soptown"  size="16" maxlength="20">
        Post Code
        <input name="soppostcode" type="text" id="soppostcode"  size="7" maxlength="7"></td>
    </tr>
    <tr>
      <td class="boxlabel">Company Phone</td>
      <td><input name="sophone" type="text" id="sophone"  size="20" maxlength="20"></td>
        <td class="boxlabelleft"> Origin</td>
        <td width="636" colspan="2" align="left"><select name="tcountry" id="tcountry">
        	<?php echo $country_options; ?>
        </select></td>
      </tr>
    <tr>
      <td class="boxlabel">Company Admin User Name</td>
      <td colspan="4"><input name="uname" type="text" id="uname"  size="45" maxlength="45"></td>
    </tr>
    <tr>
      <td class="boxlabel">Company Admin Password</td>
      <td colspan="4"><input name="pwd" type="text" id="pwd"  size="45" maxlength="45" onBlur="checkup()"></td>
    </tr>
    <tr>
      <td class="boxlabel">Administrator Mobile</td>
      <td colspan="4"><input name="somobile" type="text" id="somobile"  size="20" maxlength="20"></td>
    </tr>
    <tr>
      <td class="boxlabel">Google Calendar Username</td>
      <td><input name="guser" type="text" id="guser"  size="50" maxlength="45"></td>
    </tr>
    <tr>
      <td class="boxlabel">Google Calendar Password</td>
      <td><input name="gpwd" type="text" id="gpwd"  size="50" maxlength="45"></td>
    </tr>
   
    <tr>
      <td>&nbsp;</td>
      <td colspan="3" align="left"><input type="submit" value="Save" name="save" ></td>
    </tr>
  </table>
</form>
</div>
<script>
  document.onkeypress = stopRKey;
  document.getElementById('sname').focus();
</script><?php


	if(isset($_POST['save'])) {
		$ok = 'Y';

		if ($_REQUEST['sname'] == '') {
			echo '<script>';
			echo 'alert("Please enter a Company.")';
			echo '</script>';	
			$ok = 'N';
		} 

		if ($_REQUEST['semail'] == '') {
			echo '<script>';
			echo 'alert("Please enter an email address.")';
			echo '</script>';	
			$ok = 'N';
		} 


		if ($ok == 'Y') {
			$sname = $_REQUEST['sname'];
			$semail = $_REQUEST['semail'];
			$sofname = $_REQUEST['fname'];
			$solname = $_REQUEST['lname'];
			$sadd1 = $_REQUEST['sosad1'];
			$sadd2 = $_REQUEST['sosad2'];
			$sadd3 = $_REQUEST['sosad3'];
			$saddtown = $_REQUEST['sostown'];
			$saddpostcode = $_REQUEST['sospostcode'];
			$padd1 = $_REQUEST['sopad1'];
			$padd2 = $_REQUEST['sopad2'];
			$padd3 = $_REQUEST['sopad3'];
			$paddtown = $_REQUEST['soptown'];
			$paddpostcode = $_REQUEST['soppostcode'];
			$scountry = $_REQUEST['tcountry'];
			$sphone = $_REQUEST['sophone'];
			$fname = $_REQUEST['fname'];
			$lname = $_REQUEST['lname'];
			$uname = $_REQUEST['uname'];
			$pwd = $_REQUEST['pwd'];
			$soemail = $_REQUEST['soemail'];
			$sname = $_REQUEST['sname'];
			$smobile = $_REQUEST['somobile'];
			$sguser = $_REQUEST['guser'];
			$sgpwd = $_REQUEST['gpwd'];
			

			$q = 'insert into companies (coysubid,coyname,coyemail,coyoemail,coysad1,coysad2,coysad3,coystown,coyspostcode,coypad1,coypad2,coypad3,coyptown,coyppostcode,coycountry,coyphone,coyomobile) values ('.$sbid.',"'.$sname.'","'.$semail.'","'.$soemail.'","'.$sadd1.'","'.$sadd2.'","'.$sadd3.'","'.$saddtown.'","'.$saddpostcode.'","'.$padd1.'","'.$padd2.'","'.$padd3.'","'.$paddtown.'","'.$paddpostcode.'","'.$scountry.'","'.$sphone.'","'.$smobile.'")';
			$r = mysql_query($q) or die(mysql_error().' '.$q);

			$coyid = mysql_insert_id();

			$q = 'insert into users (ufname,ulname,username,upwd,coyowner,uemail,uadmin,uphone,umobile,ug_user,ug_pwd,sub_id) values (';
			$q .= '"'.$sofname.'",';
			$q .= '"'.$solname.'",';
			$q .= '"'.md5($uname).'",';
			$q .= '"'.md5($pwd).'",';
			$q .= '"Y",';
			$q .= '"'.$soemail.'",';
			$q .= '"Y",';
			$q .= '"'.$sphone.'",';
			$q .= '"'.$smobile.'",';
			$q .= '"'.$sguser.'",';
			$q .= '"'.$sgpwd.'",';
			$q .= $sbid.')';

			$r = mysql_query($q) or die(mysql_error().' '.$q);
			$stfid = mysql_insert_id();

	
			if ($clt = 'Y') {
				$q = 'insert into access (staff_id,subid,coyid,module,usergroup) values ('.$stfid.','.$sbid.','.$coyid.',"clt",20)';
				$r = mysql_query($q) or die(mysql_error().' '.$q);
			}
			if ($fin = 'Y') {
				$q = 'insert into access (staff_id,subid,coyid,module,usergroup) values ('.$stfid.','.$sbid.','.$coyid.',"fin",20)';
				$r = mysql_query($q) or die(mysql_error().' '.$q);

				$i = 1;
				$found = 'N';
				$checkdb = 'fin'.$sbid.'_'.$coyid;
					
				$res = mysql_query("SHOW DATABASES");

				while ($row = mysql_fetch_assoc($res)) {
					$dbarr[$i] = $row['Database'];
						
					if ($dbarr[$i] == $checkdb) {
						$found = 'Y';
					}
						
					$i = $i + 1;
				}
					
				if ($found == 'N') {	


					// Add new empty company accounts database for this subscriber
					$server = $_SESSION['s_server'];
					$user = 'logtracc9';
					$pwd = 'dun480can';
					$connect = mysql_connect($server,$user,$pwd) or die("Check your database connection");
					
					/*
					$q = "create database if not exists fin".$sbid."_".$coyid." DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci";
					$r = mysql_query($q) or die(mysql_error().' '.$q);
					$q = "GRANT select,insert,update,delete,index,create temporary tables,create,alter,drop,lock tables ON *.* TO 'logtracc9'@'localhost' IDENTIFIED BY 'dun480can'";
					$r = mysql_query($q) or die(mysql_error().' '.$q);
					$q = "GRANT select,insert,update,delete,index,create temporary tables,create,alter,drop,lock tables ON *.* TO 'logtracc9'@'%' IDENTIFIED BY 'dun480can'";
					$r = mysql_query($q) or die(mysql_error().' '.$q);	
				
					$newdb = 'fin'.$sbid.'_'.$coyid;
					mysql_select_db($newdb) or die(mysql_error());
				
					//include 'addbfin.php';	
					*/
					//create folder for trading documents for this company
					mkdir("trading_docs/".$coyid);
					
				}
				
			}
			
			mysql_select_db($dbs) or die(mysql_error());
			
			if ($hrs = 'Y') {
				$q = 'insert into access (staff_id,subid,coyid,module,usergroup) values ('.$stfid.','.$sbid.','.$coyid.',"hrs",20)';
				$r = mysql_query($q) or die(mysql_error().' '.$q);
			}
			if ($prc = 'Y') {
				$q = 'insert into access (staff_id,subid,coyid,module,usergroup) values ('.$stfid.','.$sbid.','.$coyid.',"prc",20)';
				$r = mysql_query($q) or die(mysql_error().' '.$q);
			}
			if ($man = 'Y') {
				$q = 'insert into access (staff_id,subid,coyid,module,usergroup) values ('.$stfid.','.$sbid.','.$coyid.',"man",20)';
				$r = mysql_query($q) or die(mysql_error().' '.$q);
			}





			?>
			<script>
				window.open("","updtsubs").jQuery("#companylist").trigger("reloadGrid");
				this.close();
				</script>
			<?php

		}

	}

?>
</body>
</html>
