<?php
session_start();
ini_set('display_errors', true);

$id = $_SESSION["s_memberid"];

$cltdb = $_SESSION['s_cltdb'];

//construct where clause 
$where = "WHERE member_id = ".$id; 

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
$grid->SelectCommand = "SELECT complaint_id,member_id,complainant,against,received,acknowledged,closed,compensation from ".$cltdb.".complaints ".$where; 

// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('getccomplaints.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
    "rowNum"=>5,
    "sortname"=>"received",
	"sortorder"=>"desc",
    "rowList"=>array(5,30,50),
	"height"=>115,
	"width"=>900
    ));

$grid->addCol(array("name"=>"act"),"last");


// Change some property of the field(s)
$grid->setColProperty("complaint_id", array("label"=>"ID", "width"=>20, "hidden"=>true));
$grid->setColProperty("member_id", array("label"=>"ID", "width"=>20, "hidden"=>true));
$grid->setColProperty("act", array("label"=>"Actions",  "width"=>80, "sortable"=>false));
$grid->setColProperty("complainant", array("label"=>"Complainant", "width"=>170));
$grid->setColProperty("against", array("label"=>"Against", "width"=>170));
$grid->setColProperty("received", array("label"=>"Received", "width"=>90, "formatter"=>"date", "formatoptions"=>array("srcformat"=> "Y-m-d", "newformat"=>"d/m/Y")));
$grid->setColProperty("acknowledged", array("label"=>"Acknowledged", "formatter"=>"date", "formatoptions"=>array("srcformat"=> "Y-m-d", "newformat"=>"d/m/Y")));
$grid->setColProperty("closed", array("label"=>"Closed", "width"=>90, "formatter"=>"date", "formatoptions"=>array("srcformat"=> "Y-m-d", "newformat"=>"d/m/Y")));
$grid->setColProperty("compensation", array("label"=>"Compensation", "width"=>90, "align"=>"right", "formatter"=>"number","formatoptions"=>array("thousandsSeparator"=>",","decimalPlaces"=>2)));

/*
$ld3event = <<<LD3COMPLETE
function(rowid){
	var ids = jQuery("#mcomplaintslist").getDataIDs(); 
	var totpremium = 0;
	for(var i=0;i<ids.length;i++){ 
		var cl = ids[i]; 
		be = '<img src="../images/edit.png" title="View/Edit" onclick="javascript:editcomplaint('+cl+')" >'; 
		jQuery("#mcomplaintslist").setRowData(ids[i],{act:be});
	} 
}
LD3COMPLETE;

$grid->setGridEvent("loadComplete",$ld3event);


$dcevent = <<<DBLCLICK
function(rowid) {
	var rowdata = $("#mcomplaintslist").getRowData(rowid);
	var memberid = rowdata.member_id;
	editmem(memberid);
}
DBLCLICK;

$grid->setGridEvent('ondblClickRow',$dcevent);
*/

// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>false,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));
// add a custom button via the build in callGridMethod
// note the js: before the function
$buttonoptions = array("#mcomplaintspager",
    array("buttonicon"=>"ui-icon-plus","caption"=>"","position"=>"first","title"=>"Add a complaint", "onClickButton"=>"js: function(){addcomplaint();}")
);
$grid->callGridMethod("#mcomplaintslist", "navButtonAdd", $buttonoptions); 


// Run the script
$grid->renderGrid('#mcomplaintslist','#mcomplaintspager',true, null, null, true,true);

?>




