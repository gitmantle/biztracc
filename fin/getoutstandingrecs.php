<?php
session_start();
require("../db.php");

$coyidno = $_SESSION['s_coyid'];

if (isset($_SESSION['s_drac'])) {
	$ac = $_SESSION['s_drac'];
} else {
	$ac = 0;
}
if (isset($_SESSION['s_drsb'])) {
	$sb = $_SESSION['s_drsb'];
} else {
	$sb = 0;
}

$cltdb = $_SESSION['s_cltdb'];
mysql_select_db($cltdb) or die(mysql_error());

$where = "where accountno = ".$ac." and sub = ".$sb." and ((cash+cheque+eftpos+ccard+valreturned) < (totvalue + tax)) and (transtype = 'INV' or transtype = 'C_S')"; 

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

$grid->SelectCommand = "select uid,ddate,ref_no,gldesc,totvalue,tax,(totvalue + tax) as total, (cash+cheque+eftpos+ccard) as paid, valreturned, ((totvalue + tax)-(cash+cheque+eftpos+ccard+valreturned)) as outstanding from invhead ".$where;

// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('getoutstandingrecs.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
    "caption"=>"Outstanding Invoices",
    "rowNum"=>5,
    "sortname"=>"ddate",
    "rowList"=>array(5,30,50),
	"height"=>100,
	"width"=>950
    ));


// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"ID", "width"=>20, "hidden"=>true));
$grid->setColProperty("ddate", array("label"=>"Date", "width"=>70, "formatter"=>"date", "formatoptions"=>array("srcformat"=> "Y-m-d", "newformat"=>"d/m/Y")));
$grid->setColProperty("ref_no", array("label"=>"Reference", "width"=>80));
$grid->setColProperty("gldesc", array("label"=>"Description", "width"=>100));
$grid->setColProperty("totvalue", array("label"=>"Value", "width"=>80, "formatter"=>"number","formatoptions"=>array("thousandsSeparator"=>",","decimalPlaces"=>2)));
$grid->setColProperty("tax", array("label"=>"Tax", "width"=>80, "formatter"=>"number","formatoptions"=>array("thousandsSeparator"=>",","decimalPlaces"=>2)));
$grid->setColProperty("total", array("label"=>"Total", "width"=>80, "formatter"=>"number","formatoptions"=>array("thousandsSeparator"=>",","decimalPlaces"=>2)));
$grid->setColProperty("paid", array("label"=>"Paid", "width"=>80, "formatter"=>"number","formatoptions"=>array("thousandsSeparator"=>",","decimalPlaces"=>2)));
$grid->setColProperty("valreturned", array("label"=>"Returns", "width"=>80, "formatter"=>"number","formatoptions"=>array("thousandsSeparator"=>",","decimalPlaces"=>2)));
$grid->setColProperty("outstanding", array("label"=>"Outstanding", "width"=>80, "formatter"=>"number","formatoptions"=>array("thousandsSeparator"=>",","decimalPlaces"=>2)));

// on select row we should post the member id to second table and trigger it to reload the data
$selectgrp = <<<GRP
function(rowid, selected)
{
    if(rowid != null) {
		var rowd = $("#outreclist").getRowData(rowid);
		var ref = rowd.ref_no;
        jQuery("#outinvtrans").jqGrid('setGridParam',{postData:{cid:ref}});
        jQuery("#outinvtrans").trigger("reloadGrid");
    }
}
GRP;
$grid->setGridEvent('onSelectRow', $selectgrp);

// We should clear the grid data on second grid on sorting, paging, etc.
$cleargrid = <<<CLEAR
function(rowid, selected)
{
   // clear the grid data and footer data
   jQuery("#outreclist").jqGrid('clearGridData',true);
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
$grid->renderGrid('#outreclist','#outrecpager2',true, null, null, true,true);


?>

