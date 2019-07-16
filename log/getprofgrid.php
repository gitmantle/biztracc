<?php
session_start();
$usersession = $_SESSION['usersession'];

$admindb = $_SESSION['s_admindb'];
require("../db.php");
mysql_select_db($admindb) or die(mysql_error());
$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);

$tbfile = 'ztmp'.$user_id.'_prof';

$moduledb = $_SESSION['s_findb'];
mysql_select_db($moduledb) or die(mysql_error());

$heading = $_SESSION['s_coyname'].' - '.$_SESSION['s_tbheading'];

include '../fin/jq-config.php';

// include the jqGrid Class
require_once "../includes/jquery/php/jqGrid.php";
// include the PDO driver class
require_once "../includes/jquery/php/jqGridPdo.php";
// Connection to the server
$conn = new PDO(DB_DSN,DB_USER,DB_PASSWORD);

// Create the jqGrid instance
$grid = new jqGridRender($conn);


// the actual query for the grid data
$grid->SelectCommand = "select branch,truckno,income,cost,pl from ".$tbfile;

// set the ouput format to json
$grid->dataType = 'json';

// Let the grid create the model
$grid->setColModel(null);

// Set the url from where we obtain the data
$grid->setUrl('getprofgrid.php');


// Set some grid options
$grid->setGridOptions(array(
    "caption"=>$heading,
    "rowNum"=>20,
    "sortname"=>"branch",
    "rowList"=>array(20,100,200),
	"height"=>460,
	"width"=>890
    ));

$grid->addCol(array("name"=>"act"),"last");

// Enable footerdata an tell the grid to obtain it from the request
$grid->setGridOptions(array("footerrow"=>true,"userDataOnFooter"=>true));

// Change some property of the field(s)
$grid->setColProperty("branch", array("label"=>"Branch", "width"=>25, "hidden"=>true));
$grid->setColProperty("truckno", array("label"=>"Truck/Trailer", "width"=>70));
$grid->setColProperty("income", array("label"=>"Income", "width"=>40, "align"=>"right","formatter"=>"number"));
$grid->setColProperty("cost", array("label"=>"Cost", "width"=>40, "align"=>"right","formatter"=>"number"));
$grid->setColProperty("pl", array("label"=>"Profit/Loss", "width"=>40, "align"=>"right","formatter"=>"number"));
$grid->setColProperty("act", array("label"=>"Actions", "width"=>20));


// At end call footerData to put total  label
$grid->callGridMethod('#proflist', 'footerData', array("set",array("truckno"=>"Balances:")));
// Set which parameter to be sumarized
$summaryrows = array("income"=>array("income"=>"SUM"),"cost"=>array("cost"=>"SUM"),"pl"=>array("pl"=>"SUM"));

$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#proflist").getDataIDs();
	for(var i=0;i<ids.length;i++){
			var rowd = $("#proflist").getRowData(ids[i]);
			var cl = ids[i];
			var brn = rowd.branch;
			var br = "'"+brn+"'";
			be = '<img src="../images/into.png" title="View Transactions" onclick="javascript:viewtrucktrans('+br+')" ></ids>';
			jQuery("#proflist").setRowData(ids[i],{act:be}); 
	}
}
LOADCOMPLETE;

$grid->setGridEvent("loadComplete",$loadevent);


// add a custom button via the build in callGridMethod
// note the js: before the function
$buttonoptions = array("#proflistpager",
    array("buttonicon"=>"ui-icon-newwin","caption"=>"","position"=>"last","title"=>"Output to Excel.", "onClickButton"=>"js: function(){prof2xl();}")
);
$grid->callGridMethod("#proflist", "navButtonAdd", $buttonoptions); 

$buttonoptions2 = array("#proflistpager",
    array("buttonicon"=>"ui-icon-calculator","caption"=>"","position"=>"last","title"=>"Calculators", "onClickButton"=>"js: function(){showCalculators();}")
);
$grid->callGridMethod("#proflist", "navButtonAdd", $buttonoptions2); 



// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>false,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

// Run the script
$grid->renderGrid('#proflist','#proflistpager',true, $summaryrows, null,true,true,true);
$conn = null;

?>




