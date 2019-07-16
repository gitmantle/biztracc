<?php
session_start();
//ini_set('display_errors', true);

$usersession = $_SESSION['usersession'];
$vid = $_REQUEST['uid'];
$coyname = $_SESSION['s_coyname'];

$admindb = $_SESSION['s_admindb'];
date_default_timezone_set($_SESSION['s_timezone']);

require("../db.php");
mysql_select_db($admindb) or die(mysql_error());
$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);

$subscriber = $subid;
$sname = $uname;
$cluid = $user_id;

$serialtable = 'ztmp'.$user_id.'_vtyres';

$moduledb = $_SESSION['s_logdb'];
mysql_select_db($moduledb) or die(mysql_error());

$q = "select regno,branch,vehicleno from vehicles where cost_centre = '".$vid."'";
$r = mysql_query($q) or die(mysql_error());
$row = mysql_fetch_array($r);
extract($row);
$rn = $regno;
$br = $branch;
$vn = $vehicleno;

$_SESSION['s_vehicleno'] = $vn;

$moduledb = $_SESSION['s_findb'];
mysql_select_db($moduledb) or die(mysql_error());

$query = "drop table if exists ".$serialtable;
$result = mysql_query($query) or die(mysql_error());

$query = "create table ".$serialtable." ( itemcode varchar(30) default '',item varchar(100) default '',serialno varchar(50) default '',ddate date,activity varchar(50),ref_no varchar(15))  engine myisam";
$calc = mysql_query($query) or die(mysql_error());


$q = "insert into ".$serialtable." select itemcode,item,serialno,date,activity,ref_no from stkserials where stkserials.branch = '".$br."'";
$r = mysql_query($q) or die(mysql_error().' '.$q);

// Add uid
$q = "alter table ".$serialtable." add `uid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY";
$r = mysql_query($q) or die(mysql_error().' '.$q);

// populate vehicles drop down
$query = "select branch,branchname from branch where branchname like 'Tr%'";
$result = mysql_query($query) or die(mysql_error().$query);
$truck_options = "<option value=\" \">Select Vehicle</option>";
while ($row = mysql_fetch_array($result)) {
	extract($row);
	if ($branch.'~'.$branchname == $cid) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}
	$truck_options .= '<option value="'.$branch.'~'.$branchname.'"'.$selected.'>'.$branch.'~'.$branchname.'</option>';
}


$findb = $_SESSION['s_logdb'];
mysql_select_db($findb) or die(mysql_error());


$theme = $_SESSION['deftheme'];
require_once("../includes/backgrounds.php");

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Maintenance</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<link type="text/css" href="../includes/jquery/themes/<?php echo $theme; ?>/jquery.ui.all.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="../includes/jquery/themes/ui.jqgrid.css" />
<script src="../includes/jquery/js/jquery.js" type="text/javascript"></script>
<script src="../includes/jquery/js/i18n/grid.locale-en.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery-ui-custom.min.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery.jqGrid.min.js" type="text/javascript"></script>
<script type="text/javascript" src="../includes/jquery/ui/jquery.ui.widget.js"></script>
<script>

window.name = "maintenance";

/**
 * DHTML date validation script. Courtesy of SmartWebby.com (http://www.smartwebby.com/dhtml/)
 */

// function hideAll()
//  hides a bunch of divs
//

function hideAllm() {
   changeObjectVisibility("rucs","hidden");
   changeObjectVisibility("servicing","hidden");
   changeObjectVisibility("repairs","hidden");
   changeObjectVisibility("tyres","hidden");
}

function stopRKey(evt) {
  var evt = (evt) ? evt : ((event) ? event : null);
  var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
  if ((evt.keyCode == 13) && (node.type=="text"))  {return false;}
}



// function getStyleObject(string) -> returns style object
//  given a string containing the id of an object
//  the function returns the stylesheet of that object
//  or false if it can't find a stylesheet.  Handles
//  cross-browser compatibility issues.
//
function getStyleObject(objectId) {
  // checkW3C DOM, then MSIE 4, then NN 4.
  //
  if(document.getElementById && document.getElementById(objectId)) {
	return document.getElementById(objectId).style;
   }
   else if (document.all && document.all(objectId)) {  
	return document.all(objectId).style;
   } 
   else if (document.layers && document.layers[objectId]) { 
	return document.layers[objectId];
   } else {
	return false;
   }
}


