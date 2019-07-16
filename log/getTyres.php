<?php
session_start();


$admindb = $_SESSION['s_admindb'];

require_once('../db.php');
mysql_select_db($admindb) or die(mysql_error());

$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);

$serialtable = 'ztmp'.$user_id.'_serialnos';

$moduledb = $_SESSION['s_findb'];
mysql_select_db($moduledb) or die(mysql_error());

include '../fin/jq-config.php';

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
$grid->SelectCommand = "select uid,serialno,itemcode,item from ".$serialtable;

// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model

$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('getTyres.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
    "caption"=>"Tyres",
    "rowNum"=>7,
    "sortname"=>"serialno",
    "rowList"=>array(7,50,80),
	"width"=>860,
	"height"=>161
    ));

$grid->addCol(array("name"=>"act"),"last");

// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"ID", "width"=>80, "hidden"=> true));
$grid->setColProperty("serialno", array("label"=>"Serial No", "width"=>100));
$grid->setColProperty("itemcode", array("label"=>"Item Code", "width"=>80));
$grid->setColProperty("item", array("label"=>"Item", "width"=>150));
$grid->setColProperty("act", array("label"=>"Actions", "width"=>60));

$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#tyrelist").getDataIDs();
	for(var i=0;i<ids.length;i++){
			var rowd = $("#tyrelist").getRowData(ids[i]);
			var cl = ids[i];
			var itemid = "'"+rowd.itemcode+"'";
			var sno = "'"+rowd.serialno+"'";
			be = '<img src="../images/edit.png" title="Change Status" onclick="javascript:realloctyre('+cl+','+itemid+','+sno+')" ></ids>';
			jQuery("#tyrelist").setRowData(ids[i],{act:be}); 
	}
}
LOADCOMPLETE;

$grid->setGridEvent("loadComplete",$loadevent);


// on select row we should post the member id to second table and trigger it to reload the data
$selectgrp = <<<GRP
function(rowid, selected)
{
    if(rowid != null) {
		var rowd = $("#tyrelist").getRowData(rowid);
		var itid = rowd.itemcode;
		var sno = rowd.serialno;
		var itsno = itid+'~'+sno;
        jQuery("#tyreactivitylist").jqGrid('setGridParam',{postData:{its:itsno}});
        jQuery("#tyreactivitylist").trigger("reloadGrid");
    }
}
GRP;
$grid->setGridEvent('onSelectRow', $selectgrp);

// We should clear the grid data on second grid on sorting, paging, etc.
$cleargrid = <<<CLEAR
function(rowid, selected)
{
   // clear the grid data and footer data
   jQuery("#tyreactivitylist").jqGrid('clearGridData',true);
}
CLEAR;

$grid->setGridEvent('onPaging', $cleargrid);
$grid->setGridEvent('onSortCol', $cleargrid);


// enable form editing
$grid->navigator = true;
$grid->setNavOptions('navigator', array("excel"=>true,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

// Run the script
$grid->renderGrid('#tyrelist','#tyrepager',true, null, null, true,true);
?>



