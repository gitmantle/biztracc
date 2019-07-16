<?php
session_start();
$usersession = $_SESSION['usersession'];

$bx = $_SESSION['s_gstbox'];
$fromdate = $_SESSION['s_gstfdate'];
$todate = $_SESSION['s_gsttdate'];

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];

$findb = $_SESSION['s_findb'];

$glfile = 'ztmp'.$user_id.'_gst'.$bx;

switch ($bx) {
	case 1:
		$heading = "VAT on Sales";
		break;
	case 4:	
		$heading = "VAT on Purchases";
		break;
	case 6:	
		$heading = "Sales excluding VAT";
		break;
	case 7:	
		$heading = "Purchases excluding VAT";
		break;
}

$db->closeDB();

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
$grid->SelectCommand = "select uid,ddate,account,amount,reference,taxpcent,gsttype,descript1 from ".$findb.".".$glfile;

// set the ouput format to json
$grid->dataType = 'json';

// Let the grid create the model
$grid->setColModel(null);

// Set the url from where we obtain the data
$grid->setUrl('getviewgstUK.php');


// Set some grid options
$grid->setGridOptions(array(
    "caption"=>$heading,
    "rowNum"=>15,
    "sortname"=>"ddate",
    "rowList"=>array(15,100,200),
	"height"=>352,
	"width"=>900
    ));

// Enable footerdata an tell the grid to obtain it from the request
$grid->setGridOptions(array("footerrow"=>true,"userDataOnFooter"=>true));

// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"ID", "width"=>25, "hidden"=>true));
$grid->setColProperty("ddate", array("label"=>"Date", "width"=>40, "formatter"=>"date", "formatoptions"=>array("srcformat"=> "Y-m-d", "newformat"=>"d/m/Y")));
$grid->setColProperty("account", array("label"=>"Account", "width"=>100));
$grid->setColProperty("amount", array("label"=>"Amount", "width"=>35, "formatter"=>"number","formatoptions"=>array("thousandsSeparator"=>",","decimalPlaces"=>2)));
$grid->setColProperty("reference", array("label"=>"Ref.", "width"=>30));
$grid->setColProperty("taxpcent", array("label"=>"Tax %", "width"=>30));
$grid->setColProperty("gsttype", array("label"=>"Type", "width"=>30));
$grid->setColProperty("descript1", array("label"=>"Description", "width"=>100));

// At end call footerData to put total  label
$grid->callGridMethod('#gl1list', 'footerData', array("set",array("accountno"=>"Balance:")));
// Set which parameter to be sumarized
$summaryrows = array("amount"=>array("amount"=>"SUM"));

// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>true,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

$buttonoptions2 = array("#gl1pager",
    array("buttonicon"=>"ui-icon-calculator","caption"=>"","position"=>"last","title"=>"Calculators", "onClickButton"=>"js: function(){showCalculators();}")
);
$grid->callGridMethod("#gl1list", "navButtonAdd", $buttonoptions2); 

// Run the script
$grid->renderGrid('#gl1list','#gl1pager',true, $summaryrows, null,true,true,true);
$conn = null;
?>




