<?php

//ini_set('display_errors', true);
session_start();
$usersession = $_SESSION['usersession'];

$ddate = date("d/m/Y");
$hdate = date("Y-m-d");

$admindb = $_SESSION['s_admindb'];
require("../db.php");
mysql_select_db($admindb) or die(mysql_error());
$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);

$subscriber = $subid;

//construct where clause 
$dep = $_SESSION['s_udepot'];
if ($dep == 0) {
	$where = " where 1 = 1"; 
} else {
	$where = " where depot_id = ".$dep; 
}

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
$grid->SelectCommand = "SELECT depot_id,sad1,sad2,scountry,depot,stown,contact,phone from depots".$where;

// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('getdepots.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
    "caption"=>"Depots",
    "rowNum"=>12,
    "sortname"=>"depot",
    "rowList"=>array(12,30,50),
	"height"=>270,
	"width"=>700
    ));

$grid->addCol(array("name"=>"act"),"last");

// Change some property of the field(s)
$grid->setColProperty("depot_id", array("label"=>"ID", "width"=>20, "hidden"=>true));
$grid->setColProperty("sad1", array("label"=>"a1", "width"=>50, "hidden"=>true));
$grid->setColProperty("sad2", array("label"=>"a2", "width"=>50, "hidden"=>true));
$grid->setColProperty("scountry", array("label"=>"ct", "width"=>50, "hidden"=>true));
$grid->setColProperty("depot", array("label"=>"Depot", "width"=>150));
$grid->setColProperty("stown", array("label"=>"Town", "width"=>150));
$grid->setColProperty("contact", array("label"=>"Contact", "width"=>150));
$grid->setColProperty("phone", array("label"=>"Phone", "width"=>100));
$grid->setColProperty("act", array("label"=>"Actions", "width"=>70));

$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#depotlist").getDataIDs();
	for(var i=0;i<ids.length;i++){
			var cl = ids[i];
			var rowdata = $("#depotlist").getRowData(cl);
			var ad1 = rowdata.sad1;
			var ad2 = rowdata.sad2;
			var town = rowdata.stown;
			var country = rowdata.scountry;
			var address = "'"+ad1+','+ad2+','+town+','+country+"'";
			be = '<img src="../images/edit.png" title="Edit Depot" onclick="javascript:editdepot('+cl+')" ></ids>';
			mp = '<img src="../images/map.gif" title="Map" onclick="javascript:mapad('+address+')" >';
			jQuery("#depotlist").setRowData(ids[i],{act:be+'&nbsp;&nbsp;&nbsp;'+mp}); 
	}
}
LOADCOMPLETE;

$grid->setGridEvent("loadComplete",$loadevent);

$grid->gSQLMaxRows = 4000;

// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>true,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));
// add a custom button via the build in callGridMethod
// note the js: before the function
$buttonoptions = array("#depotpager",
    array("buttonicon"=>"ui-icon-plus","caption"=>"","position"=>"first","title"=>"Add a Depot", "onClickButton"=>"js: function(){adddepot();}")
);
$grid->callGridMethod("#depotlist", "navButtonAdd", $buttonoptions); 

// Run the script
$grid->renderGrid('#depotlist','#depotpager',true, null, null, true,true);


?>