function changeObjectVisibility(objectId, newVisibility) {
    // first get a reference to the cross-browser style object 
    // and make sure the object exists
    var styleObject = getStyleObject(objectId);
    if(styleObject) {
	styleObject.visibility = newVisibility;
	return true;
    } else {
	// we couldn't find the object, so we can't change its visibility
	return false;
    }
}

function switchDivm(div_id,cell) {
  var style_sheet = getStyleObject(div_id);
  
  if (style_sheet)  {
	hideAllm();
    changeObjectVisibility(div_id,"visible");
  }
}

function addrucdetails() {
var x = 0, y = 0; // default values	
 x = window.screenX +5;
  y = window.screenY +265;
		
	window.open('addrucdetails.php','adruc','toolbar=0,scrollbars=1,height=250,width=650,resizeable=yes,left='+x+',screenX='+x+',top='+y+',screenY='+y);
} 

function editrucdetails(uid) {
var x = 0, y = 0; // default values	
 x = window.screenX +5;
  y = window.screenY +265;
		
	window.open('editrucdetails.php?uid='+uid,'edruc','toolbar=0,scrollbars=1,height=250,width=650,resizeable=yes,left='+x+',screenX='+x+',top='+y+',screenY='+y);
} 

function delrucdetails(uid) {
	  if (confirm("Are you sure you want to delete this record")) {
		$.get("includes/ajaxdelruc.php", {tid: uid}, function(data){$("#ruclist").trigger("reloadGrid")});
	  }
	  
}

function addrucrefund(rlic) {
var x = 0, y = 0; // default values	
 x = window.screenX +5;
  y = window.screenY +265;
		
	window.open('addrucrefund.php?rlic='+rlic,'addrucref','toolbar=0,scrollbars=1,height=400,width=700,resizeable=yes,left='+x+',screenX='+x+',top='+y+',screenY='+y);
} 

function addservicea() {
var x = 0, y = 0; // default values	
 x = window.screenX +5;
  y = window.screenY +5;
		
	window.open('addservicea.php','adsrv','toolbar=0,scrollbars=1,height=750,width=990,resizeable=yes,left='+x+',screenX='+x+',top='+y+',screenY='+y);
} 

