<?php
session_start();
//ini_set('display_errors', true);

$usersession = $_SESSION['usersession'];
$admindb = $_SESSION['s_admindb'];
date_default_timezone_set($_SESSION['s_timezone']);

require("../db.php");
mysql_select_db($admindb) or die(mysql_error());
$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);

$cluid = $_SESSION["s_memberid"];

$moduledb = $_SESSION['s_cltdb'];
mysql_select_db($moduledb) or die(mysql_error());

include 'jq-config.php';
// include the jqGrid Class
require_once "../includes/jquery/php/jqGrid.php";
// include the PDO driver class
require_once "../includes/jquery/php/jqGridPdo.php";
// Connection to the server
$conn = new PDO(DB_DSN,DB_USER,DB_PASSWORD);

// Create the jqGrid instance
$grid = new jqGridRender($conn);


// the actual query for the grid data
$grid->SelectCommand = "select doc_id,ddate,doc,staff,subject from attachments";


// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('getattach.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
    "rowNum"=>12,
    "sortname"=>"ddate",
	"sortorder"=>"desc",
    "rowList"=>array(12,30,50),
	"height"=>90,
	"width"=>850
    ));


$grid->addCol(array("name"=>"act"),"first");

// Change some property of the field(s)
$grid->setColProperty("doc_id", array("label"=>"ID", "width"=>20, "hidden"=>true));
$grid->setColProperty("ddate", array("label"=>"Date", "width"=>80, "formatter"=>"date", "formatoptions"=>array("srcformat"=> "Y-m-d", "newformat"=>"d/m/Y")));
$grid->setColProperty("doc", array("label"=>"General Documents", "width"=>200));
$grid->setColProperty("subject", array("label"=>"Subject", "width"=>200));
$grid->setColProperty("staff", array("label"=>"Author", "width"=>150));
$grid->setColProperty("act", array("label"=>"Actions",  "width"=>80, "sortable"=>false));



$loadevent = <<<LOADCOMPLETE
function(rowid){
  var ids = jQuery("#doclistblk").getDataIDs(); 
  for(var i=0;i<ids.length;i++){ 
	  var cl = ids[i]; 
	  var rowdata = $("#doclistblk").getRowData(cl);
	  var d = "'"+rowdata.doc+"'";
	  be = '<img src="../images/attach.jpg" title="Attach Document" onclick="javascript:attachdocb('+d+')" >'; 
	  jQuery("#doclistblk").setRowData(ids[i],{act:be}); 
  } 
}
LOADCOMPLETE;

$grid->setGridEvent("loadComplete",$loadevent);

// enable form editing
$grid->navigator = true;
$grid->setNavOptions('navigator',array('add'=>false,'edit'=>false,'del'=>false,'excel'=>false));

/*
$buttonoption2 = array("#docpagerblk",
    array("buttonicon"=>"ui-icon-mail-closed","caption"=>"","position"=>"last","title"=>"Attach mail merged letter", "onClickButton"=>"js: function(){mailmerge();}")
);
$grid->callGridMethod("#doclistblk", "navButtonAdd", $buttonoption2); 
*/

// Run the script
$grid->renderGrid('#doclistblk','#docpagerblk',true, null, null, true,true);


?>




