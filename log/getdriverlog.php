<?php
session_start();
$usersession = $_SESSION['usersession'];

$drs = $_SESSION['s_drivers'];
$fromdate = $_SESSION['s_fromdate'];
$todate = $_SESSION['s_todate'];

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
$grid->SelectCommand = "select uid,driver,date,time,log,truckno,hubodometer from driverlog where driverid in (".$drs.") and date >= '".$fromdate."' and date <= '".$todate."'";

// set the ouput format to json
$grid->dataType = 'json';

// Let the grid create the model
$grid->setColModel(null);

// Set the url from where we obtain the data
$grid->setUrl('getdriverlog.php');


// Set some grid options
$grid->setGridOptions(array(
    "caption"=>$heading,
    "rowNum"=>20,
    "sortname"=>"uid",
	"sortorder"=>"desc",
    "rowList"=>array(20,100,200),
	"height"=>450,
	"width"=>890
    ));


$grid->addCol(array("name"=>"act"),"last");

// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"UID", "width"=>25, "hidden"=>true));
$grid->setColProperty("driver", array("label"=>"Driver", "width"=>100));
$grid->setColProperty("date", array("label"=>"Date", "width"=>70, "formatter"=>"date", "formatoptions"=>array("srcformat"=> "Y-m-d", "newformat"=>"d/m/Y")));
$grid->setColProperty("time", array("label"=>"Time", "width"=>70));
$grid->setColProperty("log", array("label"=>"Log Activity", "width"=>100));
$grid->setColProperty("truckno", array("label"=>"Truck", "width"=>70));
$grid->setColProperty("hubodometer", array("label"=>"Hubodometer", "width"=>70));
$grid->setColProperty("act", array("label"=>"Actions",  "width"=>50, "sortable"=>false));

$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#dloglist").getDataIDs();
	for(var i=0;i<ids.length;i++){
			var rowd = $("#dloglist").getRowData(ids[i]);
			var cl = ids[i];
			be = '<img src="../images/edit.png" title="Edit Entry" onclick="javascript:editdlog('+cl+')" ></ids>';
			jQuery("#dloglist").setRowData(ids[i],{act:be}); 
	}
}
LOADCOMPLETE;

$grid->setGridEvent("loadComplete",$loadevent);

// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>true,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

// Run the script
$grid->renderGrid('#dloglist','#dloglistpager',true, null, null,true,true,true);
$conn = null;

?>




