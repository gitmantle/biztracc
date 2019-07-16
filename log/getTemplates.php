<?php
session_start();


$admindb = $_SESSION['s_admindb'];

require_once('../db.php');
mysql_select_db($admindb) or die(mysql_error());

$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);
$ad = $admin;
$sid = $subid;


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
$grid->SelectCommand = "select uid,'".$ad."' as admin,".$sid." as sid,title,template from templates";

// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model

// set the primary key - it is serial
$grid->setPrimaryKeyId('uid');

$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('getTemplates.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
    "caption"=>"Templates",
    "rowNum"=>20,
    "sortname"=>"title",
    "rowList"=>array(20,50,80),
	"width"=>760,
	"height"=>450
    ));

$grid->addCol(array("name"=>"act"),"last");

// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"ID", "width"=>20, "hidden"=>true));
$grid->setColProperty("sid", array("label"=>"SID", "width"=>20, "hidden"=>true));
$grid->setColProperty("admin", array("label"=>"Admin", "width"=>20, "hidden"=>true));
$grid->setColProperty("title", array("label"=>"Title", "width"=>200));
$grid->setColProperty("template", array("label"=>"Template", "width"=>200, "hidden"=>true));
$grid->setColProperty("act", array("label"=>"Actions", "width"=>60));


$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#templatelist").getDataIDs();
	for(var i=0;i<ids.length;i++){
			var rowd = $("#templatelist").getRowData(ids[i]);
			var cl = ids[i];
			var ad = rowd.admin;
			var sid = rowd.sid;
			var doc = "'"+rowd.template+"'";
			if (ad == 'Y') {
				ae = '<img src="../images/delete.png" title="Delete template" onclick="javascript:deltemplate('+cl+')" ></ids>';
			} else {
				ae = '&nbsp;&nbsp;&nbsp;';
			}
			be = '<img src="../images/into.png" title="Download Template" onclick="javascript:downloadtemplate('+cl+','+sid+','+doc+')" ></ids>';
			jQuery("#templatelist").setRowData(ids[i],{act:be+'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+ae}); 
	}
}
LOADCOMPLETE;

$grid->setGridEvent("loadComplete",$loadevent);

// enable form editing
$grid->navigator = true;
$grid->setNavOptions('navigator', array("excel"=>true,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

// add a custom button via the build in callGridMethod
// note the js: before the function
$buttonoptions = array("#templatepager",
    array("buttonicon"=>"ui-icon-plus","caption"=>"","position"=>"first","title"=>"Add a template", "onClickButton"=>"js: function(){addtemplate();}")
);
$grid->callGridMethod("#templatelist", "navButtonAdd", $buttonoptions); 

// Run the script
$grid->renderGrid('#templatelist','#templatepager',true, null, null, true,true);
?>



