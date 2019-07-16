<?php
session_start();
$usersession = $_SESSION['usersession'];
$admindb = $_SESSION['s_admindb'];
require("../db.php");
mysql_select_db($admindb) or die(mysql_error());

$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);
$subscriber = $subid;

$tradetable = 'ztmp'.$user_id.'_dn';

$moduledb = $_SESSION['s_cltdb'];
mysql_select_db($moduledb) or die(mysql_error());


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

// enable debugging
//$grid->debug = true;

// Write the SQL Query
// We suppose that mytable exists in your database
$grid->SelectCommand = "select uid,itemcode,item,unit,quantity,sent,(quantity - sent) as outstanding,picked from ".$tradetable; 


// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('getdeliverynotes.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
    "caption"=>"Sales Order Lines",
    "rowNum"=>12,
    "sortname"=>"item",
    "rowList"=>array(12,30,50),
	"height"=>300,
	"width"=>940,
	"cellEdit"=> true,
	"mtype" => "POST",
	"cellsubmit" => "remote",
	"cellurl" => "../ajax/ajaxpicked.php"
	));


$grid->addCol(array("name"=>"act2"),"last");
/*
$grid->addCol(array(
    "name"=>"Pick",
    "formatter"=>"actions",
    "editable"=>false,
    "sortable"=>false,
    "resizable"=>false,
    "fixed"=>true,
    "width"=>60,
    "formatoptions"=>array("keys"=>true, "delbutton"=>false)
    ), "last"); 
*/
// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"ID", "editable"=>false, "width"=>20, "hidden"=>true));
$grid->setColProperty("itemcode", array("label"=>"Code", "editable"=>false, "width"=>70));
$grid->setColProperty("item", array("label"=>"Item", "editable"=>false, "width"=>120));
$grid->setColProperty("unit", array("label"=>"Unit", "editable"=>false, "width"=>50));
$grid->setColProperty("quantity", array("label"=>"Quantity", "editable"=>false, "width"=>70, "align"=>"right","formatter"=>"number"));
$grid->setColProperty("sent", array("label"=>"Sent", "editable"=>false, "width"=>70, "align"=>"right","formatter"=>"number"));
$grid->setColProperty("outstanding", array("label"=>"Outstanding", "editable"=>false, "width"=>70, "align"=>"right","formatter"=>"number"));
$grid->setColProperty("picked", array("label"=>"Pick", "width"=>70, "align"=>"right","formatter"=>"number"));
$grid->setColProperty("act2", array("label"=>"Actions", "editable"=>false, "width"=>50,  "formatoptions"=>array("keys"=>true)
));


$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#deliverylist").getDataIDs();
	for(var i=0;i<ids.length;i++){
			var rowd = $("#deliverylist").getRowData(ids[i]);
			var r = rowd.ref_no;
			var rf = "'"+r+"'";
			var cl = ids[i];
			be = '<img src="../images/into.png" title="View Delivery Note" onclick="javascript:viewdn('+cl+')" ></ids>';
			de = '<img src="../images/printer.gif" title="Print Delivery Note" onclick="javascript:printdn('+cl+')" ></ids>';
			jQuery("#deliverylist").setRowData(ids[i],{act2:be+'&nbsp;&nbsp;&nbsp;'+de}); 
	}
}
LOADCOMPLETE;

$grid->setGridEvent("loadComplete",$loadevent);

$grid->gSQLMaxRows = 4000;

// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>false,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

// Run the script
$grid->renderGrid('#deliverylist','#deliverypager',true, null, null, true,true);

?>



