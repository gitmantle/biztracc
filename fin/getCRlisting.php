<?php
session_start();

$coyidno = $_SESSION['s_coyid'];

$cltdb = $_SESSION['s_cltdb'];

$where = "where client_company_xref.company_id = ".$coyidno." and client_company_xref.crno != 0"; 

include '../clt/jq-config.php';
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

$grid->SelectCommand = "select members.member_id,client_company_xref.uid,members.lastname,members.firstname,client_company_xref.crno,client_company_xref.crsub,client_company_xref.subname,client_company_xref.sortcode,client_company_xref.client_id from ".$cltdb.".members left join ".$cltdb.".client_company_xref on members.member_id = client_company_xref.client_id ".$where;

// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('getCRlisting.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
    "caption"=>"Creditors Ledger Accounts",
    "rowNum"=>7,
    "sortname"=>"sortcode",
    "rowList"=>array(7,30,50),
	"height"=>160,
	"width"=>750
    ));


// Change some property of the field(s)
$grid->setColProperty("member_id", array("label"=>"MID", "width"=>20, "hidden"=>true));
$grid->setColProperty("uid", array("label"=>"ID", "width"=>20, "hidden"=>true));
$grid->setColProperty("lastname", array("label"=>"Company/Last Name", "width"=>200));
$grid->setColProperty("firstname", array("label"=>"First Name", "width"=>160));
$grid->setColProperty("crno", array("label"=>"Account", "width"=>60, "hidden"=>true));
$grid->setColProperty("crsub", array("label"=>"Sub", "width"=>60, "hidden"=>true));
$grid->setColProperty("subname", array("label"=>"Sub Acc.", "width"=>60));
$grid->setColProperty("sortcode", array("label"=>"Sort code", "width"=>50, "hidden"=>true));
$grid->setColProperty("client_id", array("label"=>"Client", "width"=>50, "hidden"=>true));

$grid->setSubGrid("getClientAd.php",
        array('Type', 'Addr_1', 'Addr_2', 'Suburb', 'Town'),
        array(50,120,110,110,90),
        array('left','left','left','left','left'));

// on select row we should post the member id to second table and trigger it to reload the data
$selectgrp = <<<GRP
function(rowid, selected)
{
    if(rowid != null) {
		var rowd = $("#crlisting").getRowData(rowid);
		var craccno = rowd.crno;
		var crsubno = rowd.crsub;
		var cr = craccno+'~'+crsubno;
        jQuery("#crtranslist").jqGrid('setGridParam',{postData:{cid:cr}});
        jQuery("#crtranslist").trigger("reloadGrid");
    }
}
GRP;
$grid->setGridEvent('onSelectRow', $selectgrp);

// We should clear the grid data on second grid on sorting, paging, etc.
$cleargrid = <<<CLEAR
function(rowid, selected)
{
   // clear the grid data and footer data
   jQuery("#crtranslist").jqGrid('clearGridData',true);
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
$grid->renderGrid('#crlisting','#crlistingpager2',true, null, null, true,true);


?>

