<?php
session_start();
ini_set('display_errors', true);

$findb = $_SESSION['s_findb'];

include '../fin/jq-config.php';

// include the jqGrid Class
require_once "../includes/jquery/php/jqGrid.php";
// include the PDO driver class
require_once "../includes/jquery/php/jqGridPdo.php";
// Connection to the server
$conn = new PDO(DB_DSN,DB_USER,DB_PASSWORD);

// Create the jqGrid instance
$grid = new jqGridRender($conn);


// the actual query for the grid data
$grid->SelectCommand = "select groupid,groupname from ".$findb.".stkgroup";

// set the ouput format to json
$grid->dataType = 'json';

// Let the grid create the model
$grid->setColModel(null);

// Set the url from where we obtain the data
$grid->setUrl('../fin/getstkgroups.php');


// Set some grid options
$grid->setGridOptions(array(
    "rowNum"=>12,
    "sortname"=>"groupid",
    "rowList"=>array(12,30,50),
	"height"=>280,
	"width"=>450
    ));

$grid->addCol(array("name"=>"act"),"last");

// Change some property of the field(s)
$grid->setColProperty("groupid", array("label"=>"ID", "width"=>25, "hidden"=>true));
$grid->setColProperty("groupname", array("label"=>"Stock Group", "width"=>200));
$grid->setColProperty("act", array("label"=>"Actions", "width"=>70));

// on select row we should post the member id to second table and trigger it to reload the data
$selectgrp = <<<GRP
function(rowid, selected)
{
    if(rowid != null) {
        jQuery("#stkcatslist").jqGrid('setGridParam',{postData:{grp:rowid}});
        jQuery("#stkcatslist").trigger("reloadGrid");
    }
}
GRP;
$grid->setGridEvent('onSelectRow', $selectgrp);

// We should clear the grid data on second grid on sorting, paging, etc.
$cleargrid = <<<CLEAR
function(rowid, selected)
{
   // clear the grid data and footer data
   jQuery("#stkgrouplist").jqGrid('clearGridData',true);
}
CLEAR;

$grid->setGridEvent('onPaging', $cleargrid);
$grid->setGridEvent('onSortCol', $cleargrid);

$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#stkgrouplist").getDataIDs();
	for(var i=0;i<ids.length;i++){
			var rowd = $("#stkgrouplist").getRowData(ids[i]);
			var cl = ids[i];
			var gp = rowd.groupid;
			if (gp == 1) {
				be = '  ';
			} else {
				be = '<img src="../images/edit.png" title="Edit Group" onclick="javascript:editstkgroup('+cl+')" ></ids>';
			}
			jQuery("#stkgrouplist").setRowData(ids[i],{act:be}); 
	}
}
LOADCOMPLETE;

$grid->setGridEvent("loadComplete",$loadevent);


// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>false,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

// add a custom button via the build in callGridMethod
// note the js: before the function
$buttonoptions = array("#stkgrouppager",
    array("buttonicon"=>"ui-icon-plus","caption"=>"","position"=>"first","title"=>"Add a Stock Group.", "onClickButton"=>"js: function(){addstkgroup();}")
);
$grid->callGridMethod("#stkgrouplist", "navButtonAdd", $buttonoptions); 


// Run the script
$grid->renderGrid('#stkgrouplist','#stkgrouppager',true, null, null,true,true,true);

?>




