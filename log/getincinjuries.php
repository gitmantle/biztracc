<?php
session_start();

$incid = $_SESSION['s_incidentid'];
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
$grid->SelectCommand = "select uid,name,injury1,body1,severity,dayslost from incinjuries where incident_id = ".$incid;

// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model

// set the primary key - it is serial
$grid->setPrimaryKeyId('uid');

$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('getincinjuries.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
    "caption"=>"People Injured in Incident",
    "rowNum"=>15,
    "sortname"=>"name",
    "rowList"=>array(15,50,80),
	"width"=>860,
	"height"=>400
    ));

$grid->addCol(array("name"=>"act"),"last");

// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"ID", "width"=>20, "hidden"=>true));
$grid->setColProperty("name", array("label"=>"Name", "width"=>150));
$grid->setColProperty("injury1", array("label"=>"Injury", "width"=>150));
$grid->setColProperty("body1", array("label"=>"Part of Body", "width"=>150));
$grid->setColProperty("severity", array("label"=>"Severity", "width"=>150));
$grid->setColProperty("dayslost", array("label"=>"Days Lost", "width"=>70));
$grid->setColProperty("act", array("label"=>"Actions", "width"=>60));


$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#iinjurylist").getDataIDs();
	for(var i=0;i<ids.length;i++){
			var rowd = $("#iinjurylist").getRowData(ids[i]);
			var cl = ids[i];
			be = '<img src="../images/edit.png" title="Edit Injury" onclick="javascript:editiinjury('+cl+')" ></ids>';
			jQuery("#iinjurylist").setRowData(ids[i],{act:be}); 
	}
}
LOADCOMPLETE;

$grid->setGridEvent("loadComplete",$loadevent);

// enable form editing
$grid->navigator = true;
$grid->setNavOptions('navigator', array("excel"=>true,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

// add a custom button via the build in callGridMethod
// note the js: before the function
$buttonoptions = array("#iinjurypager",
    array("buttonicon"=>"ui-icon-plus","caption"=>"","position"=>"first","title"=>"Add an injury", "onClickButton"=>"js: function(){addiinjury();}")
);
$grid->callGridMethod("#iinjurylist", "navButtonAdd", $buttonoptions); 

// Run the script
$grid->renderGrid('#iinjurylist','#iinjurypager',true, null, null, true,true);
?>



