<?php
session_start();

date_default_timezone_set($_SESSION['s_timezone']);
$ddate = date("d/m/Y");
$ddateh = date("Y-m-d");
$rtf = $_REQUEST['rtf'];
$_SESSION['s_rtfile'] = $rtf;

$usersession = $_SESSION['usersession'];

include_once("../includes/DBClass.php");
$db_trd = new DBClass();

$db_trd->query("select * from sessions where session = :vusersession");
$db_trd->bind(':vusersession', $usersession);
$row = $db_trd->single();
$subid = $row['subid'];
$user_id = $row['user_id'];

$findb = $_SESSION['s_findb'];

switch($rtf) {
	case "rt1":
		$db_trd->query("select ".$findb.".z_rt1 as fl from globals");
		break;
	case "rt2":
		$db_trd->query("select ".$findb.".z_rt2 as fl from globals");
		break;
	case "rt3":
		$db_trd->query("select ".$findb.".z_rt3 as fl from globals");
		break;
	case "rt4":
		$db_trd->query("select ".$findb.".z_rt4 as fl from globals");
		break;
	case "rt5":
		$db_trd->query("select ".$findb.".z_rt5 as fl from globals");
		break;
	case "rt6":
		$db_trd->query("select ".$findb.".z_rt6 as fl from globals");
		break;
}
$row = $db_trd->single();
extract($row);

$db_trd->closeDB();

require_once("../includes/backgrounds.php");
$theme = $_SESSION['deftheme'];


?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

<title>Process Recurring Transactions</title>
<link rel="stylesheet" type="text/css" href="../includes/flatpickr_date/dist/flatpickr.min.css">
<script src="../includes/flatpickr_date/dist/flatpickr.js"></script>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<link type="text/css" href="../includes/jquery/themes/<?php echo $theme; ?>/jquery.ui.all.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="../includes/jquery/themes/ui.jqgrid.css" />
<link rel="stylesheet" type="text/css" media="screen" href="../includes/jquery/themes/ui.multiselect.css"/> 
<link rel="stylesheet" type="text/css" href="../includes/jquery.confirm/jquery.confirm.css" />

<script src="../includes/jquery/js/jquery.js" type="text/javascript"></script>
<script src="../includes/jquery/js/i18n/grid.locale-en.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery-ui-custom.min.js" type="text/javascript"></script>
<script type="text/javascript" src="../includes/jquery/ui/jquery.ui.widget.js"></script>
<script src="../includes/jquery/js/jquery.jqGrid.min.js" type="text/javascript"></script>
<script src="../includes/jquery.confirm/jquery.confirm.js"></script>
<script type="text/javascript" src="includes/ajaxCheckTransDate.js"></script>


<script type="text/javascript" src="js/fin.js"></script>


<script type="text/javascript">

window.name = 'rtgrid';

		

function addtd() {
	rtfile = document.getElementById("rtf").value;
	rtd = document.getElementById("newdate").value;
	$.get("includes/ajaxupdtrtdate.php", {rtfile: rtfile, rtd: rtd}, function(data){$("#rtlist").trigger("reloadGrid")});
	
	document.getElementById('rtfl').style.visibility = 'visible';
	document.getElementById('btdt').style.visibility = 'hidden';
	
}

function addrtTrans() {
	trdate = document.getElementById('newdateh').value;
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	
	window.open('addrtTrans.php?rtdate='+trdate,'trrt','toolbar=0,scrollbars=1,height=500,width=970,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function rt_editline(lineno) {

	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	
	window.open('editrtTrans.php?ln='+lineno,'tred','toolbar=0,scrollbars=1,height=300,width=970,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	
}

function rt_delline(lineno) {
	  
	$.alertbox({
		'title'		: 'Delete Confirmation',
		'message'	: 'Are you sure you want to delete this transaction line?',
		'buttons'	: {
			'Yes'	: {
				'class'	: 'blue',
				'action': function(){
					$.get("includes/ajaxrtdelline.php", {tid: lineno}, function(data){$("#rtlist").trigger("reloadGrid")});
				}
			},
			'No'	: {
				'class'	: 'gray',
				'action': function(){}	// Nothing to do in this case. You can as well omit the action property.
			}
		}
	});
		  
}

function rt_post() {
	fl = '<?php //echo $fl; ?>';
	$.alertbox({
		'title'		: 'Post Confirmation',
		'message'	: 'Are you sure you want to post the transactions from the '+fl+' file?',
		'buttons'	: {
			'Yes'	: {
				'class'	: 'blue',
				'action': function(){
					$.get("includes/ajaxpostrt.php", {}, function(data){
						if (data == 'Y') {
							alert('Posting transactions complete');
							window.close();
						}
					});
				}
			},
			'No'	: {
				'class'	: 'gray',
				'action': function(){}	// Nothing to do in this case. You can as well omit the action property.
			}
		}
	});
}

</script>
</head>
<body>
<form>
  <input type="hidden" name="newdateh" id="newdateh" value=<?php echo $ddateh; ?>>
  <input type="hidden" name="rtf" id="rtf" value=<?php echo $rtf; ?>>

  <table width="960" border="0" cellpadding="3" cellspacing="1" align="center" bgcolor="<?php echo $bgcolor; ?>">
    <tr bgcolor="<?php echo $bghead; ?>">
      <td colspan="3" align="center"><label style="color: <?php echo $thfont; ?>"><strong>Recurring Transactions - <?php echo $fl; ?> </strong></label></td>
    </tr>
      <tr>
        <td class="boxlabel" >Set Transaction Date</td>
      <td><div align="left">
          <input type="Text" id="newdate" name="newdate" value="<?php echo $ddateh; ?>" class="flatpickr" data-alt-input="true" data-alt-format="F j,Y"></a>
        </div></td>
      <td ><div align="right">
          <input type="button" value="Save Transaction Date" name="btdt" id="btdt" onClick="addtd()" >
        </div></td>
	</table>	
    
  <div id="rtfl" style="position:absolute;visibility:hidden;top:52px;left:0px;height:400px;width:960px;background-color:<?php echo $bgcolor ?>;">
    <table>
      <tr>
        <td colspan="2"><?php include "getrt.php"; ?></td>
      </tr>
    </table>
  </div>

 <script>
 	document.getElementById("newdate").flatpickr({
		onChange: function(dateObj, dateStr, instance) {
			ajaxCheckTransDate();	
		}
	});
 </script>
 

    	
</form>
</body>
</html>