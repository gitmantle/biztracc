<?php
session_start();
$usersession = $_SESSION['usersession'];

$admindb = $_SESSION['s_admindb'];
require("../db.php");
mysql_select_db($admindb) or die(mysql_error());
$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);
$sid = $subid;

$ws = $_SESSION['s_wsstaff'];

require_once '../includes/jquery/jq-config.php';

// include the jqGrid Class
require_once "../includes/jquery/php/jqGrid.php";
// include the PDO driver class
require_once "../includes/jquery/php/jqGridPdo.php";
// Connection to the server
$conn = new PDO(DB_DSN,DB_USER,DB_PASSWORD);

// Create the jqGrid instance
$grid = new jqGridRender($conn);

// the actual query for the grid data
$grid->SelectCommand = "select users.uid,users.ufname,users.ulname,users.uemail,users.uphone,users.umobile from users,access where users.uid = access.staff_id and access.subid = ".$sid." and access.module = 'prc' and users.workshop_id = ".$ws;

// set the ouput format to json
$grid->dataType = 'json';

// Let the grid create the model
$grid->setColModel(null);

// Set the url from where we obtain the data
$grid->setUrl('getwsstaff.php');


// Set some grid options
$grid->setGridOptions(array(
    "rowNum"=>12,
    "sortname"=>"uid",
    "rowList"=>array(12,30,50),
	"height"=>300,
	"width"=>690
    ));

$grid->addCol(array("name"=>"act"),"last");

// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"ID", "width"=>25, "hidden"=>true));
$grid->setColProperty("ufname", array("label"=>"First Name", "width"=>100));
$grid->setColProperty("ulname", array("label"=>"Last Name", "width"=>100));
$grid->setColProperty("uemail", array("label"=>"Email", "width"=>170));
$grid->setColProperty("uphone", array("label"=>"Phone", "width"=>70));
$grid->setColProperty("umobile", array("label"=>"Mobile", "width"=>70));
$grid->setColProperty("act", array("label"=>"Actions", "width"=>40));

$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#wsstafflist").getDataIDs();
	for(var i=0;i<ids.length;i++){
			var rowd = $("#wsstafflist").getRowData(ids[i]);
			var cl = ids[i];
			be = '<img src="../images/edit.png" title="Edit User" onclick="javascript:editwsstaff('+cl+')" ></ids>';
			se = '<img src="../images/delete.png" title="Delete User" onclick="javascript:delwsstaff('+cl+')" />'; 
			jQuery("#wsstafflist").setRowData(ids[i],{act:be+'&nbsp;&nbsp;&nbsp;'+se}); 
	}
}
LOADCOMPLETE;

$grid->setGridEvent("loadComplete",$loadevent);

// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>true,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

// add a custom button via the build in callGridMethod
// note the js: before the function
$buttonoptions = array("#wsstaffpager",
    array("buttonicon"=>"ui-icon-plus","caption"=>"","position"=>"first","title"=>"Add a Workshop Staff Member", "onClickButton"=>"js: function(){addwsstaff();}")
);
$grid->callGridMethod("#wsstafflist", "navButtonAdd", $buttonoptions); 


// Run the script
$grid->renderGrid('#wsstafflist','#wsstaffpager',true, null, null,true,true,true);

?>




