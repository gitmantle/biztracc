<?php
session_start();
//ini_set('display_errors', true);

$usersession = $_SESSION['usersession'];
date_default_timezone_set($_SESSION['s_timezone']);

$cluid = $_SESSION["s_memberid"];

$cltdb = $_SESSION['s_cltdb'];

$from = $_REQUEST['from'];

?>


<html>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Add Recipient</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">

<script>
function stopRKey(evt) {
  var evt = (evt) ? evt : ((event) ? event : null);
  var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
  if ((evt.keyCode == 13) && (node.type=="text"))  {return false;}
}


</script>

<style type="text/css">
<!--
.style1 {font-size: large}
-->
</style>
</head>


<body>
<div id="mwin">
<form name="form1" method="post" >
  <table width="570" border="0" align="center">
    <tr>
      <td colspan="2"><div align="center" class="style1"><u>Add non-member email recipient </u></div></td>
    </tr>
    <tr>
      <td width="106" class="boxlabel"><div align="right">Name</div></td>
      <td><input name="recipient" type="text" id="recipient"  size="70" maxlength="70"></td>
      </tr>
    <tr>
      <td width="106" class="boxlabel"><div align="right">Email</div></td>
      <td><input name="email" type="text" id="email"  size="70" maxlength="70"></td>
      </tr>
	<tr>
      <td>&nbsp;</td>
      <td align="right"><input type="submit" value="Save" name="save" ></td>
      </tr>
  </table>
</form>
</div>
	<script>document.onkeypress = stopRKey;</script> 

<?php

	if(isset($_POST['save'])) {

		if ($_REQUEST['email'] == '') {
			echo '<script>';
			echo 'alert("Please enter an email address.")';
			echo '</script>';	
		} else {			

				include_once("../includes/cltadmin.php");
				$oIn = new cltadmin;	
				
				$oIn->recipient = $_REQUEST['recipient'];
				$oIn->email = $_REQUEST['email'];
		
				$oIn->AddRecipient();
	
				?>
				<script>
				var frm = "<?php echo $from; ?>";
				
				if (frm == 'u') {
					window.open("","updtrecipients").jQuery("#subemaillist").trigger("reloadGrid");
				} else {
					if (frm == 'c') {
						window.open("","chgmeeting").jQuery("#ccemaillistm").trigger("reloadGrid");
					} else {
						window.open("","emailmem").jQuery("#toemaillist").trigger("reloadGrid");
						window.open("","emailmem").jQuery("#ccemaillist").trigger("reloadGrid");
						window.open("","emailmem").jQuery("#bccemaillist").trigger("reloadGrid");
					}
				}
				this.close();
				</script>
				<?php
		
			
		}
	}

?>


</body>
</html>
