<?php
session_start();


$admindb = $_SESSION['s_admindb'];

require_once('../db.php');
mysql_select_db($admindb) or die(mysql_error());

$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);

$serialtable = 'ztmp'.$user_id.'_vtyres';

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
$grid->SelectCommand = "select uid,itemcode,item,serialno,ddate,activity,ref_no from ".$moduledb.".".$serialtable;

// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model

$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('getvtyres.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
    "caption"=>"Tyres",
    "rowNum"=>15,
    "sortname"=>"serialno",
    "rowList"=>array(15,50,80),
	"width"=>860,
	"height"=>350
    ));

$grid->addCol(array("name"=>"act"),"last");

// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"ID", "width"=>80, "hidden"=> true));
$grid->setColProperty("itemcode", array("label"=>"Item Code", "width"=>80));
$grid->setColProperty("serialno", array("label"=>"Serial No", "width"=>100));
$grid->setColProperty("item", array("label"=>"Item", "width"=>150));
$grid->setColProperty("ddate", array("label"=>"Date", "width"=>80, "formatter"=>"date", "formatoptions"=>array("srcformat"=> "Y-m-d", "newformat"=>"d/m/Y")));
$grid->setColProperty("activity", array("label"=>"Activity", "width"=>150));
$grid->setColProperty("ref_no", array("label"=>"Reference", "width"=>80));
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
// enable form editing
$grid->navigator = true;
$grid->setNavOptions('navigator', array("excel"=>true,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

// Run the script
$grid->renderGrid('#tyrelist','#tyrepager',true, null, null, true,true);
?>



