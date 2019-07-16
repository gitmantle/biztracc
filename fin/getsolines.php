<?php
session_start();
$usersession = $_SESSION['usersession'];

$findb = $_SESSION['s_findb'];

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

// Write the SQL Query
// We suppose that mytable exists in your database
$grid->SelectCommand = "select uid,ref_no,ddate,client,totvalue from ".$findb.".invhead where ref_no like 'S_O%'"; 


// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('getsolines.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
    "caption"=>"Sales Order Lines",
    "rowNum"=>5,
    "sortname"=>"uid",
    "rowList"=>array(5,30,50),
	"height"=>135,
	"width"=>400
    ));


$grid->addCol(array("name"=>"act"),"last");

// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"ID", "width"=>20, "hidden"=>true));
$grid->setColProperty("ref_no", array("label"=>"Ref.", "width"=>70));
$grid->setColProperty("ddate", array("label"=>"Date", "width"=>80, "formatter"=>"date", "formatoptions"=>array("srcformat"=> "Y-m-d", "newformat"=>"d/m/Y")));
$grid->setColProperty("client", array("label"=>"Client", "width"=>100));
$grid->setColProperty("totvalue", array("label"=>"Value", "width"=>80, "align"=>"right","formatter"=>"number"));
$grid->setColProperty("act", array("label"=>"Actions",  "width"=>50, "sortable"=>false));

// on select row we should post the member id to second table and trigger it to reload the data
$selectdns = <<<DNS
function(rowid, selected)
{
    if(rowid != null) {
        jQuery("#dnlist").jqGrid('setGridParam',{postData:{son:rowid}});
        jQuery("#dnlist").trigger("reloadGrid");
    }
}
DNS;
$grid->setGridEvent('onSelectRow', $selectdns);

// We should clear the grid data on second grid on sorting, paging, etc.
$cleargrid = <<<CLEAR
function(rowid, selected)
{
   // clear the grid data and footer data
   jQuery("#dnlist").jqGrid('clearGridData',true);
}
CLEAR;

$grid->setGridEvent('onPaging', $cleargrid);
$grid->setGridEvent('onSortCol', $cleargrid);

$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#solist").getDataIDs();
	for(var i=0;i<ids.length;i++){
			var rowd = $("#solist").getRowData(ids[i]);
			var r = rowd.ref_no;
			var rf = "'"+r+"'";
			var cl = ids[i];
			be = '<img src="../images/printer.gif" title="Print Sales Order" onclick="javascript:printdoc('+rf+')" ></ids>';
			de = '<img src="../images/delivery.png" title="Add Delivery Note" onclick="javascript:adddo('+rf+')" ></ids>';
			jQuery("#solist").setRowData(ids[i],{act:be+'&nbsp;&nbsp;&nbsp;'+de}); 
	}
}
LOADCOMPLETE;

$grid->setGridEvent("loadComplete",$loadevent);

$dcevent = <<<DBLCLICK
function(rowid) {
	var rowdata = $("#solist").getRowData(rowid);
	var memberid = rowdata.member_id;
	editmem(memberid);
}
DBLCLICK;

$grid->setGridEvent('ondblClickRow',$dcevent);

$grid->gSQLMaxRows = 4000;

// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>false,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

// Run the script
$grid->renderGrid('#solist','#sopager',true, null, null, true,true);

?>



