<?php
//error_reporting(0); 
session_start();
$usersession = $_SESSION['usersession'];

$ddate = date("d/m/Y");
$hdate = date("Y-m-d");

$admindb = $_SESSION['s_admindb'];
require("../db.php");
mysql_select_db($admindb) or die(mysql_error());
$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);

$subscriber = $sub_id;

$moduledb = $_SESSION['s_cltdb'];
mysql_select_db($moduledb) or die(mysql_error());

$q = "select campaign_id,name from campaigns";
$r = mysql_query($q) or die (mysql_error().$q);
$camp_options = '<option value=" ">Select Campaign</option>';
while ($row = mysql_fetch_array($r)) {
	extract($row);
	$camp_options .= '<option value="'.$campaign_id.'" >'.$name.'</option>';
}
$consfile = "consfile".$user_id;

$ddate = date("d/m/Y");

$theme = $_SESSION['deftheme'];
require_once("../includes/backgrounds.php");


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Consolidated List</title>

<link type="text/css" href="../includes/jquery/themes/<?php echo $theme; ?>/jquery.ui.all.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="../includes/jquery/themes/ui.jqgrid.css" />

<script src="../includes/jquery/js/jquery.js" type="text/javascript"></script>
<script src="../includes/jquery/js/i18n/grid.locale-en.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery.jqGrid.min.js" type="text/javascript"></script>

<script type="text/javascript">

window.name = "consolidatedlist";

var consfile = "<?php echo $consfile; ?>";

function viewmem(uid) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('editmember.php?uid='+uid,'vmem','toolbar=0,scrollbars=1,height=780,width=1140,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

  function list2pdf() {
  var heading = 'Consolidated File';
  var x = 0, y = 0; // default values	
  x = window.screenX +5;
  y = window.screenY +265;	
	  window.open('list2pdf.php?filterfile='+consfile+'&heading='+heading,'listpdf'+consfile,'toolbar=0,scrollbars=1,height=500,width=1020,resizable=yes,left='+x+',screenX='+x+',top='+y+',screenY='+y);
  }


  function xl7() {
  var heading = 'Consolidated File';
  var x = 0, y = 0; // default values	
  x = window.screenX +5;
  y = window.screenY +265;	
	  window.open('xl_maillist.php?filterfile='+consfile+'&heading='+heading,'listxl'+consfile+'&gen='+7,'toolbar=0,scrollbars=1,height=500,width=1020,resizable=yes,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	 // window.open('listxl.php?filterfile='+filterfile+'&heading='+heading,'listxl'+filterfile,'toolbar=0,scrollbars=1,height=500,width=1020,resizable=yes,left='+x+',screenX='+x+',top='+y+',screenY='+y);
  }

function xl3() {
  var heading = 'Consolidated File';
  var x = 0, y = 0; // default values	
  x = window.screenX +5;
  y = window.screenY +265;	
	  window.open('xl_maillist.php?filterfile='+consfile+'&heading='+heading,'listxl'+consfile+'&gen='+3,'toolbar=0,scrollbars=1,height=500,width=1020,resizable=yes,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	 // window.open('listxl.php?filterfile='+filterfile+'&heading='+heading,'listxl'+filterfile,'toolbar=0,scrollbars=1,height=500,width=1020,resizable=yes,left='+x+',screenX='+x+',top='+y+',screenY='+y);
  }



function addcandidates() {
  var camp_id = document.getElementById('campid').value;
  var x = 0, y = 0; // default values	
  x = window.screenX +5;
  y = window.screenY +265;	
	window.open('add2camp.php?filterfile='+consfile+'&camp_id='+camp_id,'a2c','toolbar=0,scrollbars=1,height=10,width=10,resizable=yes,left='+x+',screenX='+x+',top='+y+',screenY='+y);
		
	
}

function delconsol() {
	 if (confirm("Are you sure you want to delete the consolidated list?")) {
		$.get("includes/ajaxdelconsol.php");	
		this.close();
	  }
}

function cbulknotes() {
	x = window.screenX +5;
	y = window.screenY +200;
	window.open("consbulknotes.php","conlist","toolbar=0,scrollbars=1,height=250,width=640,resizable=0,left="+x+",screenX="+x+",top="+y+",screenY="+y);
}

</script>

</head>
<body>
	<table align="left" border="0" cellspacing="0" cellpadding="0" bgcolor="<?php echo $bgcolor; ?>">
        <tr>
            <td colspan="6" align="left"><label style="color: <?php echo $tdfont; ?>; font-size: 14px;">Consolidated File</label></td>
            <td align="left">&nbsp;<input type="button" name="bdelconsol" id="bdelconsol" value="Delete Consolidated List" onclick="delconsol();"/></td>
        </tr>
        <tr>
            <td colspan="7">
                <?php include "getConsolidatedList.php" ?>
            </td>
        </tr>
        <tr>
            <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Add as Candidates to &nbsp;</label></td>
            <td><select name="campid" id="campid"><?php echo $camp_options;?></select></td>
            <td><input type="button" name="btncamp" id="btncamp" value="Add" onclick="addcandidates();" />  </td>
            <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
            <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Send to ... </label></td>
            <td><input name="bxl" type="button" value="Excel" onclick="xl7()" /></td>
           <td><input name="bpdf" type="button" value="PDF" onclick="list2pdf()" />
           <input type="button" name="bnotes" id="bnotes" value="Bulk Note"  onclick="cbulknotes()"/></td>
        </tr>
    </table>

</body>
</html>