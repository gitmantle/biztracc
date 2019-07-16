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

if(isset ($_REQUEST["son"])) {
    $s = jqGridUtils::Strip($_REQUEST["son"]);
	$n = substr($s,3);
	$id = 'QOT'.$n;
} else {
    $id = '';
}

// Create the jqGrid instance
$grid = new jqGridRender($conn);


// the actual query for the grid data
$grid->SelectCommand = "select uid,itemcode,item,unit,quantity,picked from quotelines where ref_no = '".$id."'"; 

// set the ouput format to json
$grid->dataType = 'json';

// Let the grid create the model
$grid->setColModel(null);

// Set the url from where we obtain the data
$grid->setUrl('getsolines.php');


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
$grid->setColProperty("itemcode", array("label"=>"Code", "width"=>45));
$grid->setColProperty("item", array("label"=>"Item", "width"=>70));
$grid->setColProperty("unit", array("label"=>"Unit", "width"=>30));
$grid->setColProperty("quantity", array("label"=>"Qty", "width"=>30));
$grid->setColProperty("picked", array("label"=>"Picked", "width"=>30));
$grid->setColProperty("act", array("label"=>"Actions", "width"=>70));


$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#solineslist").getDataIDs(); 
	for(var i=0;i<ids.length;i++){ 
		var cl = ids[i]; 
		var rowdata = $("#solineslist").getRowData(cl);
			be = '<img src="../images/edit.png" title="Edit Quote" onclick="javascript:editquote('+cl+')" >'; 
			pe = '<img src="../images/printer.gif" title="Print Quote" onclick="javascript:printquote('+rf+')" >'; 
			ee = '<img src="../images/mail.png" title="Email Quote" onclick="javascript:emailquote('+rf+')" >'; 
			se = '<img src="../images/sale-icon.png" title="Convert to Sales Order" onclick="javascript:quote2so('+rf+')" >'; 
		jQuery("#solineslist").setRowData(ids[i],{act:be+'&nbsp;&nbsp;&nbsp;'+pe+'&nbsp;&nbsp;&nbsp;'+ee+'&nbsp;&nbsp;&nbsp;'+se}); 
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
$buttonoptions = array("#solinespager",
    array("buttonicon"=>"ui-icon-plus","caption"=>"","position"=>"first","title"=>"Add a quote.", "onClickButton"=>"js: function(){addquote('".$cn."');}")
);
$grid->callGridMethod("#solineslist", "navButtonAdd", $buttonoptions); 

// Run the script
$grid->renderGrid('#solineslist','#solinespager',true, null, null,true,true,true);

?>




