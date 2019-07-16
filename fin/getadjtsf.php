<?php
session_start();

$coyidno = $_SESSION['s_coyid'];

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

$grid->SelectCommand = "select stktrans.uid,stktrans.ref_no,stktrans.ddate,stktrans.increase,stktrans.decrease,stklocs.location,stktrans.amount,stktrans.note from ".$findb.".stktrans,".$findb.".stklocs where stktrans.locid = stklocs.uid and (transtype = 'ADJ' or transtype = 'TSF')";

// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('getadjtsf.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
    "caption"=>"Adjustments and Transfers",
    "rowNum"=>15,
    "sortname"=>"ref_no",
    "rowList"=>array(15,30,50),
	"height"=>300,
	"width"=>750
    ));


// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"ID", "width"=>20, "hidden"=>true));
$grid->setColProperty("ref_no", array("label"=>"Reference", "width"=>90));
$grid->setColProperty("ddate", array("label"=>"Date", "width"=>80, "formatter"=>"date", "formatoptions"=>array("srcformat"=> "Y-m-d", "newformat"=>"d/m/Y")));
$grid->setColProperty("increase", array("label"=>"Increase", "width"=>90));
$grid->setColProperty("decrease", array("label"=>"Decrease", "width"=>90));
$grid->setColProperty("location", array("label"=>"Location", "width"=>90));
$grid->setColProperty("amount", array("label"=>"Value", "width"=>70, "formatter"=>"number","formatoptions"=>array("thousandsSeparator"=>",","decimalPlaces"=>2)));
$grid->setColProperty("note", array("label"=>"Note", "width"=>100));


// on select row we should post the member id to second table and trigger it to reload the data
$selectgrp = <<<GRP
function(rowid, selected)
{
    if(rowid != null) {
		var rowd = $("#adjtsflist").getRowData(rowid);
		var rno = rowd.ref_no;
        jQuery("#trddetlist").jqGrid('setGridParam',{postData:{cid:rno}});
        jQuery("#trddetlist").trigger("reloadGrid");
    }
}
GRP;
$grid->setGridEvent('onSelectRow', $selectgrp);

// We should clear the grid data on second grid on sorting, paging, etc.
$cleargrid = <<<CLEAR
function(rowid, selected)
{
   // clear the grid data and footer data
   jQuery("#trddetlist").jqGrid('clearGridData',true);
}
CLEAR;

$grid->setGridEvent('onPaging', $cleargrid);
$grid->setGridEvent('onSortCol', $cleargrid);


$grid->gSQLMaxRows = 4000;

// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>true,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

// Run the script
$grid->renderGrid('#adjtsflist','#adjtsfpager',true, null, null, true,true);


?>

