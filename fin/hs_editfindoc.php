<?php
session_start();
//ini_set('display_errors', true);

$usersession = $_SESSION['usersession'];
$type = $_REQUEST['type'];

switch ($type) {
	case 'qot':
		$tp = 'Quote';
		$tfile = 'qottemplate';
		break;
	case 's_o':
		$tp = 'Sales Order';
		$tfile = 's_otemplate';
		break;
	case 'p_o':
		$tp = 'Purchase Order';
		$tfile = 'p_otemplate';
		break;
	case 'grn':
		$tp = 'Goods Received';
		$tfile = 'grntemplate';
		break;
	case 'inv':
		$tp = 'Invoice';
		$tfile = 'invtemplate';
		break;
	case 'c_s':
		$tp = 'Cash Sale';
		$tfile = 'c_stemplate';
		break;
	case 'c_p':
		$tp = 'Cash Purchase';
		$tfile = 'c_ptemplate';
		break;
	case 'rec':
		$tp = 'Receipt';
		$tfile = 'rectemplate';
		break;
	case 'pay':
		$tp = 'Payment';
		$tfile = 'paytemplate';
		break;
	case 'crn':
		$tp = 'Credit Note';
		$tfile = 'crntemplate';
		break;
	case 'ret':
		$tp = 'Goods Returned';
		$tfile = 'rettemplate';
		break;
	case 'req':
		$tp = 'Requisition';
		$tfile = 'reqtemplate';
		break;
	case 'pkl':
		$tp = 'Picking List';
		$tfile = 'pkltemplate';
		break;
	case 'd_n':
		$tp = 'Delivery Note';
		$tfile = 'd_ntemplate';
		break;
		
		
}

$_SESSION['s_tfile'] = $tfile;
$_SESSION['s_findoc'] = $type;

date_default_timezone_set($_SESSION['s_timezone']);

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];
$subscriber = $subid;
$sname = $row['uname'];

$hdate = date('Y-m-d');
$ttime = strftime("%H:%M", time());

$findb = $_SESSION['s_findb'];

$db->closeDB();

$theme = $_SESSION['deftheme'];
require_once("../includes/backgrounds.php");

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Edit Financial Document</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<link type="text/css" href="../includes/jquery/themes/<?php echo $theme; ?>/jquery.ui.all.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="../includes/jquery/themes/ui.jqgrid.css" />
<script src="../includes/jquery/js/jquery.js" type="text/javascript"></script>
<script src="../includes/jquery/js/i18n/grid.locale-en.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery-ui-custom.min.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery.jqGrid.min.js" type="text/javascript"></script>
<script type="text/javascript" src="../includes/jquery/ui/jquery.ui.widget.js"></script>
<script>

window.name = "hs_editfindoc";


// function hideAll()
//  hides a bunch of divs
//

