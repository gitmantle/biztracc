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
$grid->SelectCommand = "select uid,ref_no,item,quantity,price,unit,value,tax,(value+tax) as total,paid,(value+tax-paid) - (returns*price*(1+(taxpcent/100))) as outstanding  from ".$findb.".invtrans where ref_no = '".$ref."'";

// set the ouput format to json
$grid->dataType = 'json';

// Let the grid create the model
$grid->setColModel(null);

// Set the url from where we obtain the data
$grid->setUrl('getgrnlines.php');

// Set some grid options
$grid->setGridOptions(array(
    "rowNum"=>5,
    "sortname"=>"item",
    "rowList"=>array(5,50,100),
	"height"=>100,
	"width"=>950
    ));


$grid->addCol(array("name"=>"act"),"last");

// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"ID", "width"=>25, "hidden"=>true));
$grid->setColProperty("ref_no", array("label"=>"Reference", "width"=>65));
$grid->setColProperty("item", array("label"=>"Item", "width"=>200));
$grid->setColProperty("quantity", array("label"=>"Quantity", "width"=>80, "formatter"=>"number","formatoptions"=>array("thousandsSeparator"=>",","decimalPlaces"=>3)));
$grid->setColProperty("price", array("label"=>"Price", "width"=>100, "formatter"=>"number","formatoptions"=>array("thousandsSeparator"=>",","decimalPlaces"=>2)));
$grid->setColProperty("unit", array("label"=>"Unit", "width"=>100));
$grid->setColProperty("value", array("label"=>"Value", "width"=>100, "formatter"=>"number","formatoptions"=>array("thousandsSeparator"=>",","decimalPlaces"=>2)));
$grid->setColProperty("tax", array("label"=>"Tax", "width"=>100, "formatter"=>"number","formatoptions"=>array("thousandsSeparator"=>",","decimalPlaces"=>2)));
$grid->setColProperty("total", array("label"=>"Total", "width"=>100, "formatter"=>"number","formatoptions"=>array("thousandsSeparator"=>",","decimalPlaces"=>2)));
$grid->setColProperty("paid", array("label"=>"Paid", "width"=>100, "formatter"=>"number","formatoptions"=>array("thousandsSeparator"=>",","decimalPlaces"=>2)));
$grid->setColProperty("outstanding", array("label"=>"Outstanding", "width"=>100, "formatter"=>"number","formatoptions"=>array("thousandsSeparator"=>",","decimalPlaces"=>2)));
$grid->setColProperty("act", array("label"=>"Actions",  "width"=>50, "sortable"=>false));

$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#outgrntrans").getDataIDs();
	for(var i=0;i<ids.length;i++){
			var rowd = $("#outgrntrans").getRowData(ids[i]);
			var cl = ids[i];
			var topay = rowd.outstanding;
			if (topay > 0) {
				var refno = "'"+rowd.ref_no+"'";
				be = '<img src="../images/costs.png" title="Pay in Full" onclick="javascript:payfullp('+cl+','+topay+','+refno+')" ></ids>';
				se = '<img src="../images/half.png" title="Pay in Part" onclick="javascript:paypartp('+cl+','+refno+')" ></ids>';
			} else {
				be = '&nbsp;&nbsp';
				se = '&nbsp;&nbsp';
			}
			jQuery("#outgrntrans").setRowData(ids[i],{act:be+'&nbsp;&nbsp;&nbsp;'+se}); 
	}
}
LOADCOMPLETE;

$grid->setGridEvent("loadComplete",$loadevent);
// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>true,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

// Run the script
$grid->renderGrid('#outgrntrans','#outgrntranspager',true, null, null,true,true,true);
?>




