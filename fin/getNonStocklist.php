<?php
session_start();

$nstk = $_SESSION['s_nstk'];
$bdt = $_SESSION['s_bdt'];
$edt = $_SESSION['s_edt'];

$findb = $_SESSION['s_findb'];

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

// Check to see if we are in search mode
$sSearch = jqGridUtils::GetParam("_search");
$sAction="";

$sSearchFilters="";


// First visit to page
if(!strlen($sSearch))
{
    $sAction="";
    if(!empty($_SESSION['s_resultslist_filters']))
        $sSearchFilters=$_SESSION['s_resultslist_filters'];
}
// Assume clearing
else if($sSearch=="false")
{                       
    $sAction="clearSearch";    
    $_SESSION['s_searchfilter'] = ""; 
    
    if(isset($_SESSION['s_resultslist_filters']))
        unset($_SESSION['s_resultslist_filters']);
    $sSearchFilters="";    
}
// Search was triggered
else
{   
    $sAction="triggerSearch";    
    $where = $grid->buildSearch( '' );
    $_SESSION['s_searchfilter'] = $where;
	$objFilters=false;
		if(!empty($_GET['filters']))
			$objFilters=json_decode($_GET['filters'],true);
    $objFilter=$objFilters;
    if($objFilter)
        $_SESSION['s_resultslist_filters']=json_encode($objFilter);    
}

$grid->SelectCommand = "select h.ddate, h.client, i.ref_no, i.item, i.quantity, i.unit, i.value from ".$findb.".invtrans i, ".$findb.".invhead h, ".$findb.".stkmast s where (i.ref_no = h.ref_no) and (i.itemcode = s.itemcode) and (s.stock = 'Service') and (h.ddate >= '".$bdt."' and h.ddate <= '".$edt."') and i.itemcode = '".$nstk."'";

// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('getNonStocklist.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
    "caption"=>"Non Stock List",
    "rowNum"=>20,
    "sortname"=>"ddate",
    "rowList"=>array(20,50,100),
	"height"=>460,
	"width"=>880
    ));

// Enable footerdata an tell the grid to obtain it from the request
$grid->setGridOptions(array("footerrow"=>true,"userDataOnFooter"=>true));

// Change some property of the field(s)
$grid->setColProperty("ddate", array("label"=>"Date", "width"=>80, "formatter"=>"date", "formatoptions"=>array("srcformat"=> "Y-m-d", "newformat"=>"d/m/Y")));
$grid->setColProperty("client", array("label"=>"Client", "width"=>100));
$grid->setColProperty("ref_no", array("label"=>"Reference", "width"=>70));
$grid->setColProperty("item", array("label"=>"Item", "width"=>100));
$grid->setColProperty("quantity", array("label"=>"Quantity", "width"=>60));
$grid->setColProperty("unit", array("label"=>"Unit", "width"=>60));
$grid->setColProperty("value", array("label"=>"Value", "width"=>50, "align"=>"right","formatter"=>"number"));

// At end call footerData to put total  label
$grid->callGridMethod('#nstklist', 'footerData', array("set",array("unit"=>"Total:")));
// Set which parameter to be sumarized
$summaryrows = array("value"=>array("value"=>"SUM"));


$grid->gSQLMaxRows = 4000;

// add a custom button via the build in callGridMethod
// note the js: before the function
$buttonoptions = array("#nstkpager",
    array("buttonicon"=>"ui-icon-newwin","caption"=>"","position"=>"last","title"=>"Output to Excel.", "onClickButton"=>"js: function(){tst2xl();}")
);
$grid->callGridMethod("#nstklist", "navButtonAdd", $buttonoptions); 

// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>false,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

// Run the script
$grid->renderGrid('#nstklist','#nstkpager',true, $summaryrows, null, true,true);


?>

