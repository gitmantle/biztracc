<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
//ini_set('display_errors', true);
$usersession = $_SESSION['usersession'];
$localcurrency = $_SESSION['s_localcurrency'];
$findb = $_SESSION['s_findb'];
include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];

$table = 'ztmp'.$user_id.'_trading';

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
$grid->SelectCommand = "select uid,itemcode,item,currency,price*rate as price,unit,quantity,discount,value*rate as value,tax*rate as tax,tot*rate as tot from ".$findb.".".$table;

// set the ouput format to json
$grid->dataType = 'json';

// Let the grid create the model
$grid->setColModel(null);

// Set the url from where we obtain the data
$grid->setUrl('gettradingtrans.php');

// Set some grid options
$grid->setGridOptions(array(
	"caption"=>"Items",						
    "rowNum"=>30,
    "sortname"=>"uid",
	"sortorder"=>"desc",
    "rowList"=>array(5,30,50),
	"height"=>300,
	"width"=>950,
	"cellEdit"=> true,
	"mtype" => "POST",
	"cellsubmit" => "remote",
	"cellurl" => "includes/ajaxdescript.php"
    ));

// Enable footerdata an tell the grid to obtain it from the request
$grid->setGridOptions(array("footerrow"=>true,"userDataOnFooter"=>true));


$grid->addCol(array("name"=>"act"),"last");

// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"ID", "width"=>25, "editable"=>false, "hidden"=>true));
$grid->setColProperty("itemcode", array("label"=>"Item Code", "width"=>60, "editable"=>false));
$grid->setColProperty("item", array("label"=>"Item (click to edit)", "width"=>150, "editable"=>true));
$grid->setColProperty("currency", array("label"=>" ", "width"=>20, "editable"=>false));
$grid->setColProperty("price", array("label"=>"Price", "width"=>80, "editable"=>false, "align"=>"right", "formatter"=>"number","formatoptions"=>array("thousandsSeparator"=>",","decimalPlaces"=>2)));
$grid->setColProperty("unit", array("label"=>"Unit", "width"=>50, "editable"=>false));
$grid->setColProperty("quantity", array("label"=>"Qty", "align"=>"right", "width"=>50, "editable"=>false ));
$grid->setColProperty("value", array("label"=>"Value", "width"=>70, "editable"=>false, "align"=>"right", "formatter"=>"number","formatoptions"=>array("thousandsSeparator"=>",","decimalPlaces"=>2)));
$grid->setColProperty("discount", array("label"=>"Disc.", "width"=>60, "editable"=>false, "align"=>"right", "formatter"=>"number","formatoptions"=>array("thousandsSeparator"=>",","decimalPlaces"=>2)));
$grid->setColProperty("tax", array("label"=>"Tax", "width"=>50, "editable"=>false, "align"=>"right", "formatter"=>"number","formatoptions"=>array("thousandsSeparator"=>",","decimalPlaces"=>2)));
$grid->setColProperty("tot", array("label"=>"Total", "width"=>50, "editable"=>false, "align"=>"right", "formatter"=>"number","formatoptions"=>array("thousandsSeparator"=>",","decimalPlaces"=>2)));
$grid->setColProperty("act", array("label"=>"Actions",  "width"=>50, "editable"=>false, "sortable"=>false));


// At end call footerData to put total  label
$grid->callGridMethod('#tradlist', 'footerData', array("set",array("quantity"=>"Total: ".$localcurrency)));
// Set which parameter to be sumarized
$summaryrows = array("value"=>array("value"=>"SUM"),"tax"=>array("tax"=>"SUM"),"discount"=>array("discount"=>"SUM"),"tot"=>array("tot"=>"SUM"));


$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#tradlist").getDataIDs();
	for(var i=0;i<ids.length;i++){
		var rowd = $("#tradlist").getRowData(ids[i]);
		var cl = ids[i];
		var ac = rowd.accountno;
		var br = "'"+rowd.branch+"'";
		var dm = rowd.item;
		var dn = dm.substring(0,3);
		if (dn != 'D_N'){
			be = '<img src="../images/edit.png" title="Edit Line Item" onclick="javascript:editlineitem('+cl+')" ></ids>';
			se = '<img src="../images/delete.png" title="Delete Line Item" onclick="javascript:dellineitem('+cl+')" ></ids>';
			jQuery("#tradlist").setRowData(ids[i],{act:be+'&nbsp;&nbsp;&nbsp;'+se}); 
		}
	}
}
LOADCOMPLETE;

$grid->setGridEvent("loadComplete",$loadevent);
// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>true,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

$buttonoptions2 = array("#tradpager",
    array("buttonicon"=>"ui-icon-calculator","caption"=>"","position"=>"last","title"=>"Calculators", "onClickButton"=>"js: function(){showCalculators();}")
);
$grid->callGridMethod("#tradlist", "navButtonAdd", $buttonoptions2); 


// Run the script
$grid->renderGrid('#tradlist','#tradpager',true, $summaryrows, null,true,true,true);
$conn = null;


?>




