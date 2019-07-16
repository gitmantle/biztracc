<?php
session_start();

date_default_timezone_set($_SESSION['s_timezone']);

$usersession = $_SESSION['usersession'];
$draccno = $_REQUEST['dracc'];

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];

$wiptable = 'ztmp'.$user_id.'_wip';

$findb = $_SESSION['s_findb'];

$db->query("drop table if exists ".$findb.".".$wiptable);
$db->execute();

$db->query("create table ".$findb.".".$wiptable." ( uid int(11) default 0, datestarted date default '0000-00-00', hours decimal(5,2) default 0)  engine myisam");
$db->execute();


// insert into tradetable all records for this sales order from invtrans
$db->query("insert into ".$findb.".".$wiptable." (uid,datestarted,hours) select uid, date(start), hours from ".$findb.".wip where accountno = ".$draccno." and posted = 'No'");
$db->execute();

$db->closeDB();

require_once("../includes/backgrounds.php");
$theme = $_SESSION['deftheme'];


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>WIP List</title>
<link rel="stylesheet" type="text/css" href="../includes/flatpickr_date/dist/flatpickr.min.css">
<script src="../includes/flatpickr_date/dist/flatpickr.js"></script>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<link type="text/css" href="../includes/jquery/themes/<?php echo $theme; ?>/jquery.ui.all.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="../includes/jquery/themes/ui.jqgrid.css" />
<script src="../includes/jquery/js/jquery.js" type="text/javascript"></script>
<script src="../includes/jquery/js/i18n/grid.locale-en.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery-ui-custom.min.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery.jqGrid.min.js" type="text/javascript"></script>
<script type="text/javascript" src="../includes/jquery/ui/jquery.ui.widget.js"></script>
<script type="text/javascript" src="../fin/includes/ajaxgetref.js"></script>
<script>

	window.name = "wiplst";

function post2inv() {
	var aprint = jQuery("#invwiplist").getGridParam('selarrrow');	
	var astring = aprint.toString();
	var ok = "Y";
	if (astring == "") {
		alert("Please select at least one wip line.");
		ok = "N";
		return false;
    }
    
    if (ok == "Y") {
       
          jQuery.ajaxSetup({async:false});  
          var draccno = <?php echo $draccno; ?>;
          $.get("../ajax/ajaxwip2inv.php", {dstring: astring, draccno:draccno}, function(data){window.open("","tr_inv").jQuery("#tradlist").trigger("reloadGrid")});           
          jQuery.ajaxSetup({async:true});  
		      this.close(); 

    }
	
}	


</script>
</head>
<body>
<form name="so" id="so" method="post" >
  <input type="hidden" name="savebutton" id="savebutton" value="N">
  <table  width="950" border="0" cellpadding="3" cellspacing="1" align="center" bgcolor="<?php echo $bgcolor; ?>">
    <tr bgcolor="<?php echo $bghead; ?>">
      <td ><label style="color: <?php echo $thfont; ?>"><strong>WIP List</strong></label></td>
    </tr>
    <tr>
      <td class="boxlabelleft" >Select the WIP items you want invoiced.</td>
    </tr>
    <tr>
        <td> Once in the invoice you may edit them if required.</td>      
    </tr>
    
    <tr>
      <td><?php include "getinvwiplist.php"; ?></td>
    </tr>
    <tr bgcolor="<?php echo $bghead; ?>">
      <td><input name="bSubmit" id="bSubmit" type="button" value="Post" onClick="post2inv()"></td>
    </tr>
  </table>  
  
  
</form>


</body>
</html>
