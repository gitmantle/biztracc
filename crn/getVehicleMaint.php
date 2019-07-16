<?php
session_start();
date_default_timezone_set($_SESSION['s_timezone']);

$crndb = $_SESSION['s_crndb'];

include 'jq-config.php';

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
// Write the SQL Query
// We suppose that mytable exists in your database
$grid->SelectCommand = "select uid,vehicleno,branch,regno,make,rate,lastserviced,odometer,servicedue,cofdate from ".$crndb.".vehicles";

// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model

// set the primary key - it is serial
$grid->setPrimaryKeyId('uid');

$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('getVehicleMaint.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
    "caption"=>"Fleet Maintenance",
    "rowNum"=>20,
    "sortname"=>"vehicleno",
    "rowList"=>array(20,50,80),
	"width"=>960,
	"height"=>450
    ));


// Change some property of the field(s)
$grid->addCol(array("name"=>"act"),"last");
$grid->setColProperty("uid", array("label"=>"ID", "width"=>20, "hidden"=>true));
$grid->setColProperty("vehicleno", array("label"=>"Fleet No.", "width"=>70));
$grid->setColProperty("branch", array("label"=>"Branch", "width"=>70));
$grid->setColProperty("regno", array("label"=>"Registration No", "width"=>70));
$grid->setColProperty("make", array("label"=>"Make", "width"=>80));
$grid->setColProperty("rate", array("label"=>"Hourly Rate", "width"=>55, "align"=>"right","formatter"=>"number"));
$grid->setColProperty("lastserviced", array("label"=>"Last serviced", "width"=>80, "formatter"=>"date", "formatoptions"=>array("srcformat"=> "Y-m-d", "newformat"=>"d/m/Y")));
$grid->setColProperty("odometer", array("label"=>"Mileage/Hours", "width"=>70));
$grid->setColProperty("servicedue", array("label"=>"Service due", "width"=>70));
$grid->setColProperty("cofdate", array("label"=>"COF due", "width"=>80, "formatter"=>"date", "formatoptions"=>array("srcformat"=> "Y-m-d", "newformat"=>"d/m/Y")));
$grid->setColProperty("act", array("label"=>"Actions",  "width"=>50, "sortable"=>false));

$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#smlist").getDataIDs();
	for(var i=0;i<ids.length;i++){
			var rowd = $("#smlist").getRowData(ids[i]);
			var cl = ids[i];
			var vn = "'"+rowd.branch+"~"+rowd.vehicleno+"'";
			be = '<img src="../images/edit.png" title="Edit Vehicle" onclick="javascript:editvehicle('+cl+')" ></ids>';
			se = '<img src="../images/spanner.png" title="Servicing Records" onclick="javascript:sm('+vn+')" ></ids>';
			jQuery("#smlist").setRowData(ids[i],{act:be+'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+se}); 
	}
}
LOADCOMPLETE;

$grid->setGridEvent("loadComplete",$loadevent);

// enable form editing
$grid->navigator = true;
$grid->setNavOptions('navigator', array("excel"=>true,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

// add a custom button via the build in callGridMethod
// note the js: before the function
$buttonoptions = array("#smpager",
    array("buttonicon"=>"ui-icon-plus","caption"=>"","position"=>"first","title"=>"Add a vehicle", "onClickButton"=>"js: function(){addvehicle();}")
);
$grid->callGridMethod("#smlist", "navButtonAdd", $buttonoptions); 

// Run the script
$grid->renderGrid('#smlist','#smpager',true, null, null, true,true);
?>



