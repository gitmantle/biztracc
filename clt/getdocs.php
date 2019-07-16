<?php
session_start();
ini_set('display_errors', true);

$id = $_SESSION["s_memberid"];

$cltdb = $_SESSION['s_cltdb'];

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
$grid->SelectCommand = "select doc_id,member_id,ddate,doc,staff,subject from ".$cltdb.".documents where member_id = ".$id; 

// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('getdocs.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
    "rowNum"=>12,
    "sortname"=>"ddate",
    "rowList"=>array(12,30,50),
	"height"=>115,
	"width"=>800
    ));

$grid->addCol(array("name"=>"act"),"last");

// Change some property of the field(s)
$grid->setColProperty("doc_id", array("label"=>"ID", "width"=>20, "hidden"=>true));
$grid->setColProperty("ddate", array("label"=>"Date", "width"=>80, "formatter"=>"date", "formatoptions"=>array("srcformat"=> "Y-m-d", "newformat"=>"d/m/Y")));
$grid->setColProperty("doc", array("label"=>"Document", "width"=>200));
$grid->setColProperty("subject", array("label"=>"Subject", "width"=>200));
$grid->setColProperty("staff", array("label"=>"Staff Member", "width"=>150));
$grid->setColProperty("member_id", array("label"=>"Member ID", "width"=>50, "hidden"=>true));
$grid->setColProperty("act", array("label"=>"Actions",  "width"=>80, "sortable"=>false));


$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#mdoclist").getDataIDs(); 
	for(var i=0;i<ids.length;i++){ 
		var cl = ids[i]; 
		var rowdata = $("#mdoclist").getRowData(cl);
		var d = "'"+rowdata.member_id+"__"+rowdata.doc+"'";
		be = '<img src="../images/edit.png" title="View Document" onclick="javascript:editdoc('+d+')" >'; 
		cp = '<img src="../images/copypaste.png" title="Copy to Desktop" onclick="javascript:copypaste('+d+')" >'; 
		se = '<img src="../images/delete.png" title="Delete Document" onclick="javascript:deldoc('+cl+')" />'; 
		jQuery("#mdoclist").setRowData(ids[i],{act:be+'&nbsp;&nbsp;&nbsp;'+cp+'&nbsp;&nbsp;&nbsp;'+se}); 
	} 
}
LOADCOMPLETE;

$grid->setGridEvent("loadComplete",$loadevent);

// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>true,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));
// add a custom button via the build in callGridMethod
// note the js: before the function
$buttonoptions = array("#mdocpager",
    array("buttonicon"=>"ui-icon-plus","caption"=>"","position"=>"first","title"=>"Add a document", "onClickButton"=>"js: function(){adddoc('m');}")
);
$grid->callGridMethod("#mdoclist", "navButtonAdd", $buttonoptions); 

// Run the script
$grid->renderGrid('#mdoclist','#mdocpager',true, null, null, true,true);

?>




