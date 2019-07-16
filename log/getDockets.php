<?php
session_start();

$usersession = $_SESSION['usersession'];

$admindb = $_SESSION['s_admindb'];

require_once('../db.php');
mysql_select_db($admindb) or die(mysql_error());

$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);


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
$grid->SelectCommand = "select docket_id,docket_no,ddate,forest,cpt,skid,customer,destination,truck,trailer,net,amount,invoice from dockets";

// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model

// set the primary key - it is serial
$grid->setPrimaryKeyId('docket_id');

$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('getDockets.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
    "caption"=>"Dockets",
    "rowNum"=>15,
    "sortname"=>"ddate",
    "rowList"=>array(15,50,80),
	"width"=>950,
	"height"=>350
    ));

$grid->addCol(array("name"=>"act"),"last");

// Change some property of the field(s)
$grid->setColProperty("docket_id", array("label"=>"ID", "width"=>20, "hidden"=>true));
$grid->setColProperty("docket_no", array("label"=>"Docket", "width"=>50));
$grid->setColProperty("ddate", array("label"=>"Date", "width"=>80, "formatter"=>"date", "formatoptions"=>array("srcformat"=> "Y-m-d", "newformat"=>"d/m/Y")));
$grid->setColProperty("forest", array("label"=>"Forest", "width"=>90));
$grid->setColProperty("cpt", array("label"=>"CPT", "width"=>30));
$grid->setColProperty("skid", array("label"=>"Skid", "width"=>40));
$grid->setColProperty("customer", array("label"=>"Customer", "width"=>100));
$grid->setColProperty("destination", array("label"=>"Destination", "width"=>80));
$grid->setColProperty("truck", array("label"=>"Truck", "width"=>80));
$grid->setColProperty("trailer", array("label"=>"Trailer", "width"=>80));
$grid->setColProperty("net", array("label"=>"Net Kg", "width"=>50, "formatter"=>"number","formatoptions"=>array("thousandsSeparator"=>",","decimalPlaces"=>3)));
$grid->setColProperty("amount", array("label"=>"Amount $", "width"=>55, "formatter"=>"number","formatoptions"=>array("thousandsSeparator"=>",","decimalPlaces"=>2)));
$grid->setColProperty("invoice", array("label"=>"Invoice", "width"=>50));
$grid->setColProperty("act", array("label"=>"Actions", "width"=>60));


$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#docketlist").getDataIDs();
	for(var i=0;i<ids.length;i++){
			var rowd = $("#docketlist").getRowData(ids[i]);
			var cl = ids[i];
			be = '<img src="../images/edit.png" title="Edit Docket" onclick="javascript:editdocket('+cl+')" ></ids>';
			se = '<img src="../images/delete.png" title="Delete Docket" onclick="javascript:deldocket('+cl+')" >'; 
			jQuery("#docketlist").setRowData(ids[i],{act:be+'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+se}); 
	}
}
LOADCOMPLETE;

$grid->setGridEvent("loadComplete",$loadevent);

// enable form editing
$grid->navigator = true;
$grid->setNavOptions('navigator', array("excel"=>true,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

// add a custom button via the build in callGridMethod
// note the js: before the function
$buttonoptions = array("#docketpager",
    array("buttonicon"=>"ui-icon-plus","caption"=>"","position"=>"first","title"=>"Add a docket", "onClickButton"=>"js: function(){adddocket();}")
);
$grid->callGridMethod("#docketlist", "navButtonAdd", $buttonoptions); 

$buttonoptions1 = array("#docketpager",
    array("buttonicon"=>"ui-icon-extlink","caption"=>"","position"=>"last","title"=>"Import Invoice Amounts from Spreadsheet", "onClickButton"=>"js: function(){impinv();}")
);
$grid->callGridMethod("#docketlist", "navButtonAdd", $buttonoptions1); 

$buttonoptions2 = array("#docketpager",
    array("buttonicon"=>"ui-icon-script","caption"=>"","position"=>"last","title"=>"Generate Invoices", "onClickButton"=>"js: function(){addinvoices();}")
);
$grid->callGridMethod("#docketlist", "navButtonAdd", $buttonoptions2); 

// Run the script
$grid->renderGrid('#docketlist','#docketpager',true, null, null, true,true);
?>



