<?php

session_start();
$usersession = $_SESSION['usersession'];
$coyid = $_SESSION['s_coyid'];
$_SESSION['s_module'] = 'h_s';

ini_set('display_errors', true);

$admindb = $_SESSION['s_admindb'];

require('../db.php');
mysql_select_db($admindb) or die(mysql_error(). 'here');

$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error().' '.$query);
$row = mysql_fetch_array($result);
extract($row);
$subid = $row[7];
$userid = $row[2];

$moduledb = 'sub'.$subid;
$_SESSION['s_cltdb'] = $moduledb;

$moduledb = 'fin'.$subid.'_'.$coyid;
$_SESSION['s_findb'] = $moduledb;

$moduledb = 'log'.$subid.'_'.$coyid;
$_SESSION['s_logdb'] = $moduledb;

$moduledb = 'h_s'.$subid.'_'.$coyid;
$_SESSION['h_sdb'] = $moduledb;


date_default_timezone_set($_SESSION['s_timezone']);

$q = "select subname,logo,timezone from subscribers where subid = ".$subid;
$r = mysql_query($q) or die(mysql_error().$q);
$qrow = mysql_fetch_array($r);
extract($qrow);
$subscriber = $subname;
$logo = $_SESSION['logo'];

$q = "select coyname from companies where coyid = ".$coyid;
$r = mysql_query($q) or die(mysql_error().$q);
$qrow = mysql_fetch_array($r);
extract($qrow);
$companyname = $coyname;
$_SESSION['s_coyname'] = $companyname;

$_SESSION['s_menutable'] = 'c_menu';


// get theme for this user
$theme = $_SESSION['deftheme'];
require_once("../includes/backgrounds.php");

// get default branch for this user
$query = "select branch as ubranch from access where staff_id = ".$userid." and module = 'prc'";
$result = mysql_query($query) or die(mysql_error().' '.$query);
$qrow = mysql_fetch_array($result);
extract($qrow);
$_SESSION['s_ubranch'] = $ubranch;


$moduledb = $_SESSION['s_prcdb'];
mysql_select_db($moduledb) or die(mysql_error());

