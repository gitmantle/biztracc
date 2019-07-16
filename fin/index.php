<?php
session_start();
$usersession = $_SESSION['usersession'];
$dbase = $_SESSION['s_admindb'];
$coyid = $_SESSION['s_coyid'];
$coytaxyear = $_SESSION['s_coytaxyear'];
$dbprefix = $_SESSION['s_dbprefix'];
$_SESSION['s_module'] = 'fin';

ini_set('display_errors', true);

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$userid = $row['user_id'];
$username = $row['uname'];

// get Google Calendar login details for user.
$db->query("select ug_user,ug_pwd from users where uid = :userid");
$db->bind(':userid', $userid);
$row = $db->single();
extract($row);

if (trim($ug_user) == "" || trim($ug_pwd) == "") {
	$glogin = 'N';
} else {
	$glogin = 'Y';
}

$db->query("select subname,logo250,logo55,timezone from subscribers where subid = :vsubscriber");
$db->bind(':vsubscriber', $subid);
$row = $db->single();
$lg250 = $row['logo250'];
$lg55 = $row['logo55'];
$subscriber = $row['subname'];
$timezone = $row['timezone'];

$db->query("select coyname from companies where coyid = :coyid");
$db->bind(':coyid', $coyid);
$row = $db->single();
extract($row);
$companyname = $coyname;

$_SESSION['s_coyname'] = $companyname;

$_SESSION['s_menutable'] = 'c_menu';

// get default branch for this user
$db->query("select `branch` as ubranch from access where staff_id = :userid and module = :module");
$db->bind(':module', 'fin');
$db->bind(':userid', $userid);
$row = $db->single();
$numrw = $db->rowCount();
if ($numrw > 0) {
	extract($row);
	$_SESSION['s_ubranch'] = $ubranch;
} 

if ($coytaxyear == '0000') {
	$moduledb = $dbprefix.'sub'.$subid;
} else {
	$moduledb = $dbprefix.'sub'.$subid.'_'.$coytaxyear;
}
$_SESSION['s_cltdb'] = $moduledb;

