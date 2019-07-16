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
$grid->SelectCommand = "select workflow_xref.workflow_xref_id,workflow_xref.ddate,workflow_xref.process,datediff(curdate( ),workflow_xref.ddate) AS diff,workflow.process_id,workflow.aide_memoir from ".$cltdb.".workflow_xref,".$cltdb.".workflow where workflow_xref.process = workflow.process and workflow_xref.member_id = ".$id; 

// set the ouput format to json
$grid->dataType = 'json';

// Let the grid create the model
$grid->setColModel(null);

// Set the url from where we obtain the data
$grid->setUrl('getworkflowm.php');

// Set some grid options
$grid->setGridOptions(array(
	"rowNum"=>12,
    "sortname"=>"ddate",
	"sortorder"=>"asc",
    "rowList"=>array(12,30,50),
	"height"=>95,
	"width"=>700
    ));

$grid->addCol(array("name"=>"act"),"last");


// Change some property of the field(s)
$grid->setColProperty("workflow_xref_id", array("label"=>"ID", "width"=>25, "hidden"=>true));
$grid->setColProperty("process_id", array("label"=>"PID", "width"=>25, "hidden"=>true));
$grid->setColProperty("aide_memoir", array("label"=>"Aide Memoir", "width"=>25, "hidden"=>true));
$grid->setColProperty("ddate", array("label"=>"Date", "width"=>80, "formatter"=>"date", "formatoptions"=>array("srcformat"=> "Y-m-d", "newformat"=>"d/m/Y")));
$grid->setColProperty("process", array("label"=>"Workflow", "width"=>250));
$grid->setColProperty("diff", array("label"=>"Days Outstanding", "width"=>75));
$grid->setColProperty("act", array("label"=>"Actions",  "width"=>50, "sortable"=>false));


$ldevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#mworkflowlist").getDataIDs(); 
	for(var i=0;i<ids.length;i++){ 
		var rowd = $("#mworkflowlist").getRowData(ids[i]);
		var pid = rowd.process_id;
		var cl = ids[i]; 
		se = '<img src="../images/edit.png" title="View Aide Memoire" onclick="javascript:viewaide('+pid+')" >';
		de = '<img src="../images/delete.png" title="Delete Workflow Stage" onclick="javascript:delworkflow('+cl+')" >'; 
		jQuery("#mworkflowlist").setRowData(ids[i],{act:se+'&nbsp;&nbsp;&nbsp'+de});
	} 
}
LOADCOMPLETE;
$grid->setGridEvent("loadComplete",$ldevent);


// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>true,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));
// add a custom button via the build in callGridMethod
// note the js: before the function
$buttonoption1 = array("#mworkflowpager",
    array("buttonicon"=>"ui-icon-plus","caption"=>"","position"=>"first","title"=>"Add an workflow stage", "onClickButton"=>"js: function(){addworkflow(".$id.",'m');}"),
);
$grid->callGridMethod("#mworkflowlist", "navButtonAdd", $buttonoption1); 

// Run the script
$grid->renderGrid('#mworkflowlist','#mworkflowpager',true, null, null,true,true,true);

?>




