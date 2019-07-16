<?php
session_start();
$usersession = $_SESSION['usersession'];

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$sid = $row['subid'];
$user_id = $row['user_id'];
$sname = $row['uname'];

$db->closeDB();

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
$grid->SelectCommand = "select distinct users.uid,users.ufname,users.ulname,users.uemail,users.uphone,users.umobile from users where users.active = 'Y' and users.sub_id = ".$sid;

// set the ouput format to json
$grid->dataType = 'json';

// Let the grid create the model
$grid->setColModel(null);

// Set the url from where we obtain the data
$grid->setUrl('../admin/hs_getusers.php');


// Set some grid options
$grid->setGridOptions(array(
	"caption"=>"Staff Members",						
    "rowNum"=>7,
    "sortname"=>"uid",
    "rowList"=>array(7,20,50),
	"height"=>200,
	"width"=>750
    ));

$grid->addCol(array("name"=>"act"),"last");

// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"ID", "width"=>25, "hidden"=>true));
$grid->setColProperty("ufname", array("label"=>"First Name", "width"=>100));
$grid->setColProperty("ulname", array("label"=>"Last Name", "width"=>100));
$grid->setColProperty("uemail", array("label"=>"Email", "width"=>170));
$grid->setColProperty("uphone", array("label"=>"Phone", "width"=>70));
$grid->setColProperty("umobile", array("label"=>"Mobile", "width"=>70));
$grid->setColProperty("branch", array("label"=>"Branch", "width"=>70));
$grid->setColProperty("act", array("label"=>"Actions", "width"=>40));

// on select row we should post the member id to second table and trigger it to reload the data
$selectgrp = <<<GRP
function(rowid, selected)
{
    if(rowid != null) {
		var rowd = $("#userslist").getRowData(rowid);
		var userid = rowd.uid;
        jQuery("#accesslist").jqGrid('setGridParam',{postData:{id:userid}});
        jQuery("#accesslist").trigger("reloadGrid");
    }
}
GRP;
$grid->setGridEvent('onSelectRow', $selectgrp);

// We should clear the grid data on second grid on sorting, paging, etc.
$cleargrid = <<<CLEAR
function(rowid, selected)
{
   // clear the grid data and footer data
   jQuery("#accesslist").jqGrid('clearGridData',true);
}
CLEAR;

$grid->setGridEvent('onPaging', $cleargrid);
$grid->setGridEvent('onSortCol', $cleargrid);


$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#userslist").getDataIDs();
	for(var i=0;i<ids.length;i++){
			var rowd = $("#userslist").getRowData(ids[i]);
			var cl = ids[i];
			be = '<img src="../images/edit.png" title="Edit User" onclick="javascript:edituser('+cl+')" ></ids>';
			se = '<img src="../images/delete.png" title="De-activate User" onclick="javascript:deluser('+cl+')" />'; 
			jQuery("#userslist").setRowData(ids[i],{act:be+'&nbsp;&nbsp;&nbsp;'+se}); 
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
$buttonoptions = array("#userspager",
    array("buttonicon"=>"ui-icon-plus","caption"=>"","position"=>"first","title"=>"Add a User", "onClickButton"=>"js: function(){adduser();}")
);
$grid->callGridMethod("#userslist", "navButtonAdd", $buttonoptions); 


// Run the script
$grid->renderGrid('#userslist','#userspager',true, null, null,true,true,true);

?>




