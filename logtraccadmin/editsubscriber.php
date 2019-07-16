<?php
session_start();
$dbs = $_SESSION['s_admindb'];

require("../db.php");
mysql_select_db($dbs) or die(mysql_error());

$sid = $_SESSION['s_subid'];

$q = "select * from subscribers where subid = ".$sid;
$result = mysql_query($q) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);

// populate timezone list
    $arr = array('Africa/Abidjan','Africa/Accra','Africa/Addis_Ababa','Africa/Algiers','Africa/Blantyre','Africa/Brazzaville','Africa/Bujumbura','Africa/Cairo','Africa/Casablanca','Africa/Conakry','Africa/Dakar',	'Africa/Dar_es_Salaam','Africa/Freetown','Africa/Gaborone','Africa/Harare','Africa/Johannesburg','Africa/Kampala','Africa/Kigali','Africa/Kinshasa','Africa/Lagos','Africa/Lome','Africa/Luanda','Africa/Lubumbashi',	'Africa/Lusaka','Africa/Maputo','Africa/Maseru','Africa/Mbabane','Africa/Monrovia','Africa/Nairobi','Africa/Tunis','Africa/Windhoek','Australia/ACT','Australia/Adelaide','Australia/Brisbane','Australia/Broken_Hill','Australia/Canberra','Australia/Currie','Australia/Darwin','Australia/Eucla','Australia/Hobart','Australia/LHI','Australia/Lindeman','Australia/Lord_Howe','Australia/Melbourne','Australia/North','Australia/NSW','Australia/Perth','Australia/Queensland','Australia/South','Australia/Sydney','Australia/Tasmania','Australia/Victoria','Australia/West','Australia/Yancowinna','Eurpope/Amsterdam','Europe/Belfast','Europe/Berlin','Europe/Brussels','Europe/Dublin','Europe/London','Europe/Madrid','Europe/Paris','Pacific/Auckland');
	$zone_options = '';
    for($i = 0; $i < count($arr); $i++)	{
		if ($arr[$i] == $timezone) {
			$selected = 'selected="selected"';
		} else {
			$selected = '';
		}	
		$zone_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
 	}

