<?php
session_start();

if(isset($_REQUEST["br"])) {
	$br = $_REQUEST["br"];
	if ($br != 'all') {
		$br_mask = " and glmast.branch = '".$br."'";
	}	
} else {
	$br_mask = "";	
}

if(isset($_REQUEST["ac_mask"])) {
	$ac_mask = $_REQUEST['ac_mask'];
} else {
  	$ac_mask = ""; 
}


//construct where clause 
switch ($ac_mask) {
	case 'inc':
		$beg = 1;
		$end = 80;
		$hed = "Income";
		break;
	case 'sun';
		$beg = 81;
		$end = 100;
		$hed = "Sundry Income";
		break;
	case 'cos';
		$beg = 101;
		$end = 200;
		$hed = "Cost of Sales";
		break;
	case 'exp';
		$beg = 201;
		$end = 700;
		$hed = "Expenses";
		break;
	case 'inv';
		$beg = 701;
		$end = 750;
		$hed = "Investments";
		break;
	case 'ban';
		$beg = 751;
		$end = 800;
		$hed = "Bank";
		break;
	case 'oas';
		$beg = 801;
		$end = 850;
		$hed = "Assets";
		break;
	case 'lib';
		$beg = 851;
		$end = 900;
		$hed = "Liabilities";
		break;
	case 'equ';
		$beg = 901;
		$end = 999;
		$hed = "Income";
		break;
	case 'all':
		$beg = 1;
		$end = 999;
		$hed = "All";
		break;		
	default:
		$beg = 1;
		$end = 999;
		$hed = "All";
		break;
}

$where = "WHERE branch.branch = glmast.branch AND accountno >= ".$beg." and accountno <= ".$end.$br_mask; 

require_once '../includes/jquery/jq-config_fin.php';
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

$grid->SelectCommand = "SELECT glmast.uid,glmast.branch,branch.branchname,glmast.accountno,glmast.sub,glmast.account from glmast,branch ".$where;


// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('getgl.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
    "caption"=>"General Ledger Accounts ",
    "rowNum"=>14,
    "sortname"=>"accountno",
    "rowList"=>array(14,30,50),
	"height"=>310,
	"width"=>680
    ));

$grid->addCol(array("name"=>"act"),"first");

// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"ID", "width"=>20, "hidden"=>true));
$grid->setColProperty("branch", array("label"=>"Branch", "width"=>35));
$grid->setColProperty("branchname", array("label"=>"Branch Name", "width"=>100));
$grid->setColProperty("accountno", array("label"=>"Account", "width"=>40));
$grid->setColProperty("sub", array("label"=>"Sub", "width"=>30));
$grid->setColProperty("account", array("label"=>"Account Name", "width"=>200));
$grid->setColProperty("act", array("label"=>"Actions", "width"=>40));


$ldevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#gl_list").getDataIDs(); 
	for(var i=0;i<ids.length;i++){ 
		var cl = ids[i]; 
		be = '<img src="../images/edit.png" title="Edit Account" onclick="javascript:editgl('+cl+')" >'; 
		se = '<img src="../images/delete.png" title="Delete Account" onclick="javascript:delgl('+cl+')" />'; 
		jQuery("#gl_list").setRowData(ids[i],{act:be+'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+se}); 
	} 
}
LOADCOMPLETE;
$grid->setGridEvent("loadComplete",$ldevent);



$grid->gSQLMaxRows = 4000;

// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>false,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));
// add a custom button via the build in callGridMethod
// note the js: before the function
$buttonoptions = array("#gl_pager2",
    array("buttonicon"=>"ui-icon-plus","caption"=>"","position"=>"first","title"=>"Add an account", "onClickButton"=>"js: function(){addgl();}")
);
$grid->callGridMethod("#gl_list", "navButtonAdd", $buttonoptions); 

// Run the script
$grid->renderGrid('#gl_list','#gl_pager2',true, null, null, true,true);


?>

