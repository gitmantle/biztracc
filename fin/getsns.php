<?php
session_start();
$usersession = $_SESSION['usersession'];

include_once("../includes/DBClass.php");
$dbs = new DBClass();

$dbs->query("select * from sessions where session = :vusersession");
$dbs->bind(':vusersession', $usersession);
$row = $dbs->single();
$subid = $row['subid'];
$user_id = $row['user_id'];

$findb = $_SESSION['s_findb'];
$snfile = 'ztmp'.$user_id.'_sn';

$dbs->closeDB();

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
$grid->SelectCommand = "select uid,itemcode,item,serialno,ddate,ref_in,ref_out,location,branch from ".$findb.".".$snfile; 

// set the ouput format to json
$grid->dataType = 'json';

// Let the grid create the model
$grid->setColModel(null);

// Set the url from where we obtain the data
$grid->setUrl('getsns.php');


// Set some grid options
$grid->setGridOptions(array(
    "caption"=>"Serial Numbers",
    "rowNum"=>15,
    "sortname"=>"serialno",
    "rowList"=>array(15,30,50),
	"height"=>350,
	"width"=>900
    ));

$grid->addCol(array("name"=>"act"),"last");

// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"ID", "width"=>25, "hidden"=>true));
$grid->setColProperty("itemcode", array("label"=>"Item Code", "width"=>80));
$grid->setColProperty("item", array("label"=>"Item", "width"=>170));
$grid->setColProperty("serialno", array("label"=>"Serial No", "width"=>100));
$grid->setColProperty("ddate", array("label"=>"Date", "width"=>80, "formatter"=>"date", "formatoptions"=>array("srcformat"=> "Y-m-d", "newformat"=>"d/m/Y")));
$grid->setColProperty("ref_in", array("label"=>"In on", "width"=>80));
$grid->setColProperty("ref_out", array("label"=>"Out on", "width"=>80));
$grid->setColProperty("location", array("label"=>"Location", "width"=>100));
$grid->setColProperty("branch", array("label"=>"Branch", "width"=>100));
$grid->setColProperty("act", array("label"=>"Actions",  "width"=>55));

$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#snlist").getDataIDs();
	for(var i=0;i<ids.length;i++){
			var rowd = $("#snlist").getRowData(ids[i]);
			var cl = ids[i];
			var pin = "'"+rowd.ref_in+"'";
			var pout = "'"+rowd.ref_out+"'";
			be = '<img src="../images/printer.gif" title="Print/View In Document" onclick="javascript:prin('+pin+')" ></ids>';
			if (pout.length < 4) {
				se = '&nbsp;&nbsp;&nbsp;';
			} else {
				se = '<img src="../images/printer.gif" title="Print/View Out Document" onclick="javascript:prout('+pout+')" ></ids>';
			}
			jQuery("#snlist").setRowData(ids[i],{act:be+'&nbsp;&nbsp;&nbsp;'+se}); 
	}
}
LOADCOMPLETE;

$grid->setGridEvent("loadComplete",$loadevent);

// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>true,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

// Run the script
$grid->renderGrid('#snlist','#snpager',true, null, null,true,true,true);

?>