?>
<html>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Edit Subscriber</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<script src="../includes/jquery/js/jquery.js" type="text/javascript"></script>
<script>
function stopRKey(evt) {
  var evt = (evt) ? evt : ((event) ? event : null);
  var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
  if ((evt.keyCode == 13) && (node.type=="text"))  {return false;}
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
<div id="swin">
  <form name="form1" method="post" >
    <table width="450" border="0" align="center">
      <tr>
        <td colspan="3"><div align="center" class="style1"><u>Edit Subscriber</u></div></td>
      </tr>
      <tr>
        <td width="121" class="boxlabel">Subscriber Name</td>
        <td colspan="2"><input name="sname" type="text" id="sname"  size="50" maxlength="50" value="<?php echo $subname; ?>"></td>
      </tr>
      <tr>
        <td class="boxlabel">Address line 1</td>
        <td colspan="2"><input name="sad1" type="text" id="sad1"  size="50" maxlength="50" value="<?php echo $subad1; ?>"></td>
      </tr>
      <tr>
        <td class="boxlabel">Address line 2</td>
        <td colspan="2"><input name="sad2" type="text" id="sad2"  size="50" maxlength="50" value="<?php echo $subad2; ?>"></td>
      </tr>
      <tr>
        <td class="boxlabel">Address line 3</td>
        <td colspan="2"><input name="sad3" type="text" id="sad3"  size="50" maxlength="50" value="<?php echo $subad3; ?>"></td>
      </tr>
      <tr>
        <td class="boxlabel">Phone</td>
        <td colspan="2"><input name="sphone" type="text" id="sphone"  size="50" maxlength="30" value="<?php echo $subphone; ?>"></td>
      </tr>
      <tr>
        <td class="boxlabel">Email</td>
        <td colspan="2"><input name="semail" type="text" id="semail"  size="50" maxlength="100" value="<?php echo $subemail; ?>"></td>
      </tr>
      <tr>
        <td class="boxlabel">Main Contact</td>
        <td colspan="2"><input name="scontact" type="text" id="scontact"  size="50" maxlength="50" value="<?php echo $subcontact; ?>"></td>
      </tr>
      <tr>
        <td class="boxlabel">Subscribes to:-&nbsp;</td>
        <td width="272" class="boxlabelleft">Client Management</td>
        <td width="43" class="boxlabelleft"><input type="text" name="tclt" id="tclt" size="3" maxlength="1" value="<?php echo $clt; ?>"></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td class="boxlabelleft">Financial Management</td>
        <td class="boxlabelleft"><input type="text" name="tfin" id="tfin" size="3" maxlength="1" value="<?php echo $fin; ?>"></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td class="boxlabelleft">Human Resosurces</td>
        <td class="boxlabelleft"><input type="text" name="thrs" id="thrs" size="3" maxlength="1" value="<?php echo $hrs; ?>"></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td class="boxlabelleft">Processes</td>
        <td class="boxlabelleft"><input type="text" name="tprc" id="tprc" size="3" maxlength="1" value="<?php echo $prc; ?>"></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td class="boxlabelleft">Manufacturing</td>
        <td class="boxlabelleft"><input type="text" name="tman" id="tman" size="3" maxlength="1" value="<?php echo $man; ?>"></td>
      </tr>
      <tr>
        <td class="boxlabel">Time Zone</td>
	      <td colspan="2" align="left"><select name="timezone" id="timezone">
              <?php echo $zone_options;?>
          </select></td>
      </tr>
      
      <tr>
        <td>&nbsp;</td>
        <td colspan="2" align="right"><input type="submit" value="Save" name="save" ></td>
      </tr>
    </table>
  </form>
</div>
<script>document.onkeypress = stopRKey;</script>
<?php

	if(isset($_POST['save'])) {

		if ($_REQUEST['sname'] == '') {
			echo '<script>';
			echo 'alert("Please enter a subscriber.")';
			echo '</script>';	
		} else {			
			$sname = $_REQUEST['sname'];
			$sad1 = $_REQUEST['sad1'];
			$sad2 = $_REQUEST['sad2'];
			$sad3 = $_REQUEST['sad3'];
			$sphone = $_REQUEST['sphone'];
			$semail = $_REQUEST['semail'];
			$scontact = $_REQUEST['scontact'];
			$eclt = strtoupper($_REQUEST['tclt']);
			$efin = strtoupper($_REQUEST['tfin']);
			$ehrs = strtoupper($_REQUEST['thrs']);
			$eprc = strtoupper($_REQUEST['tprc']);
			$eman = strtoupper($_REQUEST['tman']);
			
			$q = 'update subscribers set ';
			$q.= 'subname = "'.$sname.'",';
			$q.= 'subad1 = "'.$sad1.'",';
			$q.= 'subad2 = "'.$sad2.'",';
			$q.= 'subad3 = "'.$sad3.'",';
			$q.= 'subphone = "'.$sphone.'",';
			$q.= 'subemail = "'.$semail.'",';
			$q.= 'subcontact = "'.$scontact.'",';
			$q.=  'clt = "'.$eclt.'",';
			$q.=  'fin = "'.$efin.'",';
			$q.=  'hrs = "'.$ehrs.'",';
			$q.=  'prc = "'.$eprc.'",';
			$q.=  'man = "'.$eman.'"';
			$q .= ' where subid = '.$sid;
			$r = mysql_query($q) or die(mysql_error().' '.$q);


			if ($eclt == 'Y' ) {
					
					// Add new empty client database for this subscriber if it does not exist
					$server = $_SESSION['s_server'];
					$user = 'logtracc9';
					$pwd = 'dun480can';
					$conn = mysql_connect($server,$user,$pwd) or die("Check your database connection");
					
					$checkdb = 'sub'.$sid;
					$i = 1;
					$found = 'N';
					
					$res = mysql_query("SHOW DATABASES");

					while ($row = mysql_fetch_assoc($res)) {
						$dbarr[$i] = $row['Database'];
						
						if ($dbarr[$i] == $checkdb) {
							$found = 'Y';
						}
						
						$i = $i + 1;
					}
					
					if ($found == 'N') {	
	
						$q = "create database if not exists sub".$sid." DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci";
						$r = mysql_query($q) or die(mysql_error().' '.$q);
						$q = "GRANT select,insert,update,delete,index,create temporary tables,create,alter,drop,lock tables ON *.* TO 'logtracc9'@'localhost' IDENTIFIED BY 'dun480can'";
						$r = mysql_query($q) or die(mysql_error().' '.$q);
						$q = "GRANT select,insert,update,delete,index,create temporary tables,create,alter,drop,lock tables ON *.* TO 'logtracc9'@'%' IDENTIFIED BY 'dun480can'";
						$r = mysql_query($q) or die(mysql_error().' '.$q);	
			
						$newdb = 'sub'.$sid;
						mysql_select_db($newdb) or die(mysql_error());
			
						include 'addbclt.php';
						
					}
			
			}
			
			?>
<script>
			window.open("","updtsubs").jQuery("#subscriberlist").trigger("reloadGrid");
			this.close();
			</script>
<?php
	
			
		}

	}

?>
</body>
</html>
