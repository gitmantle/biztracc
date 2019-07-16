<?php
session_start();
//ini_set('display_errors', true);

$usersession = $_SESSION['usersession'];

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];

$tradetable = 'ztmp'.$user_id.'_trading';

$findb = $_SESSION['s_findb'];

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
// Get the needed parameters passed from the main grid

// Create the jqGrid instance
$grid = new jqGridRender($conn);

// the actual query for the grid data
$grid->SelectCommand = "select uid,itemcode,item,price,unit,quantity,discount,value,tax,tot,pay,trackserial,ref,origqty from ".$findb.".".$tradetable;

// set the ouput format to json
$grid->dataType = 'json';

// Let the grid create the model
$grid->setColModel(null);

// Set the url from where we obtain the data
$grid->setUrl('getpurchdetails.php');

// Set some grid options
$grid->setGridOptions(array(
    "rowNum"=>5,
    "sortname"=>"item",
    "rowList"=>array(5,30,50),
	"height"=>120,
	"width"=>950
	//"cellEdit"=> true,
	//"mtype" => "POST",
	//"cellsubmit" => "remote",
	//"cellurl" => "includes/ajaxpicked.php"
	));


// Enable footerdata an tell the grid to obtain it from the request
$grid->setGridOptions(array("footerrow"=>true,"userDataOnFooter"=>true));

$grid->addCol(array("name"=>"act"),"last");

// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"ID", "width"=>25, "editable"=>false, "hidden"=>true));
$grid->setColProperty("itemcode", array("label"=>"Item Code", "width"=>60, "editable"=>false));
$grid->setColProperty("item", array("label"=>"Item", "width"=>150, "editable"=>false));
$grid->setColProperty("price", array("label"=>"Price", "width"=>80, "editable"=>false));
$grid->setColProperty("unit", array("label"=>"Unit", "width"=>50, "editable"=>false));
$grid->setColProperty("quantity", array("label"=>"Qty", "width"=>50, "editable"=>false));
//$grid->setColProperty("quantity", array("label"=>"Qty", "width"=>50, "editable"=>true, "editrules"=>array("number"=>true, "maxValue"=>quantity)));
$grid->setColProperty("value", array("label"=>"Value", "width"=>70, "editable"=>false, "editable"=>false, "align"=>"right", "formatter"=>"number","formatoptions"=>array("thousandsSeparator"=>",","decimalPlaces"=>2)));
$grid->setColProperty("discount", array("label"=>"Disc.", "width"=>60, "editable"=>false, "align"=>"right", "formatter"=>"number","formatoptions"=>array("thousandsSeparator"=>",","decimalPlaces"=>2)));
$grid->setColProperty("tax", array("label"=>"Tax", "width"=>50, "editable"=>false, "align"=>"right", "formatter"=>"number","formatoptions"=>array("thousandsSeparator"=>",","decimalPlaces"=>2)));
$grid->setColProperty("tot", array("label"=>"Total", "width"=>50, "editable"=>false, "align"=>"right", "formatter"=>"number","formatoptions"=>array("thousandsSeparator"=>",","decimalPlaces"=>2)));
$grid->setColProperty("pay", array("label"=>"Pay", "width"=>20, "editable"=>false, "hidden"=>true));
$grid->setColProperty("trackserial", array("label"=>"Serial", "width"=>20, "editable"=>false, "hidden"=>true));
$grid->setColProperty("ref", array("label"=>"Ref", "width"=>20, "editable"=>false, "hidden"=>true));
$grid->setColProperty("origqty", array("label"=>"Original Qty", "width"=>25, "editable"=>false, "hidden"=>true));
$grid->setColProperty("act", array("label"=>"Selection", "width"=>50, "editable"=>false));

// At end call footerData to put total  label
$grid->callGridMethod('#purchlist', 'footerData', array("set",array("quantity"=>"Total:")));
// Set which parameter to be sumarized
$summaryrows = array("value"=>array("value"=>"SUM"),"tax"=>array("tax"=>"SUM"),"tot"=>array("tot"=>"SUM"));

//******************************************************************************

$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#purchlist").getDataIDs();
	for(var i=0;i<ids.length;i++){
			var rowd = $("#purchlist").getRowData(ids[i]);
			var cl = ids[i];
			var topay = rowd.pay;
			var track = rowd.trackserial;
			var rf = "'"+rowd.ref+"'";
			var se = "";
			if (topay == 'N') {
				be = '<img src="../images/close.png" title="Add to be refunded" onclick="javascript:purchdeselect('+cl+')" ></ids>';
				de = "&nbsp;&nbsp;&nbsp;";	
			} else {
				be = '<img src="../images/accept.gif" title="De-select" onclick="javascript:purchselect('+cl+')" ></ids>';
				de = '<img src="../images/edit.png" title="Edit Quantity" onclick="javascript:editqty('+cl+')" ></ids>';
				if (track == 'Yes') {
					se = '<img src="../images/barcode.png" title="Purchased serial nos." onclick="javascript:showserial('+cl+','+rf+')" ></ids>';
				}
			}
			jQuery("#purchlist").setRowData(ids[i],{act:be+'&nbsp;&nbsp;&nbsp;'+de+'&nbsp;&nbsp;&nbsp;'+se});  
	}
}
LOADCOMPLETE;
$grid->setGridEvent("loadComplete",$loadevent);

/*
//**************************************************************************
$checkqty = <<<VALIDATEQTY
function (serverresponse, rowid, cellname, value, iRow, iCol) {
          var mymess = serverresponse.responseText;
          var retbool = true;
          if (mymess != '1') { retbool = false; } 
          var myret = [retbool,mymess];
          return myret;
        }
VALIDATEQTY;
$grid->setGridEvent("afterSubmitCell",$checkqty);

//*************************************************************************
$reloadevent = <<<RELOADAFTEREDIT
function(rowid,name,val,iRow,iCol) {
	jQuery("#purchlist").trigger("reloadGrid");
}
RELOADAFTEREDIT;
$grid->setGridEvent("afterSaveCell",$reloadevent);

//*************************************************************************
*/

// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>true,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

// Run the script
$grid->renderGrid('#purchlist','#purchpager',true, $summaryrows, null,true,true,true);
?>




