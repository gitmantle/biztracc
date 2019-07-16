<?php
session_start();
$usersession = $_SESSION['usersession'];

$findb = $_SESSION['s_findb'];

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

// enable debugging
//$grid->debug = true;

// Write the SQL Query
// We suppose that mytable exists in your database
$grid->SelectCommand = "select ref_no,ddate,client,totvalue from ".$findb.".invhead where ref_no like 'S_O%' and xref != 'Complete'"; 

// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('getsalesorders.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
    "caption"=>"Outstanding Sales Orders",
    "rowNum"=>12,
    "sortname"=>"ref_no",
    "rowList"=>array(12,30,50),
	"height"=>270,
	"width"=>540
    ));


$grid->addCol(array("name"=>"act"),"last");

// Change some property of the field(s)
$grid->setColProperty("ref_no", array("label"=>"Ref.", "width"=>70));
$grid->setColProperty("ddate", array("label"=>"Date", "width"=>80, "formatter"=>"date", "formatoptions"=>array("srcformat"=> "Y-m-d", "newformat"=>"d/m/Y")));
$grid->setColProperty("client", array("label"=>"Client", "width"=>120));
$grid->setColProperty("totvalue", array("label"=>"Value", "width"=>70, "align"=>"right","formatter"=>"number"));
$grid->setColProperty("act", array("label"=>"Actions",  "width"=>90, "sortable"=>false));

$grid->setSubGrid("getsotrans.php",
        array('item', 'quantity', 'unit', 'value', 'supplied'),
        array(180,80,80,80,80),
        array('left','right','left','right', 'right'));

// on select row we should post the member id to second table and trigger it to reload the data
$selectdns = <<<DNS
function(rowid, selected)
{
    if(rowid != null) {
        jQuery("#dnlist").jqGrid('setGridParam',{postData:{son:rowid}});
        jQuery("#dnlist").trigger("reloadGrid");
    }
}
DNS;
$grid->setGridEvent('onSelectRow', $selectdns);

// We should clear the grid data on second grid on sorting, paging, etc.
$cleargrid = <<<CLEAR
function(rowid, selected)
{
   // clear the grid data and footer data
   jQuery("#dnlist").jqGrid('clearGridData',true);
}
CLEAR;

$grid->setGridEvent('onPaging', $cleargrid);
$grid->setGridEvent('onSortCol', $cleargrid);

$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#solist").getDataIDs();
	for(var i=0;i<ids.length;i++){
			var rowd = $("#solist").getRowData(ids[i]);
			var r = rowd.ref_no;
			var rf = "'"+r+"'";
			var cl = ids[i];
			ee = '<img src="../images/edit.png" title="Edit Sales Order" onclick="javascript:editso('+rf+')" ></ids>';
			de = '<img src="../images/delivery.png" title="Add Delivery Note" onclick="javascript:adddo('+rf+')" ></ids>';
			be = '<img src="../images/printer.gif" title="Print Sales Order" onclick="javascript:printdoc('+rf+')" ></ids>';
			pe = '<img src="../images/picklist.png" title="Print Pick List" onclick="javascript:printpicklist('+rf+')" ></ids>';
			jQuery("#solist").setRowData(ids[i],{act:ee+'&nbsp;&nbsp;&nbsp;'+de+'&nbsp;&nbsp;&nbsp;'+be+'&nbsp;&nbsp;&nbsp;'+pe}); 
	}
}
LOADCOMPLETE;

$grid->setGridEvent("loadComplete",$loadevent);

$grid->gSQLMaxRows = 4000;

// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>false,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

// add a custom button via the build in callGridMethod
// note the js: before the function
$buttonoptions = array("#sopager",
    array("buttonicon"=>"ui-icon-plus","caption"=>"","position"=>"first","title"=>"Add a Sales Order", "onClickButton"=>"js: function(){addsalesorder();}")
);
$grid->callGridMethod("#solist", "navButtonAdd", $buttonoptions); 


// Run the script
$grid->renderGrid('#solist','#sopager',true, null, null, true,true);

?>



