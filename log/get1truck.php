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

$moduledb = $_SESSION['s_findb'];
mysql_select_db($moduledb) or die(mysql_error());

$br = $_SESSION['s_truckbr'];

$fromdate = $_SESSION['s_fromdate'];
$todate = $_SESSION['s_todate'];

$where = "";
$where .= " where accountno < 201 and reference not like 'GRN%' and branch = '".$br."'";
$where .= " and ddate >= '".$fromdate."' and ddate <= '".$todate."'";

$q = "select branchname from branch where branch = '".$br."'";
$r = mysql_query($q) or die(mysql_error().' '.$q);
$row = mysql_fetch_array($r);
extract($row);


$heading = $_SESSION['s_coyname'].' - '.$branchname." - between '".$fromdate."' and '".$todate."'";

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
$grid->SelectCommand = "select ddate,accountno,branch,sub,accno,br,subbr,credit,debit,reference,descript1 from trmain ".$where;

// set the ouput format to json
$grid->dataType = 'json';

// Let the grid create the model
$grid->setColModel(null);

// Set the url from where we obtain the data
$grid->setUrl('get1truck.php');


// Set some grid options
$grid->setGridOptions(array(
    "caption"=>$heading,
    "rowNum"=>20,
    "sortname"=>"ddate",
    "rowList"=>array(20,100,200),
	"height"=>450,
	"width"=>900
    ));

// Enable footerdata an tell the grid to obtain it from the request
$grid->setGridOptions(array("footerrow"=>true,"userDataOnFooter"=>true));



// Change some property of the field(s)
$grid->setColProperty("ddate", array("label"=>"Date", "width"=>80, "formatter"=>"date", "formatoptions"=>array("srcformat"=> "Y-m-d", "newformat"=>"d/m/Y")));
$grid->setColProperty("accountno", array("label"=>"Account", "width"=>30));
$grid->setColProperty("branch", array("label"=>"Branch", "width"=>25));
$grid->setColProperty("sub", array("label"=>"Sub", "width"=>25));
$grid->setColProperty("accno", array("label"=>"Acc", "width"=>30));
$grid->setColProperty("br", array("label"=>"Br", "width"=>25));
$grid->setColProperty("subbr", array("label"=>"Sb", "width"=>25));
$grid->setColProperty("credit", array("label"=>"Income", "width"=>40, "align"=>"right"));
$grid->setColProperty("debit", array("label"=>"Costs", "width"=>40, "align"=>"right"));
$grid->setColProperty("reference", array("label"=>"Ref.", "width"=>25));
$grid->setColProperty("descript1", array("label"=>"Description", "width"=>100));


// At end call footerData to put total  label
$grid->callGridMethod('#trucktranslist', 'footerData', array("set",array("acc"=>"Balances:")));
// Set which parameter to be sumarized
$summaryrows = array("debit"=>array("debit"=>"SUM"),"credit"=>array("credit"=>"SUM"));


// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>true,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

$buttonoptions2 = array("#trucktranspager",
    array("buttonicon"=>"ui-icon-calculator","caption"=>"","position"=>"last","title"=>"Calculators", "onClickButton"=>"js: function(){showCalculators();}")
);
$grid->callGridMethod("#trucktranslist", "navButtonAdd", $buttonoptions2); 

// Run the script
$grid->renderGrid('#trucktranslist','#trucktranspager',true, $summaryrows, null,true,true,true);
$conn = null;

?>




