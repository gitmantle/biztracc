<?php
session_start();
$usersession = $_SESSION['usersession'];

$admindb = $_SESSION['s_admindb'];
require("../db.php");
mysql_select_db($admindb) or die(mysql_error());
$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);

$tbfile = 'ztmp'.$user_id.'_rprof';

$moduledb = $_SESSION['s_logdb'];
mysql_select_db($moduledb) or die(mysql_error());

$heading = 'Route Profitability - '.$_SESSION['s_coyname'].' - '.$_SESSION['s_tbheading'];

include 'jq-config.php';

// include the jqGrid Class
require_once "../includes/jquery/php/jqGrid.php";
// include the PDO driver class
require_once "../includes/jquery/php/jqGridPdo.php";
// Connection to the server
$conn = new PDO(DB_DSN,DB_USER,DB_PASSWORD);

// Create the jqGrid instance
$grid = new jqGridRender($conn);


// the actual query for the grid data
$grid->SelectCommand = "select docket_id, docket_no, ddate,route ,cpt , vehicle,valuekm from ".$tbfile;

// set the ouput format to json
$grid->dataType = 'json';

// Let the grid create the model
$grid->setColModel(null);

// Set the url from where we obtain the data
$grid->setUrl('getrouteprofgrid.php');


// Set some grid options
$grid->setGridOptions(array(
    "caption"=>$heading,
    "rowNum"=>20,
    "sortname"=>"docket_no",
    "rowList"=>array(20,100,200),
	"height"=>460,
	"width"=>890
    ));

//$grid->addCol(array("name"=>"act"),"last");

// Change some property of the field(s)
$grid->setColProperty("docket_id", array("label"=>"ID", "width"=>40, "hidden"=>true));
$grid->setColProperty("docket_no", array("label"=>"Docket", "width"=>50));
$grid->setColProperty("ddate", array("label"=>"Date", "width"=>60, "formatter"=>"date", "formatoptions"=>array("srcformat"=> "Y-m-d", "newformat"=>"d/m/Y")));
$grid->setColProperty("route", array("label"=>"Route", "width"=>150));
$grid->setColProperty("cpt", array("label"=>"Cpt", "width"=>40));
$grid->setColProperty("vehicle", array("label"=>"Truck", "width"=>80));
$grid->setColProperty("valuekm", array("label"=>"$/Km", "width"=>50, "formatter"=>"number","formatoptions"=>array("thousandsSeparator"=>",","decimalPlaces"=>2)));
//$grid->setColProperty("act", array("label"=>"Actions", "width"=>30));

/*
$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#rproflist").getDataIDs();
	for(var i=0;i<ids.length;i++){
			var rowd = $("#rproflist").getRowData(ids[i]);
			var cl = ids[i];
			var brn = rowd.branch;
			var br = "'"+brn+"'";
			be = '<img src="../images/into.png" title="View Transactions" onclick="javascript:viewtrucktrans('+br+')" ></ids>';
			jQuery("#rproflist").setRowData(ids[i],{act:be}); 
	}
}
LOADCOMPLETE;

$grid->setGridEvent("loadComplete",$loadevent);


*/


// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>true,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

// Run the script
$grid->renderGrid('#rproflist','#rproflistpager',true, $summaryrows, null,true,true,true);
$conn = null;

?>




