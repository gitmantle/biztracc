<?php
session_start();
ini_set('display_errors', true);
require("../db.php");
$coyid = $_SESSION['s_coyid'];
$coyname = $_SESSION['s_coyname'];
$cn = $coyid.'~'.$coyname;

$moduledb = $_SESSION['s_cltdb'];
mysql_select_db($moduledb) or die(mysql_error());

include '../clt/jq-config.php';

// include the jqGrid Class
require_once "../includes/jquery/php/jqGrid.php";
// include the PDO driver class
require_once "../includes/jquery/php/jqGridPdo.php";
// Connection to the server
$conn = new PDO(DB_DSN,DB_USER,DB_PASSWORD);

if(isset ($_REQUEST["mem"])) {
    $id = jqGridUtils::Strip($_REQUEST["mem"]);
} else {
    $id = 0;
}
$_SESSION['s_memberid'] = $id;

// Create the jqGrid instance
$grid = new jqGridRender($conn);


// the actual query for the grid data
$grid->SelectCommand = "select uid,invno,ref_no,ddate,totvalue,coyname,xref from quotes where member_id = ".$id; 

// set the ouput format to json
$grid->dataType = 'json';

// Let the grid create the model
$grid->setColModel(null);

// Set the url from where we obtain the data
$grid->setUrl('getquotes.php');


// Set some grid options
$grid->setGridOptions(array(
    "rowNum"=>12,
    "sortname"=>"invno,uid",
    "rowList"=>array(12,30,50),
	"height"=>296,
	"width"=>640
    ));

$grid->addCol(array("name"=>"act"),"last");


// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"ID", "width"=>25, "hidden"=>true));
$grid->setColProperty("invno", array("label"=>"Invno", "width"=>25, "hidden"=>true));
$grid->setColProperty("member_id", array("label"=>"Member", "width"=>30, "hidden"=>true));
$grid->setColProperty("ref_no", array("label"=>"Reference", "width"=>70));
$grid->setColProperty("ddate", array("label"=>"Date", "width"=>80, "formatter"=>"date", "formatoptions"=>array("srcformat"=> "Y-m-d", "newformat"=>"d/m/Y")));
$grid->setColProperty("totvalue", array("label"=>"Value", "width"=>80, "align"=>"right","formatter"=>"number"));
$grid->setColProperty("coyname", array("label"=>"From Company", "width"=>120));
$grid->setColProperty("xref", array("label"=>"Xref", "width"=>25, "hidden"=>true));
$grid->setColProperty("act", array("label"=>"Actions", "width"=>70));


$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#quotelist").getDataIDs(); 
	for(var i=0;i<ids.length;i++){ 
		var cl = ids[i]; 
		var rowdata = $("#quotelist").getRowData(cl);
		var r = rowdata.ref_no;
		var xr = rowdata.xref;
		var tp = r.substring(0,3);
		var rf = "'"+r+"'";
		if (xr == '' && tp == 'QOT') {
			be = '<img src="../images/edit.png" title="Edit Quote" onclick="javascript:editquote('+cl+')" >'; 
			pe = '<img src="../images/printer.gif" title="Print Quote" onclick="javascript:printquote('+rf+')" >'; 
			ee = '<img src="../images/mail.png" title="Email Quote" onclick="javascript:emailquote('+rf+')" >'; 
			se = '<img src="../images/sale-icon.png" title="Convert to Sales Order" onclick="javascript:quote2so('+rf+')" >'; 
		} 
		if (xr != '' && tp == 'QOT') {
			be = '<img src="../images/into.png" title="View Quote" onclick="javascript:viewquote('+cl+')" >'; 
			pe = ' '; 
			ee = ' '; 
			se = ' '; 
		}
		if (xr == '' && tp == 'S_O') {
			be = '<img src="../images/into.png" title="View Sales Order" onclick="javascript:views_o('+cl+')" >'; 
			pe = '<img src="../images/printer.gif" title="Print Sales Order" onclick="javascript:prints_o('+rf+')" >'; 
			ee = ' '; 
			se = ' '; 
		} 
		
		jQuery("#quotelist").setRowData(ids[i],{act:be+'&nbsp;&nbsp;&nbsp;'+pe+'&nbsp;&nbsp;&nbsp;'+ee+'&nbsp;&nbsp;&nbsp;'+se}); 
	}
}
LOADCOMPLETE;

$grid->setGridEvent("loadComplete",$loadevent);

// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>true,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));
// add a custom button via the build in callGridMethod
// note the js: before the function
$buttonoptions = array("#quotepager",
    array("buttonicon"=>"ui-icon-plus","caption"=>"","position"=>"first","title"=>"Add a quote.", "onClickButton"=>"js: function(){addquote('".$cn."');}")
);
$grid->callGridMethod("#quotelist", "navButtonAdd", $buttonoptions); 

// Run the script
$grid->renderGrid('#quotelist','#quotepager',true, null, null,true,true,true);

?>




