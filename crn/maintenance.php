<?php
session_start();

$vid = $_REQUEST['uid'];
$v = explode('~',$vid);
$_SESSION['s_vehicleno'] = $v[1];

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
   //changeObjectVisibility("rucs","hidden");
   changeObjectVisibility("servicing","hidden");
   changeObjectVisibility("repairs","hidden");
  // changeObjectVisibility("tyres","hidden");
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
        <div id="servicing" style="position:absolute;visibility:visible;top:50px;left:3px;height:500px;width:880px;background-color:<?php echo $bgcolor; ?>;" >
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
    </tr>
   
  </table>
  <script>
		//hideAllm();
		switchDivm('servicing','ge');
    </script>
  <script>document.onkeypress = stopRKey;</script>
</form>

</body>
</html>
