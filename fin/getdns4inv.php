<?php
session_start();
$usersession = $_SESSION['usersession'];

$findb = $_SESSION['s_findb'];

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];

$table = 'ztmp'.$user_id.'_dns';

$db->closeDB();

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
$grid->SelectCommand = "select ref_no,accountno,sub,ddate,client,currency,totvalue*rate as totvalue,invoice,selected from ".$findb.".".$table; 


// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('getdns4inv.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
    "caption"=>"Outstanding Delivery Notes",
    "rowNum"=>5,
    "sortname"=>"client",
    "rowList"=>array(5,30,50),
	"height"=>130,
	"width"=>800
    ));


$grid->addCol(array("name"=>"act"),"last");

// Change some property of the field(s)
$grid->setColProperty("ref_no", array("label"=>"Ref.", "width"=>70));
$grid->setColProperty("accountno", array("label"=>"Accountno", "width"=>70, "hidden"=>true));
$grid->setColProperty("sub", array("label"=>"Sub", "width"=>70, "hidden"=>true));
$grid->setColProperty("ddate", array("label"=>"Date", "width"=>80, "formatter"=>"date", "formatoptions"=>array("srcformat"=> "Y-m-d", "newformat"=>"d/m/Y")));
$grid->setColProperty("client", array("label"=>"Client", "width"=>200));
$grid->setColProperty("currency", array("label"=>" ", "width"=>50));
$grid->setColProperty("totvalue", array("label"=>"Value", "width"=>80, "align"=>"right","formatter"=>"number"));
$grid->setColProperty("invoice", array("label"=>"Invoiced", "width"=>70, "hidden"=>true));
$grid->setColProperty("selected", array("label"=>"Selected", "width"=>40, "hidden"=>true));
$grid->setColProperty("act", array("label"=>"Actions",  "width"=>50, "sortable"=>false));

// on select row we should post the member id to second table and trigger it to reload the data
$selectdns = <<<DNS
function(rowid, selected)
{
    if(rowid != null) {
        jQuery("#dnrowlist").jqGrid('setGridParam',{postData:{son:rowid}});
        jQuery("#dnrowlist").trigger("reloadGrid");
    }
}
DNS;
$grid->setGridEvent('onSelectRow', $selectdns);

// We should clear the grid data on second grid on sorting, paging, etc.
$cleargrid = <<<CLEAR
function(rowid, selected)
{
   // clear the grid data and footer data
   jQuery("#dnrowlist").jqGrid('clearGridData',true);
}
CLEAR;

$grid->setGridEvent('onPaging', $cleargrid);
$grid->setGridEvent('onSortCol', $cleargrid);

$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#d4ilist").getDataIDs();
	for(var i=0;i<ids.length;i++){
			var rowd = $("#d4ilist").getRowData(ids[i]);
			var r = rowd.ref_no;
			var rf = "'"+r+"'";
			var cl = ids[i];
			var sel = rowd.selected;
			if (sel != 'Y') {
				de = '<img src="../images/close.png" title="Tick to include in invoice" onclick="javascript:dnselect('+rf+')" ></ids>';
			} else {
				de = '<img src="../images/accept.gif" title="X to exclude from invoice" onclick="javascript:dndeselect('+rf+')" ></ids>';
			}			
			be = '<img src="../images/printer.gif" title="Print Delivery Note" onclick="javascript:printdoc('+rf+')" ></ids>';
			jQuery("#d4ilist").setRowData(ids[i],{act:de+'&nbsp;&nbsp;&nbsp;'+be}); 
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
$grid->renderGrid('#d4ilist','#d4ipager',true, null, null, true,true);

?>



