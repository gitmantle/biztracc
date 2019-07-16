<?php
session_start();
//ini_set('display_errors', true);
require("../db.php");

$usersession = $_SESSION['usersession'];

$admindb = $_SESSION['s_admindb'];
require("../db.php");
mysql_select_db($admindb) or die(mysql_error());
$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);

$wagetable = 'ztmp'.$user_id.'_wages';


$moduledb = $_SESSION['s_findb'];
mysql_select_db($moduledb) or die(mysql_error());

include '../fin/jq-config.php';

// include the jqGrid Class
require_once "../includes/jquery/php/jqGrid.php";
// include the PDO driver class
require_once "../includes/jquery/php/jqGridPdo.php";
// Connection to the server
$conn = new PDO(DB_DSN,DB_USER,DB_PASSWORD);
// Tell the db that we use utf-8
$conn->query("SET NAMES utf8");

// Create the jqGrid instance
$grid = new jqGridRender($conn);

// the actual query for the grid data
$grid->SelectCommand = "select uid,operator,truckno,truckamt,trailerno,traileramt,total from ".$wagetable;

// set the ouput format to json
$grid->dataType = 'json';

// Let the grid create the model
$grid->setColModel(null);

// Set the url from where we obtain the data
$grid->setUrl('getwages.php');

// Set some grid options
$grid->setGridOptions(array(
    "rowNum"=>10,
    "sortname"=>"uid",
    "rowList"=>array(10,50,100),
	"height"=>230,
	"width"=>950
    ));

// Enable footerdata an tell the grid to obtain it from the request
$grid->setGridOptions(array("footerrow"=>true,"userDataOnFooter"=>true));

$grid->addCol(array("name"=>"act"),"last");


// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"ID", "width"=>25, "hidden"=>true));
$grid->setColProperty("operator", array("label"=>"Operator", "width"=>120));
$grid->setColProperty("truckno", array("label"=>"Truck", "width"=>60));
$grid->setColProperty("truckamt", array("label"=>"Truck Amount", "width"=>60, "formatter"=>"number","formatoptions"=>array("thousandsSeparator"=>",","decimalPlaces"=>2)));
$grid->setColProperty("trailerno", array("label"=>"Trailer", "width"=>60));
$grid->setColProperty("traileramt", array("label"=>"Trailer Amount", "width"=>60, "formatter"=>"number","formatoptions"=>array("thousandsSeparator"=>",","decimalPlaces"=>2)));
$grid->setColProperty("total", array("label"=>"Total", "width"=>60, "formatter"=>"number","formatoptions"=>array("thousandsSeparator"=>",","decimalPlaces"=>2)));
$grid->setColProperty("act", array("label"=>"Actions",  "width"=>50, "sortable"=>false));


// At end call footerData to put total  label
$grid->callGridMethod('#wageslist', 'footerData', array("note",array("operator"=>"Total:")));
// Set which parameter to be sumarized
$summaryrows = array("truckamt"=>array("truckamt"=>"SUM"),"traileramt"=>array("traileramt"=>"SUM"),"total"=>array("total"=>"SUM"));


$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#wageslist").getDataIDs();
	for(var i=0;i<ids.length;i++){
			var rowd = $("#wageslist").getRowData(ids[i]);
			var cl = ids[i];
			se = '<img src="../images/delete.png" title="Delete Transaction" onclick="javascript:delwages('+cl+')" ></ids>';
			jQuery("#wageslist").setRowData(ids[i],{act:se}); 
	}
}
LOADCOMPLETE;

$grid->setGridEvent("loadComplete",$loadevent);
// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>true,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

$buttonoptions2 = array("#wagespager",
    array("buttonicon"=>"ui-icon-calculator","caption"=>"","position"=>"last","title"=>"Calculators", "onClickButton"=>"js: function(){showCalculators();}")
);
$grid->callGridMethod("#wageslist", "navButtonAdd", $buttonoptions2); 


// Run the script
$grid->renderGrid('#wageslist','#wagespager',true, $summaryrows, null,true,true,true);
$conn = null;


?>




