<?php
session_start();

$vn = $_SESSION['s_vehicleno'];

$admindb = $_SESSION['s_admindb'];

require_once('../db.php');
mysql_select_db($admindb) or die(mysql_error());

$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);
$administrator = $admin;


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
$grid->SelectCommand = "select uid,date,hubodometer,jobno,workshop,service_type,servicedue from service where service_type <> 'R' and vehicleno = '".$vn."'";

// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model

// set the primary key - it is serial
$grid->setPrimaryKeyId('uid');

$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('getservice.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
    "caption"=>"Service Record for ".$vn,
    "rowNum"=>15,
    "sortname"=>"date",
    "rowList"=>array(15,50,80),
	"width"=>860,
	"height"=>350
    ));

$grid->addCol(array("name"=>"act"),"last");

// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"ID", "width"=>20, "hidden"=>true));
$grid->setColProperty("date", array("label"=>"Date", "width"=>80, "formatter"=>"date", "formatoptions"=>array("srcformat"=> "Y-m-d", "newformat"=>"d/m/Y")));
$grid->setColProperty("hubodometer", array("label"=>"Hubodometer", "width"=>100));
$grid->setColProperty("jobno", array("label"=>"Job No.", "width"=>100));
$grid->setColProperty("workshop", array("label"=>"Workshop", "width"=>100));
$grid->setColProperty("service_type", array("label"=>"Type", "width"=>40));
$grid->setColProperty("servicedue", array("label"=>"Next Due", "width"=>40));
$grid->setColProperty("act", array("label"=>"Actions",  "width"=>50, "sortable"=>false));


$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#servicelist").getDataIDs();
	for(var i=0;i<ids.length;i++){
			var rowd = $("#servicelist").getRowData(ids[i]);
			var cl = ids[i];
			var tp = "'"+rowd.service_type+"'";
			be = '<img src="../images/edit.png" title="Edit Service" onclick="javascript:editservice('+cl+','+tp+')" ></ids>';
			se = '<img src="../images/printer.gif" title="Print Service Sheet" onclick="javascript:printsheet('+cl+')" >'; 
			jQuery("#servicelist").setRowData(ids[i],{act:be+'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+se}); 
	}
}
LOADCOMPLETE;

$grid->setGridEvent("loadComplete",$loadevent);

// enable form editing
$grid->navigator = true;
$grid->setNavOptions('navigator', array("excel"=>true,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));


// Run the script
$grid->renderGrid('#servicelist','#servicepager',true, null, null, true,true);
?>