//delete temporary tables 
$result = mysql_list_tables($moduledb) or die(mysql_error());

	while ($row = mysql_fetch_row($result)) {
		while ($row = mysql_fetch_row($result)) {
		   if (substr($row[0], 0, 4) == 'ztmp'.$user_id.'_') {
		
			  $tablename = $row[0];
			  $query = "DROP TABLE " . $tablename;
		
			  $status = mysql_query("$query");
		
			  if ($status) {
				$status = "Success";
			  }
		
			  if (!$status){
				  die(mysql_error());
				  $status = "Failure";
			  }
		   }
		}
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
if ($_SESSION['man'] == 'Y') {
	$modules_options .= "<option value=\"man\">Manufacturing</option>";
} else {
	$modules_options .= "<option value=\"xxx\">Manufacturing</option>";
}
$modules_options .="<option value=\"for\">Forum</option>";
$modules_options .="<option value=\"thm\">Change Theme</option>";
$modules_options .="<option value=\"doc\">Documentation</option>";
$modules_options .="<option value=\"adm\">System Administration</option>";
$modules_options .="<option value=\"hom\">Home</option>";

$thisyear = date('Y');


?>

<!doctype HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta content="utf-8" http-equiv="encoding">
<title>Logtracc - Health and Safety Process</title>

<!-- Deluxe Menu -->
<noscript><p><a href="http://deluxe-menu.com">Javascript Menu by Deluxe-Menu.com</a></p></noscript>
<script type="text/javascript">var dmWorkPath = "../includes/clt.files/";</script>
<script type="text/javascript" src="../includes/clt.files/dmenu.js"></script>
<!-- (c) 2009, by Deluxe-Menu.com -->
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<link type="text/css" href="../includes/jquery/themes/<?php echo $theme; ?>/jquery.ui.all.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="../includes/jquery/themes/ui.jqgrid.css" />
<link rel="stylesheet" type="text/css" media="screen" href="../includes/jquery/themes/ui.multiselect.css"/> 

<script src="../includes/jquery/js/jquery.js" type="text/javascript"></script>
<script src="../includes/jquery/js/i18n/grid.locale-en.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery-ui-custom.min.js" type="text/javascript"></script>
<script type="text/javascript" src="../includes/jquery/ui/jquery.ui.core.js"></script>
<script type="text/javascript" src="../includes/jquery/ui/jquery.ui.widget.js"></script>
<script type="text/javascript" src="../includes/jquery/ui/jquery.ui.position.js"></script>
<script type="text/javascript" src="../includes/jquery/ui/jquery.ui.menu.js"></script>
<script type="text/javascript" src="../includes/jquery/external/jquery.bgiframe-2.1.1.js"></script>
<script src="../includes/jquery/js/jquery.jqGrid.min.js" type="text/javascript"></script>
<script type="text/javascript" src="../includes/dr.tabs.js"></script>
<script type="text/javascript" src="../includes/ui.tabs.closable.js"></script>
<script type="text/javascript" src="js/prc.js"></script>


<script type="text/javascript">

$(document).ready(function() {
	$("#tabs").tabs();
	$('#tabs').tabs({closable: true})
});


	function selectpack(pack) {
	
		if (pack == 'xxx') {
			alert('You are not an authorised to use this module');
			return false;
		}
		if (pack == 'clt') {
			window.location = '../clt/index.php';
		}	
		if (pack == 'fin') {
			window.location = '../main.php?finy=F';
		}	
		if (pack == 'hrs') {
			window.location = '../hrs/index.php';
		}	
		if (pack == 'for') {
			window.open("http://www.logtracc.co.nz/forum/index.php","forum");
		}	
		if (pack == 'doc') {
			//window.location = 'forum/index.php';
			window.open("../documentation/index.php");
		}	
		if (pack == 'adm') {
			var admin = '<?php echo $admin; ?>';
			if (admin == 'Y') {
				window.location = '../admin/index.php';
			} else {
				alert('You are not an authorised System Administrator');
				return false;
			}		
		}	
		if (pack == 'hom') {
			window.location = '../main.php';
		}	
		if (pack == 'thm') {
			//window.location = 'forum/index.php';
			window.open("../includes/admin/settheme.php");
		}	
		
	}	
	
</script>

</head>

<body>
<div id="wrapper">

	<div id="mainheader">
		<div id="mainlefttop"><img src="../images/lt_logo_log.png" width="200" height="55">  &nbsp;</div>
		<div id="mainrighttop">Select Module : <select onChange="selectpack(this.value)">
          <?php echo $modules_options; ?>
		</select></div>		
        <label style="font-size: 14px; text-align: right "> <?php echo $subscriber.' - '.$coyname; ?></label>

	</div>
	
<script type="text/javascript">

// -- Deluxe Tuner Style Names
var itemStylesNames=["Top Item",];
var menuStylesNames=["Top Menu",];
// -- End of Deluxe Tuner Style Names

//--- Common
var isHorizontal=1;
var smColumns=1;
var smOrientation=0;
var dmRTL=0;
var pressedItem=-2;
var itemCursor="default";
var itemTarget="_self";
var statusString="link";
var blankImage="../includes/clt.files/blank.gif";
var pathPrefix_img="";
var pathPrefix_link="";

//--- Dimensions
var menuWidth="";
var menuHeight="21px";
var smWidth="";
var smHeight="";

//--- Positioning
var absolutePos=0;
var posX="10px";
var posY="10px";
var topDX=0;
var topDY=1;
var DX=-5;
var DY=0;
var subMenuAlign="left";
var subMenuVAlign="top";

//--- Font
var fontStyle=["normal 11px Trebuchet MS, Tahoma","normal 11px Trebuchet MS, Tahoma"];
var fontColor=["#000000","#000000"];
var fontDecoration=["none","none"];
var fontColorDisabled="#AAAAAA";

//--- Appearance
var menuBackColor="#FFFFFF";
var menuBackImage="";
var menuBackRepeat="repeat";
var menuBorderColor="#B9B9B9";
var menuBorderWidth=1;
var menuBorderStyle="solid";

//--- Item Appearance
var itemBackColor=["#FFFFFF","#A7D7FE"];
var itemBackImage=["",""];
var beforeItemImage=["",""];
var afterItemImage=["",""];
var beforeItemImageW="";
var afterItemImageW="";
var beforeItemImageH="";
var afterItemImageH="";
var itemBorderWidth=0;
var itemBorderColor=["#FCEEB0","#4C99AB"];
var itemBorderStyle=["solid","solid"];
var itemSpacing=1;
var itemPadding="2px 5px 2px 10px";
var itemAlignTop="left";
var itemAlign="left";

//--- Icons
var iconTopWidth=16;
var iconTopHeight=16;
var iconWidth=16;
var iconHeight=16;
var arrowWidth=7;
var arrowHeight=7;
var arrowImageMain=["../includes/clt.files/arrv_white.gif",""];
var arrowWidthSub=0;
var arrowHeightSub=0;
var arrowImageSub=["../includes/clt.files/arr_black.gif","../includes/clt.files/arr_white.gif"];

//--- Separators
var separatorImage="";
var separatorWidth="100%";
var separatorHeight="3px";
var separatorAlignment="left";
var separatorVImage="";
var separatorVWidth="3px";
var separatorVHeight="100%";
var separatorPadding="0px";

//--- Floatable Menu
var floatable=0;
var floatIterations=6;
var floatableX=1;
var floatableY=1;
var floatableDX=15;
var floatableDY=15;

//--- Movable Menu
var movable=0;
var moveWidth=12;
var moveHeight=20;
var moveColor="#DECA9A";
var moveImage="";
var moveCursor="move";
var smMovable=0;
var closeBtnW=15;
var closeBtnH=15;
var closeBtn="";

//--- Transitional Effects & Filters
var transparency="85";
var transition=24;
var transOptions="";
var transDuration=350;
var transDuration2=200;
var shadowLen=3;
var shadowColor="#B1B1B1";
var shadowTop=0;

//--- CSS Support (CSS-based Menu)
var cssStyle=0;
var cssSubmenu="";
var cssItem=["",""];
var cssItemText=["",""];

//--- Advanced
var dmObjectsCheck=0;
var saveNavigationPath=1;
var showByClick=0;
var noWrap=1;
var smShowPause=200;
var smHidePause=1000;
var smSmartScroll=1;
var topSmartScroll=0;
var smHideOnClick=1;
var dm_writeAll=1;
var useIFRAME=0;
var dmSearch=0;

//--- AJAX-like Technology
var dmAJAX=0;
var dmAJAXCount=0;
var ajaxReload=0;

//--- Dynamic Menu
var dynamic=0;

//--- Popup Menu
var popupMode=0;

//--- Keystrokes Support
var keystrokes=0;
var dm_focus=1;
var dm_actKey=113;

//--- Sound
var onOverSnd="";
var onClickSnd="";

var itemStyles = [
    ["itemWidth=92px","itemHeight=21px","itemBackImage=../includes/clt.files/btn_white.gif,../includes/clt.files/btn_white_blue.gif","fontStyle='normal 11px Tahoma','normal 11px Tahoma'","fontColor=#000000,#000000"],
];
var menuStyles = [
    ["menuBackColor=transparent","menuBorderWidth=0","itemSpacing=1","itemPadding=0px 5px 0px 5px"],
];


var menuItems = [
	<?php
		$_SESSION['s_module'] = 'log';
		include('../includes/menuitems.php');
		echo getMenuItems();
	?>
];

dm_init();

</script>

<br>
  <div id="tabs">
      <ul>
          <li><a href="#ui-tabs-1">Health and Safety</a></li>
      </ul>
      <div id="ui-tabs-1">
        <div style="width: 950px; height: 350px;">
          	<table width="950" border="0">
          	  <tr>
          	    <td align="left"><label style="font-size: 14px;"> <?php echo $subscriber.' - '.$coyname; ?></label></td>
          	    <td align="left">Logged in User - <?php echo $uname; ?></td>
       	      </tr>
       	  </table>
        </div>
      </div>
  </div>
	
	
<div id="footer" style="text-align:center">
	 © Murray Russell. 2012 - <?php echo $thisyear; ?>

</div>
</div>

</body>
</html>