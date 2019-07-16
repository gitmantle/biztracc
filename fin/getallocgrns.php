<?php
session_start();

$coyidno = $_SESSION['s_coyid'];

$ac = $_SESSION['s_crac'];
$sb = $_SESSION['s_crsb'];

$findb = $_SESSION['s_findb'];

$where = "where h.accountno = ".$ac." and h.sub = ".$sb." and (substring(t.ref_no,1,3) = 'GRN') and t.paid > 0 "; 

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

$grid->SelectCommand = "select t.uid, h.ddate,t.item,t.ref_no, t.paid from ".$findb.".invtrans t inner join invhead h on t.ref_no = h.ref_no  ".$where;

// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('getallocgrns.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
    "caption"=>"Allocations against Purchases",
    "rowNum"=>10,
    "sortname"=>"ddate",
	"sortorder" => 'desc',
	"rowList"=>array(10,30,50),
	"height"=>200,
	"width"=>950
    ));

$grid->addCol(array("name"=>"act"),"last");

// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"ID", "width"=>20, "hidden"=>true));
$grid->setColProperty("ddate", array("label"=>"Date", "width"=>70, "formatter"=>"date", "formatoptions"=>array("srcformat"=> "Y-m-d", "newformat"=>"d/m/Y")));
$grid->setColProperty("gldesc", array("label"=>"Description", "width"=>250));
$grid->setColProperty("ref_no", array("label"=>"Reference", "width"=>80));
$grid->setColProperty("paid", array("label"=>"Allocated", "width"=>80, "align"=>"right", "formatter"=>"number","formatoptions"=>array("thousandsSeparator"=>",","decimalPlaces"=>2)));
$grid->setColProperty("act", array("label"=>"Actions", "width"=>70));

$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#unalgrnlist").getDataIDs();
	for(var i=0;i<ids.length;i++){
			var rowd = $("#unalgrnlist").getRowData(ids[i]);
			var cl = ids[i];
			var topay = rowd.allocated;
			var refno = "'"+rowd.ref_no+"'";
			be = '<img src="../images/costs.png" title="Reverse allocation" onclick="javascript:revallocation('+cl+','+topay+','+refno+')" ></ids>';
			jQuery("#unalgrnlist").setRowData(ids[i],{act:be}); 
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
$grid->renderGrid('#unalgrnlist','#unalgrnpager2',true, null, null, true,true);


?>

