<?php

$thisyear = date('Y');
$theme = "cupertino";
$bgcolor = "#fdfefe";
$bghead = "#e6f1f9";
$thfont = "#000";
$tdfont = "#000";

?>
<!doctype HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Logtracc Administration</title>
<!-- Deluxe Menu -->
<noscript>
<p><a href="http://deluxe-menu.com">Javascript Menu by Deluxe-Menu.com</a></p>
</noscript>
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
<script type="text/javascript">
window.name = "admin";

$(document).ready(function() {
	$("#tabs").tabs();
	$('#tabs').tabs({closable: true})
});

</script>
<style type="text/css">
#tabs {
	margin-top: 1em;
}
</style>
</head><body>
<div id="wrapper">
  <div id="mainheader">
    <div id="mainlefttop"><img src="../images/lt_logo.png" width="165" height="50"></div>
  </div>
  <script type="text/javascript">



/*
   Deluxe Menu Data File
   Created by Deluxe Tuner v3.5
   http://deluxe-menu.com
*/


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
	["Update Subscribers","javascript:createTab('updtsubscribers.php','Add/Edit Subscribers',0)", "", "", "Add/Edit Subscribers", "", "0", "", "", "", "", ],
	["Financial Administration","", "", "", "Administer Finances", "", "0", "", "", "", "", ],
	["|Subscription Schedule","javascript:createTab('subsched.php','Subscription schedule and Invoicing',0)", "", "", "Subscription schedule and Invoicing details", "", "", "", "", "", "", ],
];

dm_init();


</script>
  </head>
  <div id="tabs">
    <ul>
      <li><a href="#ui-tabs-1">Logtracc Administration</a></li>
    </ul>
    <div id="ui-tabs-1">
      <div style="width: 950px; height: 350px;">
        <table width="950" border="0">
          <tr>
            <td><table>
                <tr>
                  <td colspan="2">&nbsp;</td>
                </tr>
                <tr>
                  <td colspan="2">&nbsp;</td>
                </tr>
                <tr>
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                </tr>
                <tr>
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
              </table></td>
            <td rowspan="2">&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
        </table>
      </div>
    </div>
  </div>
  <div style="background-color: <?php echo $bghead; ?>; height: 30px;" >
    <table align="center" bgcolor="<?php echo $bghead; ?>">
      <tr>
        <td><label style="color: <?php echo $thfont; ?>">Created by Murray Russell - &nbsp; © Murray Russell 2010 - <?php echo $thisyear; ?></label></td>
      </tr>
    </table>
  </div>
</div>
</body>
</html>