function hideAllm() {
   changeObjectVisibility("system","hidden");
   changeObjectVisibility("boxes","hidden");
   changeObjectVisibility("labels","hidden");
   changeObjectVisibility("grid","hidden");
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


function editproperties(id) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;	
	window.open('hs_editprops.php?id='+id,'edprop','toolbar=0,scrollbars=1,height=670,width=950,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function copyfindoc() {
	var copyfrom = document.getElementById('ldocs').value;
	var copyto = '<?php echo $type; ?>';

	$.get("includes/ajaxcopyfindoc.php", {copyfrom: copyfrom, copyto:copyto}, function(data){
	jQuery("#fdocsystemlist").setGridParam({url:"getfindocsystem.php"}).trigger("reloadGrid"); 
	jQuery("#fdocboxeslist").setGridParam({url:"getfindocboxes.php"}).trigger("reloadGrid"); 
	jQuery("#fdoclabelslist").setGridParam({url:"getfindoclabels.php"}).trigger("reloadGrid"); 
	jQuery("#fdocgridlist").setGridParam({url:"getfindocgrid.php"}).trigger("reloadGrid"); 
	});		
	
}

function previewfindoc() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	var type = '<?php echo $type; ?>';
	window.open('hs_PrintTrading.php?type='+type,'hplpdf','toolbar=0,scrollbars=1,height=670,width=950,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);	
}

function refreshfindocgrid() {
	jQuery("#fdocsystemlist").setGridParam({url:"getfindocsystem.php"}).trigger("reloadGrid"); 
	jQuery("#fdocboxeslist").setGridParam({url:"getfindocboxes.php"}).trigger("reloadGrid"); 
	jQuery("#fdoclabelslist").setGridParam({url:"getfindoclabels.php"}).trigger("reloadGrid"); 
	jQuery("#fdocgridlist").setGridParam({url:"getfindocgrid.php"}).trigger("reloadGrid"); 
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
<form name="edsetup" id="edsetup" method="post">
  <table width="880" border="0" align="left">
    <tr bgcolor="<?php echo $bghead; ?>">
      <td class="boxlabelleft"><label style="color: <?php echo $thfont; ?> "><strong>Edit <?php echo $tp; ?> layout</strong></label></td>
      <td class="boxlabelleft"><label style="color: <?php echo $thfont; ?> "><strong>Copy <?php echo $tp; ?> layout from </strong></label>
      <select name="ldocs" id="ldocs">
      	<option value="qot">Quote</option>
      	<option value="grn">Goods Received</option>
      	<option value="inv">Invoice</option>
      	<option value="c_s">Cash Sale</option>
      	<option value="c_p">Cash Purchase</option>
      	<option value="rec">Receipt</option>
      	<option value="pay">Payment</option>
      	<option value="crn">Credit Note</option>
      	<option value="ret">Goods Returned</option>
      	<option value="req">Requisition</option>
      </select>
      &nbsp;<input name="bcopy" type="button" value="Copy" onClick="copyfindoc()">
      </td>
    </tr>
  </table>
  <table width="620" border="0" align="left">
    <tr>
      <td><script type="text/javascript" src="tabs/findoc_tabs.js"></script></td>
    </tr>
    <tr>
      <td><div id="system" style="position:absolute;visibility:hidden;top:50px;left:3px;height:500px;width:860px;background-color:<?php echo $bgcolor; ?>;" >
          <table width="600" border="0" align="left" cellpadding="3" cellspacing="1">
            <tr>
      			<td><?php include "getfindocsystem.php"; ?></td>
            </tr>
            <tr>
                <td><input name="bedit" type="button" value="Preview Document" onClick="previewfindoc()"></td>
            </tr>
          </table>
        </div>
        <div id="boxes"  style="position:absolute;visibility:visible;top:50px;left:3px;height:500px;width:860px;background-color:<?php echo $bgcolor; ?>;" >
          <table width="600" border="0" align="left" cellpadding="3" cellspacing="1">
          	<tr>
      			<td><?php include "getfindocboxes.php"; ?></td>
            </tr>
            <tr>
                <td><input name="bedit" type="button" value="Preview Document" onClick="previewfindoc()"></td>
            </tr>
          </table>
        </div>
        <div id="labels" style="position:absolute;visibility:hidden;top:50px;left:3px;height:500px;width:860px;background-color:<?php echo $bgcolor; ?>;" >
          <table width="600" border="0" align="left" cellpadding="3" cellspacing="1">
            <tr>
      			<td><?php include "getfindoclabels.php"; ?></td>
            </tr>
            <tr>
                <td><input name="bedit" type="button" value="Preview Document" onClick="previewfindoc()"></td>
            </tr>
          </table>
        </div>
         <div id="grid" style="position:absolute;visibility:hidden;top:50px;left:3px;height:500px;width:860px;background-color:<?php echo $bgcolor; ?>;" >
          <table width="600" border="0" align="left" cellpadding="3" cellspacing="1">
            <tr>
      			<td><?php include "getfindocgrid.php"; ?></td>
            </tr>
            <tr>
                <td><input name="bedit" type="button" value="Preview Document" onClick="previewfindoc()"></td>
            </tr>
          </table>
        </div>
   </tr>
  </table>
  <script>
		//hideAllm();
		switchDivm('system','hd');
    </script>
  <script>document.onkeypress = stopRKey;</script>
</form>

</body>
</html>
