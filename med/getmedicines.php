<?php
session_start();
ini_set('display_errors', true);
require("../db.php");

$id = $_SESSION["s_memberid"];

$moduledb = $_SESSION['s_findb'];
mysql_select_db($moduledb) or die(mysql_error());
	
$q = "select pcent from stkpricepcent where uid = 1";
$r = mysql_query($q) or die(mysql_error());
$row = mysql_fetch_array($r);
extract($row);	
$spcent = $pcent;
		
$markup = 1 + $spcent/100;

$findb = $_SESSION['s_findb'];
$meddb = $_SESSION['s_prcdb'];

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


$grid->SelectCommand = "select ".$meddb.".requirements.req_id,".$findb.".stkmast.item,".$meddb.".requirements.qty,".$meddb.".requirements.dosage, ".$meddb.".requirements.instructions, ".$meddb.".requirements.expdate, case ".$findb.".stkmast.setsell when 0 then (".$findb.".stkmast.avgcost * ".$markup." * ".$meddb.".requirements.periodqty) * (1 + (select ".$findb.".taxtypes.taxpcent from ".$findb.".taxtypes where ".$findb.".taxtypes.uid = ".$findb.".stkmast.deftax)/100)  else (".$findb.".stkmast.setsell * ".$meddb.".requirements.periodqty) * (1 + (select ".$findb.".taxtypes.taxpcent from ".$findb.".taxtypes where ".$findb.".taxtypes.uid = ".$findb.".stkmast.deftax)/100)  end as cost from ".$meddb.".requirements,".$findb.".stkmast where ".$meddb.".requirements.medicineid = ".$findb.".stkmast.itemid and ".$meddb.".requirements.patientid = ".$id; 

// set the ouput format to json
$grid->dataType = 'json';

// Let the grid create the model
$grid->setColModel(null);

// Set the url from where we obtain the data
$grid->setUrl('getmedicines.php');

// Set some grid options
$grid->setGridOptions(array(
    "rowNum"=>12,
    "sortname"=>"item",
    "rowList"=>array(12,30,50),
	"height"=>115,
	"width"=>900
    ));

// Enable footerdata an tell the grid to obtain it from the request
$grid->setGridOptions(array("footerrow"=>true,"userDataOnFooter"=>true));

$grid->addCol(array("name"=>"act"),"last");

// Change some property of the field(s)
$grid->setColProperty("req_id", array("label"=>"ID", "width"=>25, "hidden"=>true));
$grid->setColProperty("item", array("label"=>"Medicine", "width"=>200));
$grid->setColProperty("qty", array("label"=>"Quantity", "width"=>50));
$grid->setColProperty("dosage", array("label"=>"Per", "width"=>50));
$grid->setColProperty("instructions", array("label"=>"Instructions", "width"=>250));
$grid->setColProperty("expdate", array("label"=>"Expires", "width"=>75, "formatter"=>"date", "formatoptions"=>array("srcformat"=> "Y-m-d", "newformat"=>"d/m/Y")));
$grid->setColProperty("cost", array("label"=>"Period Cost", "width"=>80, "align"=>"right", "formatter"=>"number","formatoptions"=>array("thousandsSeparator"=>",","decimalPlaces"=>2)));
$grid->setColProperty("act", array("label"=>"Actions",  "width"=>50, "sortable"=>false));


$ldevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#reqlist").getDataIDs(); 
	for(var i=0;i<ids.length;i++){ 
		var cl = ids[i]; 
		be = '<img src="../images/edit.png" title="Edit" onclick="javascript:editmed('+cl+')" >'; 
		se = '<img src="../images/delete.png" title="Delete" onclick="javascript:delmed('+cl+')" >'; 
		jQuery("#reqlist").setRowData(ids[i],{act:be+'&nbsp;&nbsp;&nbsp;'+se}); 
	}
}
LOADCOMPLETE;

$dcevent = <<<DBLCLICK
function(rowid) {
	var rowdata = $("#reqlist").getRowData(rowid);
	var memberid = rowdata.member_id;
	emailmem(rowid,memberid);
}
DBLCLICK;


$grid->setGridEvent("loadComplete",$ldevent);

// At end call footerData to put total  label
$grid->callGridMethod('#reqlist', 'footerData', array("set",array("instructions"=>"Total period cost incl. GST:")));
// Set which parameter to be sumarized
$summaryrows = array("cost"=>array("cost"=>"SUM"));



// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>true,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));
// add a custom button via the build in callGridMethod
// note the js: before the function
$buttonoptions = array("#reqpager",
    array("buttonicon"=>"ui-icon-plus","caption"=>"","position"=>"first","title"=>"Add a medicine.", "onClickButton"=>"js: function(){addmed(".$id.");}")
);
$grid->callGridMethod("#reqlist", "navButtonAdd", $buttonoptions); 

// Run the script
$grid->renderGrid('#reqlist','#reqpager',true,  $summaryrows, null,true,true,true);

?>




