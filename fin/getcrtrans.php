<?php
session_start();
//ini_set('display_errors', true);

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
// Get the needed parameters passed from the main grid

if(isset ($_REQUEST["cid"])) {
    $id = jqGridUtils::Strip($_REQUEST["cid"]);
	$i = explode('~',$id);
	$craccno = $i[0];
	$crsubno = $i[1];
} else {
    $craccno = 0;
	$crsubno = 0;
}



// Create the jqGrid instance
$grid = new jqGridRender($conn);

// the actual query for the grid data
//$grid->SelectCommand = "select ref_no,uid,ddate,totvalue,tax from ".$findb.".invhead where accountno = ".$craccno." and sub = ".$crsubno;
$grid->SelectCommand = "select reference,uid,ddate,descript1,debit,credit from ".$findb.".trmain where accountno = ".$craccno." and sub = ".$crsubno;

// set the ouput format to json
$grid->dataType = 'json';

// Let the grid create the model
$grid->setColModel(null);

// Set the url from where we obtain the data
$grid->setUrl('getcrtrans.php');

// Set some grid options
$grid->setGridOptions(array(
    "rowNum"=>7,
    "sortname"=>"ddate",
	"sortorder"=>"desc",
    "rowList"=>array(7,50,100),
	"height"=>155,
	"width"=>750
    ));


$grid->addCol(array("name"=>"act"),"last");

/*
// Change some property of the field(s)
$grid->setColProperty("ref_no", array("label"=>"Reference", "width"=>50));
$grid->setColProperty("uid", array("label"=>"ID", "width"=>25, "hidden"=>true));
$grid->setColProperty("ddate", array("label"=>"Date", "width"=>100, "formatter"=>"date", "formatoptions"=>array("srcformat"=> "Y-m-d", "newformat"=>"d/m/Y")));
$grid->setColProperty("totvalue", array("label"=>"Value", "width"=>100, "align"=>"right","formatter"=>"number"));
$grid->setColProperty("tax", array("label"=>"Tax", "width"=>100, "align"=>"right","formatter"=>"number"));
$grid->setColProperty("act", array("label"=>"Actions",  "width"=>50, "sortable"=>false));
*/

$grid->setColProperty("reference", array("label"=>"Reference", "width"=>50));
$grid->setColProperty("uid", array("label"=>"ID", "width"=>25, "hidden"=>true));
$grid->setColProperty("ddate", array("label"=>"Date", "width"=>60, "formatter"=>"date", "formatoptions"=>array("srcformat"=> "Y-m-d", "newformat"=>"d/m/Y")));
$grid->setColProperty("descript1", array("label"=>"Description", "width"=>100));
$grid->setColProperty("debit", array("label"=>"Debit", "width"=>50, "align"=>"right","formatter"=>"number"));
$grid->setColProperty("credit", array("label"=>"Credit", "width"=>50, "align"=>"right","formatter"=>"number"));
$grid->setColProperty("act", array("label"=>"Actions",  "width"=>50, "sortable"=>false));

$grid->setSubGrid("getAllocations.php",
        array('Date', 'From_Ref', 'To_Ref', 'Amount'),
        array(80,80,70,70),
        array('left','left','left','right'));

$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#crtranslist").getDataIDs();
	for(var i=0;i<ids.length;i++){
			var rowd = $("#crtranslist").getRowData(ids[i]);
			var cl = ids[i];
			var rf = "'"+rowd.reference+"'";
			be = '<img src="../images/into.png" title="View Transaction Details" onclick="javascript:viewtrans('+rf+')" ></ids>';
			se = '<img src="../images/printer.gif" title="Print Document" onclick="javascript:printdoc('+rf+')" ></ids>';
			jQuery("#crtranslist").setRowData(ids[i],{act:be+'&nbsp;&nbsp;&nbsp;'+se}); 
	}
}
LOADCOMPLETE;

$grid->setGridEvent("loadComplete",$loadevent);
// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>true,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

// Run the script
$grid->renderGrid('#crtranslist','#crtranspager',true, null, null,true,true,true);
?>




