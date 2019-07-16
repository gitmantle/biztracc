<?php

$thisyear = date('Y');

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Logtracc - Administration</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">


<script>

	function verify() {
		var uname = document.getElementById("userid").value;
		if (uname == "") {
			alert("Please enter your user name");
			return false;
		}
		var pword = document.getElementById("password").value;
		if (pword == "") {
			alert("Please enter your password");
			return false;
		} else {
			document.getElementById('form1').submit();
		}
	}
	
	function submitenter(myfield,e)
	{
	var keycode;
	if (window.event) keycode = window.event.keyCode;
	else if (e) keycode = e.which;
	else return true;
	
	if (keycode == 13)
	   {
	   myfield.form.submit();
	   return false;
	   }
	else
	   return true;
	}
	

</script>


</head>

<body>
<div id="wrapper">

<div id="mainheader">
	<div id="mainlefttop"><img src="../images/mantle.png" width="200" height="50"></div>
</div>



<form name="form1" id="form1" method="post" action="verify.php">

  <table border="0" align="center">
  	<tr>
    	<td colspan="2">&nbsp;</td>
    </tr>
    <tr>
      <td class="boxlabel"><label style="font-size:16px">User ID</label> </td>
      <td><input type="password" name="userid" id="userid"></td>
    </tr>
    <tr>
      <td class="boxlabel"><label style="font-size:16px">Password</label></td>
      <td><input type="password" name="password" id="password"  onKeyPress="return submitenter(this,event)"></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td align="right"><input type="button" name="bsubmit" id="bsubmit" value="Submit" onclick="verify()"></td>
    </tr>
    <tr>
      <td colspan="2" align="center">© Murray Russell 2010 - <?php echo $thisyear; ?></td>
      </tr>
    </table>

  
</form>

</div>

<script>
  document.getElementById('userid').focus();
</script>
</body>
</html>