<?php
session_start();
ini_set('display_errors', true);

$usersession = $_COOKIE['usersession'];
$dbase = $_SESSION['s_admindb'];

require("../db.php");
mysql_select_db($dbase) or die(mysql_error());
$query = "select * from sessions where session_id = ".$usersession;
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);

$subscriber = $sub_id;

require_once '../includes/jquery/jq-config.php';
// include the jqGrid Class
require_once "../includes/jquery/php/jqGrid.php";
// include the PDO driver class
require_once "../includes/jquery/php/jqGridPdo.php";
// Connection to the server
$conn = new PDO(DB_DSN,DB_USER,DB_PASSWORD);

// Create the jqGrid instance
$grid = new jqGridRender($conn);
// Write the SQL Query

// the actual query for the grid data
$grid->SelectCommand = "SELECT link_id,description,link from links where sub_id = ".$subscriber;

// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('getlinks.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
    "caption"=>"Links",
    "rowNum"=>12,
    "sortname"=>"description",
    "rowList"=>array(12,30,50),
	"height"=>280,
	"width"=>900
    ));

$grid->addCol(array("name"=>"act"),"first");


// Change some property of the field(s)
$grid->setColProperty("link_id", array("label"=>"ID", "width"=>20, "hidden"=>true));
$grid->setColProperty("description", array("label"=>"Links - Double click to open", "width"=>370));
$grid->setColProperty("link", array("label"=>"Link", "width"=>200));
$grid->setColProperty("act", array("label"=>"Actions", "width"=>70));

$ldevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#updtlinklist").getDataIDs(); 
	for(var i=0;i<ids.length;i++){ 
		var cl = ids[i]; 
		be = '<img src="../images/edit.png" title="Edit" onclick="javascript:editlink('+cl+')" >'; 
		se = '<img src="../images/delete.png" title="Delete" onclick="javascript:dellink('+cl+')" />'; 
		jQuery("#updtlinklist").setRowData(ids[i],{act:be+'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+se}); 
	} 
}
LOADCOMPLETE;
$grid->setGridEvent("loadComplete",$ldevent);

$dclickevent = <<<DBLCLICK
function(rowid) {
	getlink(rowid);
}
DBLCLICK;
$grid->setGridEvent('ondblClickRow',$dclickevent);

// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>true,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));
// add a custom button via the build in callGridMethod
// note the js: before the function
$buttonoptions = array("#updtlinkpager",
    array("buttonicon"=>"ui-icon-plus","caption"=>"","position"=>"first","title"=>"Add a link", "onClickButton"=>"js: function(){addlink();}")
);
$grid->callGridMethod("#updtlinklist", "navButtonAdd", $buttonoptions); 

// Run the script
$grid->renderGrid('#updtlinklist','#updtlinkpager',true, null, null, true,true,true);



?>




