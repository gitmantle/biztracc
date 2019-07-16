<?php
session_start();
ini_set('display_errors', true);
require("../db.php");

$findb = $_SESSION['s_findb'];
mysql_select_db($findb) or die(mysql_error());

if(isset($_REQUEST["cr_mask"])) {
	$cr_mask = $_REQUEST['cr_mask'];
} else {
  	$cr_mask = ""; 
}


//construct where clause 
$where = ' where stkmast.deftax = taxtypes.uid';
if($cr_mask!='') {
	$where = " and upper(item) LIKE '".$cr_mask."%'"; 
}

$heading = 'Select Stock Item';


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

$grid->SelectCommand = "select stkmast.itemcode,stkmast.item,stkmast.unit,stkmast.deftax,taxtypes.taxpcent,stkmast.sellacc,stkmast.sellbr,stkmast.sellsub,stkmast.purchacc,stkmast.purchbr,stkmast.purchsub,stkmast.groupid,stkmast.catid,stkmast.avgcost,stkmast.setsell,stkmast.trackserial from ".$findb.".stkmast,".$findb.".taxtypes".$where;

// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('selectstk.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
    "caption"=>$heading,
    "rowNum"=>5,
    "sortname"=>"item",
    "rowList"=>array(5,50,100,200),
	"height"=>120,
	"width"=>448
	));

// Change some property of the field(s)
$grid->setColProperty("itemcode", array("label"=>"Item Code", "width"=>100));
$grid->setColProperty("item", array("label"=>"Item", "width"=>200));
$grid->setColProperty("unit", array("label"=>"Unit", "width"=>50, "hidden"=>true));
$grid->setColProperty("deftax", array("label"=>"Tax", "width"=>50, "hidden"=>true));
$grid->setColProperty("taxpcent", array("label"=>"Taxpcent", "width"=>50, "hidden"=>true));
$grid->setColProperty("sellacc", array("label"=>"sellacc", "width"=>50, "hidden"=>true));
$grid->setColProperty("sellbr", array("label"=>"sellbr", "width"=>50, "hidden"=>true));
$grid->setColProperty("sellsub", array("label"=>"sellsub", "width"=>50, "hidden"=>true));
$grid->setColProperty("purchacc", array("label"=>"purchacc", "width"=>50, "hidden"=>true));
$grid->setColProperty("purchbr", array("label"=>"purchbr", "width"=>50, "hidden"=>true));
$grid->setColProperty("purchsub", array("label"=>"purchsub", "width"=>50, "hidden"=>true));
$grid->setColProperty("groupid", array("label"=>"group", "width"=>50, "hidden"=>true));
$grid->setColProperty("catid", array("label"=>"cat", "width"=>50, "hidden"=>true));
$grid->setColProperty("avgcost", array("label"=>"avgcost", "width"=>50, "hidden"=>true));
$grid->setColProperty("setsell", array("label"=>"setsell", "width"=>50, "hidden"=>true));
$grid->setColProperty("trackserial", array("label"=>"trackserial", "width"=>50, "hidden"=>true));


// on select row populate the relevant text box and hide the div
$selectac = <<<ACC
function(rowid, selected)
{
    if(rowid != null) {
	var rowdata = $("#selectstklist").getRowData(rowid);
	var stkname = rowdata.item;
	var stkcode = rowdata.itemcode;
	var stkunit = rowdata.unit;
	var stax = rowdata.deftax;
	var sac = rowdata.sellacc;
	var sbr = rowdata.sellbr;
	var ssb = rowdata.sellsub;
	var pac = rowdata.purchacc;
	var pbr = rowdata.purchbr;
	var psb = rowdata.purchsub;
	var group = rowdata.groupid;
	var cat = rowdata.catid;
	var cost = rowdata.avgcost;
	var setsell = rowdata.setsell;
	var trackserial = rowdata.trackserial;
	var staxpcent = rowdata.taxpcent;
	var stk = stkcode+'~'+stkname+'~'+stkunit+'~'+stax+'~'+sac+'~'+sbr+'~'+ssb+'~'+pac+'~'+pbr+'~'+psb+'~'+group+'~'+cat+'~'+cost+'~'+setsell+'~'+trackserial+'~'+staxpcent;
	setstkselect(stk);	
    }
}
ACC;
$grid->setGridEvent('onSelectRow', $selectac);


// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>false,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

// Run the script
$grid->renderGrid('#selectstklist','#selectstkpager',true, null, null, true,true);

?>

