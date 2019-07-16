<?php
session_start();
require("../db.php");

$coyidno = $_SESSION['s_coyid'];

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

// enable debugging
//$grid->debug = true;



$grid->SelectCommand = "select uid,costid,date,truckno,trailerno,description,supplier,supplierref,paid,posted from costheader";

// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('getCostheader.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
    "caption"=>"Onroad Costs from Operators",
    "rowNum"=>7,
    "sortname"=>"date",
	"sortorder"=>"desc",
    "rowList"=>array(7,30,50),
	"height"=>162,
	"width"=>940
    ));

$grid->addCol(array("name"=>"act"),"last");

// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"ID", "width"=>20, "hidden"=>true));
$grid->setColProperty("costid", array("label"=>"CostID", "width"=>20, "hidden"=>true));
$grid->setColProperty("date", array("label"=>"Date", "width"=>80, "formatter"=>"date", "formatoptions"=>array("srcformat"=> "Y-m-d", "newformat"=>"d/m/Y")));
$grid->setColProperty("truckno", array("label"=>"Truck", "width"=>80));
$grid->setColProperty("trailerno", array("label"=>"Trailer", "width"=>80));
$grid->setColProperty("description", array("label"=>"Description", "width"=>160));
$grid->setColProperty("supplier", array("label"=>"Supplier", "width"=>100));
$grid->setColProperty("supplierref", array("label"=>"Reference", "width"=>70));
$grid->setColProperty("paid", array("label"=>"Paid by Driver", "width"=>70));
$grid->setColProperty("posted", array("label"=>"Posted", "width"=>40));
$grid->setColProperty("act", array("label"=>"Actions",  "width"=>50, "sortable"=>false));

// on select row we should post the member id to second table and trigger it to reload the data
$selectgrp = <<<GRP
function(rowid, selected)
{
    if(rowid != null) {
		var rowd = $("#costheadlisting").getRowData(rowid);
		var cstid = rowd.costid;
        jQuery("#costlineslist").jqGrid('setGridParam',{postData:{cid:cstid}});
        jQuery("#costlineslist").trigger("reloadGrid");
    }
}
GRP;
$grid->setGridEvent('onSelectRow', $selectgrp);

// We should clear the grid data on second grid on sorting, paging, etc.
$cleargrid = <<<CLEAR
function(rowid, selected)
{
   // clear the grid data and footer data
   jQuery("#costlineslist").jqGrid('clearGridData',true);
}
CLEAR;

$grid->setGridEvent('onPaging', $cleargrid);
$grid->setGridEvent('onSortCol', $cleargrid);

$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#costheadlisting").getDataIDs();
	for(var i=0;i<ids.length;i++){
			var rowd = $("#costheadlisting").getRowData(ids[i]);
			var cl = ids[i];
			var ptd = rowd.posted;
			if (ptd == 'N') {
				se = '<img src="../images/edit.png" title="Edit Header Details" onclick="javascript:editheader('+cl+')" ></ids>';
				be = '<img src="../images/into.png" title="Post Cost to Accounts" onclick="javascript:postcost('+cl+')" ></ids>';
			} else {
				se = '&nbsp;&nbsp;&nbsp;&nbsp;';
				be = '&nbsp;&nbsp;&nbsp;&nbsp;';
			}
			jQuery("#costheadlisting").setRowData(ids[i],{act:se+'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+be}); 
	}
}
LOADCOMPLETE;

$grid->setGridEvent("loadComplete",$loadevent);

$grid->gSQLMaxRows = 4000;

// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>true,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

// Run the script
$grid->renderGrid('#costheadlisting','#costheadlistingpager',true, null, null, true,true);


?>

