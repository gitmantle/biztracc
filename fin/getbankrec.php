<?php
session_start();
$usersession = $_SESSION['usersession'];

$showrecon = $_SESSION['s_showreconcilled'];

include_once("../includes/DBClass.php");
$dbb = new DBClass();

$dbb->query("select * from sessions where session = :vusersession");
$dbb->bind(':vusersession', $usersession);
$row = $dbb->single();
$subid = $row['subid'];
$user_id = $row['user_id'];

$findb = $_SESSION['s_findb'];

$bankrectable = 'ztmp'.$user_id.'_bankrec';

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

// the actual query for the grid data
if ($showrecon == 'Y') {
	$grid->SelectCommand = "select uid,ddate,debit,credit,reference,description,reconciled from ".$findb.".".$bankrectable;
} else {
	$grid->SelectCommand = "select uid,ddate,debit,credit,reference,description,reconciled from ".$findb.".".$bankrectable." where reconciled = 'N'";
}

// set the ouput format to json
$grid->dataType = 'json';

// Let the grid create the model
$grid->setColModel(null);

// Set the url from where we obtain the data
$grid->setUrl('getbankrec.php');

// Set some grid options
$grid->setGridOptions(array(
	"caption"=>'Unreconciled items in our accounts',						
    "rowNum"=>100,
    "sortname"=>"ddate",
    "rowList"=>array(100,200),
	"height"=>460,
	"width"=>970
    ));


$grid->addCol(array("name"=>"act"),"last");

// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"ID", "width"=>25, "hidden"=>true));
$grid->setColProperty("ddate", array("label"=>"Date", "width"=>80, "formatter"=>"date", "formatoptions"=>array("srcformat"=> "Y-m-d", "newformat"=>"d/m/Y")));
$grid->setColProperty("debit", array("label"=>"Deposit", "width"=>75, "align"=>"right","formatter"=>"number"));
$grid->setColProperty("credit", array("label"=>"Payment", "width"=>75, "align"=>"right","formatter"=>"number"));
$grid->setColProperty("reference", array("label"=>"Ref.", "width"=>50));
$grid->setColProperty("description", array("label"=>"Description", "width"=>200));
$grid->setColProperty("reconciled", array("label"=>"Reconcilled", "width"=>50));
$grid->setColProperty("act", array("label"=>"Actions", "width"=>50, "sortable"=>false));

$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#bankreclist").getDataIDs();
	for(var i=0;i<ids.length;i++){
			var rowd = $("#bankreclist").getRowData(ids[i]);
			var cl = ids[i];
			be = '<img src="../images/into.png" title="Reconcile" onclick="javascript:recon('+cl+')" ></ids>';
			se = '<img src="../images/outof.png" title="Uneconcile" onclick="javascript:unrecon('+cl+')" ></ids>';
			jQuery("#bankreclist").setRowData(ids[i],{act:be+'&nbsp;&nbsp;&nbsp;'+se}); 
	}
}
LOADCOMPLETE;

$grid->setGridEvent("loadComplete",$loadevent);
// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>true,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

// Run the script
$grid->renderGrid('#bankreclist','#bankrecpager',true, null, null,true,true,true);

$dbb->closeDB();
?>




