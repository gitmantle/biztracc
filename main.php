<?php
session_start();
$usersession = $_SESSION['usersession'];

ini_set('display_errors', true);

$logo = $_SESSION['logo'];
$fin = $_SESSION['fin'];
$prc = $_SESSION['prc'];

$admindb = $_SESSION['s_admindb'];
if (isset($_REQUEST['finy'])) {
	$showcoy = $_REQUEST['finy'];
} else {
	$showcoy = 'N';	
}

include_once("includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subscriber = $row['subid'];
$admin = $row['admin'];
$username = $row['uname'];

$db->query("select logo250,logo55,subname from subscribers where subid = :vsubscriber");
$db->bind(':vsubscriber', $subscriber);
$row = $db->single();
$lg250 = $row['logo250'];
$lg55 = $row['logo55'];
$subname = $row['subname'];


//Delete temporary files
$dir = "clt";
$t=time();
$h=opendir($dir);

while($file=readdir($h))
{
	if(substr($file,0,3)=='tmp' and substr($file,-4)=='.pdf')
	{
		$path=$dir.'/'.$file;
	
		if($t-filemtime($path)>3600)
			@unlink($path);
	}
}
closedir($h);


// get list of companies
$db->query('select * from companies where coysubid = :vsubscriber order by coyname');
$db->bind(':vsubscriber', $subscriber);
$rows = $db->resultset();

// populate files list
$file_options = "<option value=\"\">Select Company</option>";
foreach ($rows as $row) {
	extract($row);
	//$coyid = $row['coyid'];
	//$taxyear = $row['taxyear'];
	//$coyname = $row['coyname'];
	$file_options .= "<option value=\"".$coyid.'~'.$taxyear."\">".$coyname."</option>";
}

// populate modules dropdown
$modules_options = 	"<option value=\"\">Modules</option>";
if ($_SESSION['clt'] == 'Y') {
	$modules_options .= "<option value=\"clt\">Business Relationship Management</option>";
} else {
	$modules_options .= "<option value=\"xxx\">Business Relationship Management</option>";
}
if ($_SESSION['fin'] == 'Y') {
	$modules_options .= "<option value=\"fin\">Financial Management</option>";
} else {
	$modules_options .= "<option value=\"xxx\">Financial Management</option>";
}
if ($_SESSION['hrs'] == 'Y') {
	$modules_options .= "<option value=\"hrs\">Human Resources</option>";
} else {
	$modules_options .= "<option value=\"xxx\">Human Resources</option>";
}
if ($_SESSION['prc'] == 'Y') {
	$modules_options .= "<option value=\"prc\">Processes</option>";
} else {
	$modules_options .= "<option value=\"xxx\">Processes</option>";
}
/*
if ($_SESSION['man'] == 'Y') {
	$modules_options .= "<option value=\"man\">Manufacturing</option>";
} else {
	$modules_options .= "<option value=\"xxx\">Manufacturing</option>";
}
*/
$modules_options .="<option value=\"for\">Forum</option>";
$modules_options .="<option value=\"doc\">Documentation</option>";
$modules_options .="<option value=\"thm\">Change Theme</option>";
$modules_options .="<option value=\"adm\">System Administration</option>";
$modules_options .="<option value=\"lgn\">Login</option>";

$thisyear = date('Y');

$db->closeDB();

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>BizTracc - Business Management</title>
<link rel="stylesheet" href="includes/mantle.css" media="screen" type="text/css">
<script src="includes/jquery/js/jquery.js" type="text/javascript"></script>
<script type='text/javascript'>

var cpack;

	function selectpack(pack) {
		cpack = pack;
		if (pack == 'xxx') {
			alert('You are not an authorised to use this module');
			return false;
	    }
		if (pack == 'clt') {
			window.location = 'clt/index.php';
		}	
		if (pack == 'fin') {
			document.getElementById('form1').style.visibility = 'visible';
			document.getElementById('prctab').style.visibility = 'hidden';
		}	
		if (pack == 'for') {
			window.open("http://logtracc.co.nz/forum/index.php","forum");
		}	
		if (pack == 'prc') {
			document.getElementById('form1').style.visibility = 'visible';
			document.getElementById('prctab').style.visibility = 'visible';
		}	
		if (pack == 'doc') {
			window.open("manual/index.php");
		}	
		if (pack == 'lgn') {
			window.open("index.php");
		}	
		if (pack == 'thm') {
			window.open("includes/admin/settheme.php");
		}	
		if (pack == 'adm') {
			var admin = '<?php echo $admin; ?>';
			if (admin == 'Y') {
				window.location = 'admin/index.php';
			} else {
				alert('You are not an authorised System Administrator');
				return false;
			}		
		}	
		
	}

	function blankprc() {
		var x=document.getElementById("process");
		var listlength = document.getElementById("process").length;
		for (var i = 0; i < listlength; i ++) {
			x.remove(x[i]);
		}
	}

	function GetPrc(coy) {
		var cid = '<?php echo $subscriber; ?>';
		blankprc();
		$.get("ajax/ajaxGetPrc.php", {cid:cid,coyid:coy}, function(data){
			$("#process").append(data);
		});
	}

	function verify() {
		var coy = document.getElementById("company").value;
		var prc = document.getElementById('process').value;
		if (coy == "") {
			alert("Please select a Company to work with");
			return false;
		} else {
			jQuery.ajaxSetup({async:false});
			$.get("ajax/ajaxUpdtCompany.php", {coyid: coy}, function(data){
			});
			jQuery.ajaxSetup({async:true});
			if (cpack == 'fin') {
				window.location = 'fin/index.php';
			}
			if (cpack == 'prc' && prc != '') {
				jQuery.ajaxSetup({async:false});
				$.get("ajax/ajaxUpdtPrc.php", {prc: prc}, function(data){
				});
				jQuery.ajaxSetup({async:true});
				window.location = prc+"/index.php";
			}
		}
	}
	

	
	</script>
</head>
<body>
<div id="wrapper">
<div id="mainheader">
  <div id="mainlefttop"><img src="<?php echo $lg55; ?>"  height="55"></div>
  <div id="mainrighttop">


	
		Select Module :
    <select onChange="selectpack(this.value)">
      <?php echo $modules_options; ?>
    </select>
  </div>
</div>
	<table width="970" align="center">
    	<tr>
        	<td align="center"><img src="<?php echo $lg250; ?>"  alt="Logo"></td>
        </tr>	
    </table>

<form name="form1" id="form1" method="post" >
<table width="420" align="center">
<tr>
<td>
  <table id="fintab"  width="400" border="1" align="center" cellpadding="3" cellspacing="1">
    <tr>
      <td colspan="2" class="boxlabelcenter">Select the Company on which you wish to work:- </td>
    </tr>
    <tr>
      <td colspan="2" align="center"><select name="company" id="company" onChange="GetPrc(this.value);">
          <?php echo $file_options; ?>
        </select></td>
    </tr>
    <tr id="prctab">
      <td class="boxlabelleft">Process</td>
      <td><select name="process" id="process">
      </select></td>
    </tr>
    <tr>
      <td colspan="2" align="right"><input type="button" name="Submit" value="Submit" onclick="verify();"></td>
    </tr>
  </table>
</td>
</tr>
</table>
</form>
<div id="footer" style="text-align:center"> © Murray Russell. 2012 - <?php echo $thisyear; ?> </div>
<script>
	document.getElementById('form1').style.visibility = 'hidden';
	document.getElementById('prctab').style.visibility = 'hidden';
	
	var showcoy = "<?php echo $showcoy; ?>";
	if (showcoy == 'F') {
		document.getElementById('form1').style.visibility = 'visible';
		var cpack = 'fin';
	}
	if (showcoy == 'P') {
		document.getElementById('form1').style.visibility = 'visible';
		document.getElementById('prctab').style.visibility = 'visible';
		var cpack = 'prc';
	}
	
</script>
</div>
</body>
</html>
