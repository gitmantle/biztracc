<?php
error_reporting (E_ALL ^ E_NOTICE ^ E_WARNING);

require_once '../includes/jquery/jq-config.php';
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
// Write the SQL Query
// We suppose that mytable exists in your database
$grid->SelectCommand = "select subid,subname,clt,fin,hrs,prc,man from subscribers";

// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model

// set the primary key - it is serial
$grid->setPrimaryKeyId('subid');

$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('getSubscribers.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
    "caption"=>"Subscribers",
    "rowNum"=>12,
    "sortname"=>"subname",
    "rowList"=>array(12,30,50),
	"width"=>700,
	"height"=>110
    ));

$grid->addCol(array("name"=>"act"),"last");

// Change some property of the field(s)
$grid->setColProperty("subid", array("label"=>"ID", "width"=>20, "hidden"=>true));
$grid->setColProperty("subname", array("label"=>"Subscriber", "width"=>200));
$grid->setColProperty("clt", array("label"=>"Clt", "width"=>20));
$grid->setColProperty("fin", array("label"=>"Fin", "width"=>20));
$grid->setColProperty("hrs", array("label"=>"HR", "width"=>20));
$grid->setColProperty("prc", array("label"=>"Prc", "width"=>20));
$grid->setColProperty("man", array("label"=>"Man", "width"=>20));
$grid->setColProperty("act", array("label"=>"Actions",  "width"=>50, "sortable"=>false));

// on select row we should post the member id to second table and trigger it to reload the data
$selectsub = <<<SUBS
function(rowid, selected)
{
    if(rowid != null) {
        jQuery("#companylist").jqGrid('setGridParam',{postData:{sbid:rowid}});
        jQuery("#companylist").trigger("reloadGrid");
    }
}
SUBS;
$grid->setGridEvent('onSelectRow', $selectsub);

// We should clear the grid data on second grid on sorting, paging, etc.
$cleargrid = <<<CLEAR
function(rowid, selected)
{
   // clear the grid data and footer data
   jQuery("#subscriberlist").jqGrid('clearGridData',true);
}
CLEAR;

$grid->setGridEvent('onPaging', $cleargrid);
$grid->setGridEvent('onSortCol', $cleargrid);



$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#subscriberlist").getDataIDs();
	for(var i=0;i<ids.length;i++){
			var rowd = $("#subscriberlist").getRowData(ids[i]);
			var cl = ids[i];
			be = '<img src="../images/edit.png" title="Edit Subscriber" onclick="javascript:editsubscriber('+cl+')" ></ids>';
			jQuery("#subscriberlist").setRowData(ids[i],{act:be}); 
	}
}
LOADCOMPLETE;

$grid->setGridEvent("loadComplete",$loadevent);

// enable form editing
$grid->navigator = true;
$grid->setNavOptions('navigator',array('add'=>false,'edit'=>false,'del'=>false));

// add a custom button via the build in callGridMethod
// note the js: before the function
$buttonoptions = array("#subscriberpager",
    array("buttonicon"=>"ui-icon-plus","caption"=>"","position"=>"first","title"=>"Add a Subscriber", "onClickButton"=>"js: function(){addsubscriber();}")
);
$grid->callGridMethod("#subscriberlist", "navButtonAdd", $buttonoptions); 

// Run the script
$grid->renderGrid('#subscriberlist','#subscriberpager',true, null, null, true,true);



?>



