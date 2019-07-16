<?php
session_start();
//ini_set('display_errors', true);

$findb = $_SESSION['s_findb'];

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
    $ref = jqGridUtils::Strip($_REQUEST["cid"]);
} else {
    $ref = "";
}

// Create the jqGrid instance
$grid = new jqGridRender($conn);

// the actual query for the grid data
$grid->SelectCommand = "select uid,ref_no,item,quantity,price,unit,value, discount,tax,(value-discount+tax) as total,paid ,((value - discount + tax) - (paid + (price + tax / quantity) * returns)) as outstanding from ".$findb.".invtrans where ref_no = '".$ref."' and ((value - discount + tax) - (paid + (price + tax / quantity) * returns) != 0)";

// set the ouput format to json
$grid->dataType = 'json';

// Let the grid create the model
$grid->setColModel(null);

// Set the url from where we obtain the data
$grid->setUrl('getinvlines.php');

// Set some grid options
$grid->setGridOptions(array(
    "rowNum"=>5,
    "sortname"=>"item",
    "rowList"=>array(5,50,100),
	"height"=>110,
	"width"=>950
    ));


$grid->addCol(array("name"=>"act"),"last");

// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"ID", "width"=>25, "hidden"=>true));
$grid->setColProperty("ref_no", array("label"=>"Reference", "width"=>65));
$grid->setColProperty("item", array("label"=>"Item", "width"=>150));
$grid->setColProperty("quantity", array("label"=>"Quantity", "width"=>70, "align"=>"right","formatter"=>"number"));
$grid->setColProperty("price", array("label"=>"Price", "width"=>70, "align"=>"right","formatter"=>"number"));
$grid->setColProperty("unit", array("label"=>"Unit", "width"=>70));
$grid->setColProperty("value", array("label"=>"Value", "width"=>80, "align"=>"right","formatter"=>"number"));
$grid->setColProperty("discount", array("label"=>"Discount", "width"=>70, "align"=>"right","formatter"=>"number"));
$grid->setColProperty("tax", array("label"=>"Tax", "width"=>70, "align"=>"right","formatter"=>"number"));
$grid->setColProperty("total", array("label"=>"Total", "width"=>80, "align"=>"right","formatter"=>"number"));
$grid->setColProperty("paid", array("label"=>"Paid", "width"=>80, "align"=>"right","formatter"=>"number"));
$grid->setColProperty("outstanding", array("label"=>"Outstanding", "width"=>85, "align"=>"right","formatter"=>"number"));
$grid->setColProperty("act", array("label"=>"Actions",  "width"=>50, "sortable"=>false));

$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#outinvtrans").getDataIDs();
	for(var i=0;i<ids.length;i++){
			var rowd = $("#outinvtrans").getRowData(ids[i]);
			var cl = ids[i];
			var topay = rowd.outstanding;
			if (topay > 0) {
				var refno = "'"+rowd.ref_no+"'";
				var fxcode = "'"+rowd.currency+"'";
				var fxrate = rowd.rate;
				be = '<img src="../images/costs.png" title="Pay in Full" onclick="javascript:payfull('+cl+','+topay+','+refno+','+fxcode+','+fxrate+')" ></ids>';
				se = '<img src="../images/half.png" title="Pay in Part" onclick="javascript:paypart('+cl+','+refno+','+fxcode+','+fxrate+')" ></ids>';
			} else {
				be = '&nbsp;&nbsp';
				se = '&nbsp;&nbsp';
			}
			jQuery("#outinvtrans").setRowData(ids[i],{act:be+'&nbsp;&nbsp;&nbsp;'+se}); 
	}
}
LOADCOMPLETE;

$grid->setGridEvent("loadComplete",$loadevent);
// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>true,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

// Run the script
$grid->renderGrid('#outinvtrans','#outinvtranspager',true, null, null,true,true,true);
?>




