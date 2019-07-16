<?php
session_start();


$admindb = $_SESSION['s_admindb'];

require_once('../db.php');
mysql_select_db($admindb) or die(mysql_error());

$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);


$moduledb = $_SESSION['s_logdb'];
mysql_select_db($moduledb) or die(mysql_error());

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
$grid->SelectCommand = "select uid,route,compartment,forest,private,rate from routes";

// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model

// set the primary key - it is serial
$grid->setPrimaryKeyId('uid');

$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('getRoutes.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
    "caption"=>"Routes",
    "rowNum"=>20,
    "sortname"=>"route,compartment",
    "rowList"=>array(20,50,80),
	"width"=>800,
	"height"=>467
    ));

$grid->addCol(array("name"=>"act"),"last");

// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"ID", "width"=>20, "hidden"=>true));
$grid->setColProperty("route", array("label"=>"Route", "width"=>200));
$grid->setColProperty("compartment", array("label"=>"Compartment", "width"=>65));
$grid->setColProperty("forest", array("label"=>"Forest", "width"=>100));
$grid->setColProperty("private", array("label"=>"Private Road", "width"=>70));
$grid->setColProperty("rate", array("label"=>"Rate", "width"=>50, "formatter"=>"number","formatoptions"=>array("thousandsSeparator"=>",","decimalPlaces"=>2)));
$grid->setColProperty("act", array("label"=>"Actions", "width"=>60));


$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#routelist").getDataIDs();
	for(var i=0;i<ids.length;i++){
			var rowd = $("#routelist").getRowData(ids[i]);
			var cl = ids[i];
			if (cl > 1) {
				be = '<img src="../images/edit.png" title="Edit Route" onclick="javascript:editroute('+cl+')" ></ids>';
				pe = '<img src="../images/printer.gif" title="Print Site Specific Hazards" onclick="javascript:printhazard('+cl+')" >';
				me = '<img src="../images/email.png" title="Email Hazard Report" onclick="javascript:emailhazard('+cl+')" >';
			} else {
				be = '&nbsp;&nbsp;&nbsp;&nbsp;';
				pe = '&nbsp;&nbsp;&nbsp;&nbsp;';
				me = '&nbsp;&nbsp;&nbsp;&nbsp;';
			}
			jQuery("#routelist").setRowData(ids[i],{act:be+'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+pe+'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+me}); 
	}
}
LOADCOMPLETE;

$grid->setGridEvent("loadComplete",$loadevent);

// enable form editing
$grid->navigator = true;
$grid->setNavOptions('navigator', array("excel"=>true,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

// add a custom button via the build in callGridMethod
// note the js: before the function
$buttonoptions = array("#routepager",
    array("buttonicon"=>"ui-icon-plus","caption"=>"","position"=>"first","title"=>"Add a route", "onClickButton"=>"js: function(){addroute();}")
);
$grid->callGridMethod("#routelist", "navButtonAdd", $buttonoptions); 

// Run the script
$grid->renderGrid('#routelist','#routepager',true, null, null, true,true);
?>