function addserviceb() {
var x = 0, y = 0; // default values	
 x = window.screenX +5;
  y = window.screenY +5;
		
	window.open('addserviceb.php','adsrv','toolbar=0,scrollbars=1,height=750,width=990,resizeable=yes,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function addservicec() {
var x = 0, y = 0; // default values	
 x = window.screenX +5;
  y = window.screenY +5;
		
	window.open('addservicec.php','adsrv','toolbar=0,scrollbars=1,height=750,width=990,resizeable=yes,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function editservice(id,tp) {
var x = 0, y = 0; // default values	
 x = window.screenX +5;
  y = window.screenY +5;
	if (tp == 'A') {
		window.open('editservicea.php?id='+id,'edsrva','toolbar=0,scrollbars=1,height=750,width=990,resizeable=yes,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	}
	if (tp == 'B') {
		window.open('editserviceb.php?id='+id,'edsrvb','toolbar=0,scrollbars=1,height=750,width=990,resizeable=yes,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	}
	if (tp == 'C') {
		window.open('editservicec.php?id='+id,'edsrvc','toolbar=0,scrollbars=1,height=750,width=990,resizeable=yes,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	}
} 

function addrepair() {
var x = 0, y = 0; // default values	
 x = window.screenX +5;
  y = window.screenY +5;
		
	window.open('addrepairs.php','adrep','toolbar=0,scrollbars=1,height=750,width=990,resizeable=yes,left='+x+',screenX='+x+',top='+y+',screenY='+y);
} 

function editrepair(id) {
var x = 0, y = 0; // default values	
 x = window.screenX +5;
  y = window.screenY +5;
		
	window.open('editrepairs.php?id='+id,'edrep','toolbar=0,scrollbars=1,height=750,width=990,resizeable=yes,left='+x+',screenX='+x+',top='+y+',screenY='+y);
} 


function realloctyre(id,itemid,sno) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	
	window.open('realloctyre.php?id='+id+'&itemid='+itemid+'&sno='+sno,'tyr','toolbar=0,scrollbars=1,height=250,width=500,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function printsheet(id) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('service2pdf.php?id='+id,'svrpdf','toolbar=0,scrollbars=1,height=670,width=950,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	
}

function printrepair(id) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('repair2pdf.php?id='+id,'reppdf','toolbar=0,scrollbars=1,height=670,width=950,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	
}

function post() {

	//add validation here if required.
	var lname = document.getElementById('coyname').value;
	var ok = "Y";
	if (lname == "") {
		alert("Please enter a Company name.");
		ok = "N";
		return false;
	}
	
	if (ok == "Y") {
		document.getElementById('savebutton').value = "Y";
		document.getElementById('sm').submit();
	}
	
	
}

</script>
<!-- Deluxe Tabs -->
<noscript>
<a href="http://deluxe-tabs.com">Javascript Tabs Menu by Deluxe-Tabs.com</a>
</noscript>
<script type="text/javascript" src="tabs/client_tabs.files/dtabs.js"></script>
<!-- (c) 2009, http://deluxe-tabs.com -->
<style type="text/css">
<!--
.style1 {
	font-size: large
}
.star {
	color: #F00;
}
-->
</style>
</head><body>
<form name="sm" id="sm" method="post">
  <table width="900" border="0" align="left">
    <tr bgcolor="<?php echo $bghead; ?>">
      <td class="boxlabelleft"><label style="color: <?php echo $thfont; ?> "><strong><?php echo $coyname; ?> - <?php echo $vn; ?> - <?php echo $rn; ?></strong></label></td>
    </tr>
  </table>
  <table width="950" border="0" align="left">
    <tr>
      <td><script type="text/javascript" src="tabs/maint_tabs.js"></script></td>
    </tr>
    <tr>
      <td>
        <div id="rucs" style="position:absolute;visibility:hidden;top:50px;left:3px;height:500px;width:880px;background-color:<?php echo $bgcolor; ?>;" >
          <table width="940" border="0" align="center" cellpadding="3" cellspacing="1">
            <tr>
      			<td><?php include "getruc.php"; ?></td>
            </tr>
          </table>
        </div>
        <div id="servicing" style="position:absolute;visibility:hidden;top:50px;left:3px;height:500px;width:880px;background-color:<?php echo $bgcolor; ?>;" >
          <table width="940" border="0" align="center" cellpadding="3" cellspacing="1">
          <tr>
      			<td><?php include "getservice.php"; ?></td>
            </tr>
            <tr>
            	<td><input type="button" name="bserva" id="bserva" value="Add A Service" onclick="addservicea();">&nbsp;&nbsp;&nbsp;&nbsp;
          		<input type="button" name="bserveb" id="bserveb" value="Add B Service" onclick="addserviceb();">&nbsp;&nbsp;&nbsp;&nbsp;
          		<input type="button" name="bservec" id="bservec" value="Add C Service" onclick="addservicec();">
				</td>
            </tr>
          </table>
        </div>
        <div id="repairs"  style="position:absolute;visibility:hidden;top:50px;left:3px;height:500px;width:880px;background-color:<?php echo $bgcolor; ?>;" >
          <table width="940" border="0" align="center" cellpadding="3" cellspacing="1">
            <tr>
      			<td><?php include "getrepairs.php"; ?></td>
            </tr>
          </table>
        </div>
        <div id="tyres" style="position:absolute;visibility:hidden;top:50px;left:3px;height:500px;width:880px;background-color:<?php echo $bgcolor; ?>;" >
          <table width="940" border="0" align="center" cellpadding="3" cellspacing="1">
            <tr>
      			<td><?php include "getvtyres.php"; ?></td>
            </tr>
          </table>
        </div>
    </tr>
   
  </table>
  <script>
		//hideAllm();
		switchDivm('rucs','ge');
    </script>
  <script>document.onkeypress = stopRKey;</script>
</form>

</body>
</html>
