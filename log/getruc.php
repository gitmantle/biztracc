<?php
session_start();

$vn = $_SESSION['s_vehicleno'];

$admindb = $_SESSION['s_admindb'];

require_once('../db.php');
mysql_select_db($admindb) or die(mysql_error());

$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);
$administrator = $admin;


$moduledb = $_SESSION['s_logdb'];
mysql_select_db($moduledb) or die(mysql_error());

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
// Write the SQL Query
// We suppose that mytable exists in your database
$grid->SelectCommand = "select uid,ruclicence,date_issued,fromkms,ruckms,hubodometer,'".$administrator."' as admin from rucs where vehicleno = '".$vn."'";

// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model

// set the primary key - it is serial
$grid->setPrimaryKeyId('uid');

$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('getruc.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
    "caption"=>"RUCs for ".$vn,
    "rowNum"=>15,
    "sortname"=>"ruclicence",
    "rowList"=>array(15,50,80),
	"width"=>860,
	"height"=>350
    ));

$grid->addCol(array("name"=>"act"),"last");

// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"ID", "width"=>20, "hidden"=>true));
$grid->setColProperty("ruclicence", array("label"=>"RUC licence number", "width"=>100));
$grid->setColProperty("date_issued", array("label"=>"Date Issued", "width"=>70, "formatter"=>"date", "formatoptions"=>array("srcformat"=> "Y-m-d", "newformat"=>"d/m/Y")));
$grid->setColProperty("fromkms", array("label"=>"RUC from Kms", "width"=>100));
$grid->setColProperty("ruckms", array("label"=>"RUC valid to Kms", "width"=>100));
$grid->setColProperty("hubodometer", array("label"=>"Hubodometer", "width"=>100));
$grid->setColProperty("admin", array("label"=>"Admin", "width"=>20, "hidden"=>true));
$grid->setColProperty("act", array("label"=>"Actions", "width"=>60));


$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#ruclist").getDataIDs();
	for(var i=0;i<ids.length;i++){
			var rowd = $("#ruclist").getRowData(ids[i]);
			var cl = ids[i];
			var ad = rowd.admin;
			var rlic = "'"+rowd.ruclicence+"'";
			be = '<img src="../images/edit.png" title="Edit Details" onclick="javascript:editrucdetails('+cl+')" ></ids>';
			se = '<img src="../images/delete.png" title="Delete Details" onclick="javascript:delrucdetails('+cl+')" >'; 
			//if (ad == 'Y') {
				//ae = '<img src="../images/add.gif" title="Add RUC refund to this licence" onclick="javascript:addrucrefund('+rlic+')" ></ids>';
			//} else {
				//ae = '&nbsp;&nbsp;&nbsp;';
			//}
			jQuery("#ruclist").setRowData(ids[i],{act:be+'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+se}); 
	}
}
LOADCOMPLETE;

$grid->setGridEvent("loadComplete",$loadevent);

// enable form editing
$grid->navigator = true;
$grid->setNavOptions('navigator', array("excel"=>true,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

// add a custom button via the build in callGridMethod
// note the js: before the function
$buttonoptions = array("#rucpager",
    array("buttonicon"=>"ui-icon-plus","caption"=>"","position"=>"first","title"=>"Add details", "onClickButton"=>"js: function(){addrucdetails();}")
);
$grid->callGridMethod("#ruclist", "navButtonAdd", $buttonoptions); 

// Run the script
$grid->renderGrid('#ruclist','#rucpager',true, null, null, true,true);
?>