// populate modules dropdown
$modules_options = 	"<option value=\"\">Modules</option>";
if ($_SESSION['clt'] == 'Y') {
	$modules_options .= "<option value=\"clt\">Business Relationship Management</option>";
} else {
	$modules_options .= "<option value=\"xxx\">Business Relationship Management</option>";
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
$modules_options .="<option value=\"thm\">Change Theme</option>";
$modules_options .="<option value=\"doc\">Documentation</option>";
$modules_options .="<option value=\"adm\">System Administration</option>";
$modules_options .="<option value=\"hom\">Home</option>";
$modules_options .="<option value=\"lgn\">Login</option>";

$thisyear = date('Y');

if ($coytaxyear == '0000') {
	$moduledb = $dbprefix.'fin'.$subid.'_'.$coyid;
} else {
	$moduledb = $dbprefix.'fin'.$subid.'_'.$coyid.'_'.$coytaxyear;
}

$_SESSION['s_findb'] = $moduledb;
$findb = $moduledb;

$db->query("select logo from ".$findb.".globals");
$row = $db->single();
extract($row);
$lg = $logo;

$db->query("select branch,subac,stock,bedate,yrdate,gstno,acc,stk,fas,prd,country,tradtax from ".$findb.".globals");
$row = $db->single();
extract($row);

$_SESSION['subac'] = $subac;
$_SESSION['stock'] = $stock;
$_SESSION['bedate'] = $bedate;
$_SESSION['yrdate'] = $yrdate;
$_SESSION['gstno'] = $gstno;
$_SESSION['stk'] = $stk;
$_SESSION['acc'] = $acc;
$_SESSION['fas'] = $fas;
$_SESSION['prd'] = $prd;
$_SESSION['country'] = $country;
$_SESSION['s_tradtax'] = $tradtax;
	
$_SESSION['userip'] = trim(str_replace('.','x',$_SERVER['REMOTE_ADDR']));
$_SESSION['tbno'] = 0;
$_SESSION['plno'] = 0;
$_SESSION['bsno'] = 0;
$_SESSION['a1no'] = 0;
$_SESSION['asno'] = 0;
$_SESSION['fano'] = 0;
$_SESSION['stno'] = 0;

// set local currency
$db->query("select currency from ".$findb.".forex where def_forex = 'Yes'");
$row = $db->single();
extract($row);
$_SESSION['s_localcurrency'] = $currency;

$db->closeDB();

// get theme for this user
$theme = $_SESSION['deftheme'];
require_once("../includes/backgrounds.php");

?>

<!doctype HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Financial Management</title>

<!-- Deluxe Menu -->
<noscript><p><a href="http://deluxe-menu.com">Javascript Menu by Deluxe-Menu.com</a></p></noscript>
<script type="text/javascript">var dmWorkPath = "../includes/clt.files/";</script>
<script type="text/javascript" src="../includes/clt.files/dmenu.js"></script>
<!-- (c) 2009, by Deluxe-Menu.com -->

<link rel="stylesheet" type="text/css" href="../includes/flatpickr_date/dist/flatpickr.min.css">
<script src="../includes/flatpickr_date/dist/flatpickr.js"></script>

<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<link type="text/css" href="../includes/jquery/themes/<?php echo $theme; ?>/jquery.ui.all.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="../includes/jquery/themes/ui.jqgrid.css" />
<link rel="stylesheet" type="text/css" media="screen" href="../includes/jquery/themes/ui.multiselect.css"/> 

<script src="../includes/jquery/js/jquery.js" type="text/javascript"></script>

<script src="../includes/jquery/js/jquery-ui-custom.min.js" type="text/javascript"></script>
<script type="text/javascript" src="../includes/jquery/ui/jquery.ui.core.js"></script>
<script type="text/javascript" src="../includes/jquery/ui/jquery.ui.widget.js"></script>
<script type="text/javascript" src="../includes/jquery/ui/jquery.ui.position.js"></script>
<script type="text/javascript" src="../includes/jquery/ui/jquery.ui.menu.js"></script>
<script type="text/javascript" src="../includes/jquery/external/jquery.bgiframe-2.1.1.js"></script>
<script src="../includes/jquery/js/jquery.jqGrid.min.js" type="text/javascript"></script>

<script src="../includes/jquery/js/i18n/grid.locale-en.js" type="text/javascript"></script>

<script type="text/javascript" src="../includes/dr.tabs.js"></script>
<script type="text/javascript" src="../includes/ui.tabs.closable.js"></script>
<script type="text/javascript" src="js/fin.js"></script>



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
		if (pack == 'hrs') {
			window.location = '../hrs/index.php';
		}	
		if (pack == 'prc') {
			window.location = '../main.php?finy=P';
		}	
		if (pack == 'for') {
			window.open("http://biztracc.com/smf/index.php","forum");
		}	
		if (pack == 'doc') {
			//window.location = 'forum/index.php';
			window.open("../manual/index.php");
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
		if (pack == 'lgn') {
			window.location = '../index_login.php';
		}	
		if (pack == 'thm') {
			//window.location = 'forum/index.php';
			window.open("../includes/admin/settheme.php");
		}	
		
	}	

	function startclock() {
		$.get("../ajax/ajaxstartclock.php", {}, function(data){
			document.getElementById('btnstart').style.visibility = 'hidden';
			document.getElementById('btnstop').style.visibility = 'visible';
				});
	}

	function stopclock(){
		$.get("../ajax/ajaxstopclock.php", {}, function(data){
			document.getElementById('btnstart').style.visibility = 'visible';
			document.getElementById('btnstop').style.visibility = 'hidden';	
				});
	}	
	
</script>

</head>

<body>
<div id="wrapper">

	<div id="mainheader">
  		<div id="mainlefttop"><img src="<?php echo $lg; ?>" height="55"></div>
		<div id="mainrighttop">
		<?php
				if ($username == "Robyn Mills" && $subid != 45) {
					echo '<input type="button" name="btnstart" id="btnstart" value="Start Clock" onclick="startclock();">';
					echo '<input type="button" name="btnstop" id="btnstop" value="Stop Clock" onclick="stopclock();">';
					echo '&nbsp;';
					echo '<script>';
					echo "document.getElementById('btnstop').style.visibility = 'hidden';";
					echo '</script>';
				}
		?>	
			
		Select Module : <select onChange="selectpack(this.value)"><?php echo $modules_options; ?></select></div>	
        <div id="maincoyname"><?php echo $coyname; ?></div>
        <div id="mainmodule">Financial Management</div>        
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
		$_SESSION['s_module'] = 'fin';
		include('../includes/menuitems.php');
		echo getMenuItems();
	?>
];

dm_init();

</script>

<br>
  <div id="tabs">
      <ul>
          <li><a href="#ui-tabs-1">Financial Management</a></li>
      </ul>
      <div id="ui-tabs-1">
        <div style="width: 950px; height: 350px;">
          	<table width="950" border="0">
          	  <tr>
          	    <td align="left"><label style="font-size: 14px;"> <?php echo $subscriber.' - '.$coyname; ?></label></td>
          	    <td align="left">Logged in User - <?php echo $uname; ?></td>
          	    <td align="left"><img src="../images/todo.gif" width="16" height="16" alt="ToDo" title="ToDo List" onClick="todo()"></td>
       	      </tr>
          	  <tr>
            	<td align="center" colspan="2"><?php include "../includes/gettasks.php";?></td>
                <td align="center"><?php include "../includes/getvlinks.php";?></td>
       	      </tr>
       	  </table>
        </div>
      </div>
  </div>
	
	
<div id="footer" style="text-align:center">
	 © Murray Russell 2010 - <?php echo $thisyear; ?>

</div>
</div>

</body>
</html>