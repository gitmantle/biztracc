<?php
session_start();
//ini_set('display_errors', true);
require("../db.php");

include 'jq-config.php';

// include the jqGrid Class
require_once "../includes/jquery/php/jqGrid.php";
// include the PDO driver class
require_once "../includes/jquery/php/jqGridPdo.php";
// Connection to the server
$conn = new PDO(DB_DSN,DB_USER,DB_PASSWORD);
// Tell the db that we use utf-8
$conn->query("SET NAMES utf8");
// Get the needed parameters passed from the main grid

if(isset ($_REQUEST["cid"])) {
    $id = jqGridUtils::Strip($_REQUEST["cid"]);
} else {
	$id = 0;	
}
$_SESSION['s_costid'] = $id;


// Create the jqGrid instance
$grid = new jqGridRender($conn);

// the actual query for the grid data
$grid->SelectCommand = "select uid,costid,quantity,item,unitcost,total,gst from costlines where costid = ".$id;

// set the ouput format to json
$grid->dataType = 'json';

// Let the grid create the model
$grid->setColModel(null);

// Set the url from where we obtain the data
$grid->setUrl('getCostlines.php');

// Set some grid options
$grid->setGridOptions(array(
    "rowNum"=>7,
    "sortname"=>"uid",
    "rowList"=>array(7,50,100),
	"height"=>150,
	"width"=>940
    ));

// Enable footerdata an tell the grid to obtain it from the request
$grid->setGridOptions(array("footerrow"=>true,"userDataOnFooter"=>true));

$grid->addCol(array("name"=>"act"),"last");

// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"ID", "width"=>25, "hidden"=>true));
$grid->setColProperty("costid", array("label"=>"CostID", "width"=>25, "hidden"=>true));
$grid->setColProperty("quantity", array("label"=>"Quantity", "width"=>70, "align"=>"right","formatter"=>"number"));
$grid->setColProperty("item", array("label"=>"Item", "width"=>190));
$grid->setColProperty("unitcost", array("label"=>"Unit Cost", "width"=>100, "align"=>"right","formatter"=>"number"));
$grid->setColProperty("total", array("label"=>"Total", "width"=>100, "align"=>"right","formatter"=>"number"));
$grid->setColProperty("gst", array("label"=>"Tax", "width"=>70, "align"=>"right","formatter"=>"number"));
$grid->setColProperty("act", array("label"=>"Actions",  "width"=>50, "sortable"=>false));

// At end call footerData to put total  label
$grid->callGridMethod('#costlineslist', 'footerData', array("item",array("item"=>"Total:")));
// Set which parameter to be sumarized
$summaryrows = array("total"=>array("total"=>"SUM"),"gst"=>array("gst"=>"SUM"));


$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#costlineslist").getDataIDs();
	for(var i=0;i<ids.length;i++){
			var rowd = $("#costlineslist").getRowData(ids[i]);
			var cl = ids[i];
			var costid = rowd.costid;
			be = '<img src="../images/edit.png" title="Edit Transaction Details" onclick="javascript:editcost('+cl+')" ></ids>';
			se = '<img src="../images/delete.png" title="Delete Line" onclick="javascript:delcostline('+cl+')" >'; 
			jQuery("#costlineslist").setRowData(ids[i],{act:be+'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+se});  
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
$buttonoptions = array("#costlineslistpager",
    array("buttonicon"=>"ui-icon-plus","caption"=>"","position"=>"first","title"=>"Add a line", "onClickButton"=>"js: function(){addcostline(".$id.");}")
);
$grid->callGridMethod("#costlineslist", "navButtonAdd", $buttonoptions); 

// Run the script
$grid->renderGrid('#costlineslist','#costlineslistpager',true, $summaryrows, null,true,true);
?>




