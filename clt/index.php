<?php
session_start();
$usersession = $_SESSION['usersession'];
$dbase = $_SESSION['s_admindb'];
$dbprefix = $_SESSION['s_dbprefix'];
$_SESSION['s_module'] = 'clt';

ini_set('display_errors', true);

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];
$subscriber = $subid;
$sname = $row['uname'];

$db->query("select subname,logo55,timezone from subscribers where subid = ".$subid);
$row = $db->single();
extract($row);
$subscriber = $subname;
$lg = '../'.$logo55;

$moduledb = $dbprefix.'sub'.$subid;
$_SESSION['s_cltdb'] = $moduledb;
$_SESSION['s_menutable'] = 'c_menu';

$cltdb = $_SESSION['s_cltdb'];

$module = 'clt';
$compid = 0;


//Delete temporary files
$dir = "text";
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

$h=opendir($dir);

while($file=readdir($h))
{
	if(substr($file,0,3)=='pdf' and substr($file,-4)=='.txt')
	{
		$path=$dir.'/'.$file;
	
		if($t-filemtime($path)>3600)
			@unlink($path);
	}
}
closedir($h);

// populate modules dropdown
$modules_options = 	"<option value=\"\">Modules</option>";
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
$modules_options .="<option value=\"thm\">Change Theme</option>";
$modules_options .="<option value=\"doc\">Documentation</option>";
$modules_options .="<option value=\"adm\">System Administration</option>";
$modules_options .="<option value=\"hom\">Home</option>";
$modules_options .="<option value=\"lgn\">Login</option>";

$thisyear = date('Y');

// get theme for this user
$theme = $_SESSION['deftheme'];
require_once("../includes/backgrounds.php");

$db->closeDB();

?>

<!doctype HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Business Relationships Management</title>

<!-- Deluxe Menu -->
<noscript>
<p><a href="http://deluxe-menu.com">Javascript Menu by Deluxe-Menu.com</a></p>
</noscript>

<link rel="stylesheet" type="text/css" href="../includes/flatpickr_date/dist/flatpickr.min.css">
<script src="../includes/flatpickr_date/dist/flatpickr.js"></script>

<script type="text/javascript">var dmWorkPath = "../includes/clt.files/";</script>
<script type="text/javascript" src="../includes/clt.files/dmenu.js"></script>
<!-- (c) 2009, by Deluxe-Menu.com -->
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<link type="text/css" href="../includes/jquery/themes/<?php echo $theme; ?>/jquery.ui.all.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="../includes/jquery/themes/ui.jqgrid.css" />
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
<script type="text/javascript" src="js/clt.js"></script>


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
		if (pack == 'fin') {
			window.location = '../main.php?finy=F';
		}	
		if (pack == 'hrs') {
			window.location = '../hrs/index.php';
		}	
		if (pack == 'for') {
			window.open("http://biztracc.com/smf/index.php","forum");
		}	
		if (pack == 'doc') {
			//window.location = 'forum/index.php';
			window.open("../manual/index.php");
		}
		if (pack == 'prc') {
			window.location = '../main.php?finy=P';
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
	
</script>

</head>

<body>
<div id="wrapper">

	<div id="mainheader">
		<div id="mainlefttop"><img src="<?php echo $lg; ?>" height="55">  &nbsp;</div>
		<div id="mainrighttop">Select Module : <select onChange="selectpack(this.value)"><?php echo $modules_options; ?></select></div>	
        <div id="maincoyname"><?php echo $subscriber; ?></div>
        <div id="mainmodule">Business Relationships</div>        
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
		$_SESSION['s_module'] = 'clt';
		$_SESSION['s_menutable'] = 'c_menu';		
		include('../includes/menuitems.php');
		echo getMenuItems();
	?>
];

dm_init();

</script>


<br>

  <div id="tabs">
      <ul>
          <li><a href="#ui-tabs-1">Business Relationship Management</a></li>
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
	
	
<div id="footer"; align="center">
	 © Murray Russell 2010 - <?php echo $thisyear; ?>

</div>
</div>

</body>
</html>