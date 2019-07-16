<?php
session_start();
error_reporting (E_ALL ^ E_NOTICE);
$usersession = $_SESSION['usersession'];

$refid = $_SESSION['s_tradingref'];
$findb = $_SESSION['s_findb'];

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];

$trfile = 'ztmp'.$user_id.'_1tr';

$db->query("drop table if exists ".$findb.".".$trfile);
$db->execute();

$db->query("create table ".$findb.".".$trfile." ( uid int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, itemcode varchar(30) default '',item varchar(100) default '',price decimal(16,2) default 0, unit varchar(20) default '',quantity decimal(16,2) default 0,tax decimal(16,2) default 0, value decimal(16,2) default 0, discount decimal(16,2) default 0, total decimal(16,2) default 0) engine myisam"); 
$db->execute();

$db->query("insert into ".$findb.".".$trfile." select uid,itemcode,item,price,unit,quantity,tax,value,discount,(value-discount+tax) as total from ".$findb.".invtrans where ref_no = '".$refid."'");
$db->execute();

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

// the actual query for the grid data
$grid->SelectCommand = "select uid,itemcode,item,price,unit,quantity,tax,value,discount, total from ".$findb.".".$trfile;

// set the ouput format to json
$grid->dataType = 'json';

// Let the grid create the model
$grid->setColModel(null);

// Set the url from where we obtain the data
$grid->setUrl('getviewtradingtrans.php');

// Set some grid options
$grid->setGridOptions(array(
	"caption"=>"Line Items for ".$refid,						
    "rowNum"=>10,
    "sortname"=>"uid",
	"sortorder"=>"desc",
    "rowList"=>array(10,30,50),
	"height"=>250,
	"width"=>890
    ));

// Enable footerdata an tell the grid to obtain it from the request
$grid->setGridOptions(array("footerrow"=>true,"userDataOnFooter"=>true));

$grid->addCol(array("name"=>"act"),"last");

// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"ID", "width"=>25, "hidden"=>true));
$grid->setColProperty("itemcode", array("label"=>"Item Code", "width"=>60));
$grid->setColProperty("item", array("label"=>"Item", "width"=>130));
$grid->setColProperty("price", array("label"=>"Price", "width"=>70));
$grid->setColProperty("unit", array("label"=>"Unit", "width"=>50));
$grid->setColProperty("quantity", array("label"=>"Qty", "width"=>50, ));
$grid->setColProperty("value", array("label"=>"Value", "width"=>70, "align"=>"right","formatter"=>"number"));
$grid->setColProperty("discount", array("label"=>"Discount", "width"=>70, "align"=>"right","formatter"=>"number"));
$grid->setColProperty("total", array("label"=>"Total", "width"=>70, "align"=>"right","formatter"=>"number"));
$grid->setColProperty("tax", array("label"=>"Tax", "width"=>50, "align"=>"right","formatter"=>"number"));
$grid->setColProperty("act", array("label"=>"Actions", "width"=>35));

$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#tradlist").getDataIDs();
	for(var i=0;i<ids.length;i++){
			var rowd = $("#tradlist").getRowData(ids[i]);
            var cl = ids[i];
			be = '<img src="../images/edit.png" title="Edit item description" onclick="javascript:editdesc('+cl+')" ></ids>';
			jQuery("#tradlist").setRowData(ids[i],{act:be}); 
	}
}
LOADCOMPLETE;

$grid->setGridEvent("loadComplete",$loadevent);

// At end call footerData to put total  label
$grid->callGridMethod('#tradlist', 'footerData', array("set",array("quantity"=>"Total:")));
// Set which parameter to be sumarized
$summaryrows = array("value"=>array("value"=>"SUM"),"total"=>array("total"=>"SUM"),"tax"=>array("tax"=>"SUM"),"discount"=>array("discount"=>"SUM"));


// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>true,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));


// Run the script
$grid->renderGrid('#tradlist','#tradpager',true, $summaryrows, null,true,true,true);
$conn = null;

?>




