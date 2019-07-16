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
$grid->SelectCommand = "select uid,name,operation,involvment,concat(indexpy,' y ',indexpm,' m ',indexpd,' d ') as indexp, concat(jobexpy,' y ',jobexpm,' m ',jobexpd,' d ') as jobexp from incpeople where incident_id = ".$incid;

// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model

// set the primary key - it is serial
$grid->setPrimaryKeyId('uid');

$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('getincpeople.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
    "caption"=>"People Involved in Incident",
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
$grid->setColProperty("operation", array("label"=>"Operation", "width"=>150));
$grid->setColProperty("involvment", array("label"=>"Involvment", "width"=>100));
$grid->setColProperty("indexp", array("label"=>"Time in Industry", "width"=>70));
$grid->setColProperty("jobexp", array("label"=>"Time in Job", "width"=>70));
$grid->setColProperty("act", array("label"=>"Actions",  "width"=>50, "sortable"=>false));


$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#ipeoplelist").getDataIDs();
	for(var i=0;i<ids.length;i++){
			var rowd = $("#ipeoplelist").getRowData(ids[i]);
			var cl = ids[i];
			be = '<img src="../images/edit.png" title="Edit Person Involved" onclick="javascript:editipeople('+cl+')" ></ids>';
			jQuery("#ipeoplelist").setRowData(ids[i],{act:be}); 
	}
}
LOADCOMPLETE;

$grid->setGridEvent("loadComplete",$loadevent);

// enable form editing
$grid->navigator = true;
$grid->setNavOptions('navigator', array("excel"=>true,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

// add a custom button via the build in callGridMethod
// note the js: before the function
$buttonoptions = array("#ipeoplepager",
    array("buttonicon"=>"ui-icon-plus","caption"=>"","position"=>"first","title"=>"Add an involved person", "onClickButton"=>"js: function(){addipeople();}")
);
$grid->callGridMethod("#ipeoplelist", "navButtonAdd", $buttonoptions); 

// Run the script
$grid->renderGrid('#ipeoplelist','#ipeoplepager',true, null, null, true,true);
?>



