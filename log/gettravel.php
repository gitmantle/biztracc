<?php
session_start();
$usersession = $_SESSION['usersession'];

$brs = $_SESSION['s_cost_centres'];

$admindb = $_SESSION['s_admindb'];
require("../db.php");
mysql_select_db($admindb) or die(mysql_error());
$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);

$heading = $_SESSION['s_coyname'].' - '.$_SESSION['s_tbheading'];

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
$grid->SelectCommand = "select uid,truckno,datetime,driver,latitude,longitude from travellog where truckno in (".$brs.")";

// set the ouput format to json
$grid->dataType = 'json';

// Let the grid create the model
$grid->setColModel(null);

// Set the url from where we obtain the data
$grid->setUrl('gettravel.php');


// Set some grid options
$grid->setGridOptions(array(
    "caption"=>$heading,
    "rowNum"=>20,
    "sortname"=>"datetime",
	"sortorder"=>"desc",
    "rowList"=>array(20,100,200),
	"height"=>460,
	"width"=>890
    ));

$grid->addCol(array("name"=>"act"),"last");

// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"UID", "width"=>25, "hidden"=>true));
$grid->setColProperty("truckno", array("label"=>"Truck", "width"=>70));
$grid->setColProperty("driver", array("label"=>"Operator", "width"=>100));
$grid->setColProperty("datetime", array("label"=>"Date Time", "width"=>100));
$grid->setColProperty("latitude", array("label"=>"Latitude", "width"=>70));
$grid->setColProperty("longitude", array("label"=>"Longitude", "width"=>70));
$grid->setColProperty("act", array("label"=>"Actions", "width"=>20));


$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#travellist").getDataIDs();
	for(var i=0;i<ids.length;i++){
			var rowd = $("#travellist").getRowData(ids[i]);
			var cl = ids[i];
			var lat = rowd.latitude;
			var long = rowd.longitude;
			var truck = rowd.truckno;
			var dt = rowd.datetime;
			var address = "'"+lat+","+long+"'";
			mp = '<img src="../images/map.gif" title="Map" onclick="javascript:tmapad('+address+')" >';
			jQuery("#travellist").setRowData(ids[i],{act:mp}); 
	}
}
LOADCOMPLETE;

$grid->setGridEvent("loadComplete",$loadevent);


// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>true,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

// Run the script
$grid->renderGrid('#travellist','#travellistpager',true, null, null,true,true,true);
$conn = null;

?>




