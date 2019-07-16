<?php
session_start();
ini_set('display_errors', true);

$findb = $_SESSION['s_findb'];

if(isset($_REQUEST["gl_mask"])) {
	$gl_mask = $_REQUEST['gl_mask'];
} else {
  	$gl_mask = ""; 
}

if (isset($_SESSION['s_select'])) {
	$s = $_SESSION['s_select'];
	$sel = explode('~',$s);
	$ac_mask = $sel[1];
	$drcr = $sel[0];
} else {
	$ac_mask = '';
	$drcr = '';
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

//construct where clause 
$wh = '';
if($gl_mask!='') {
	$wh = " AND ".$findb.".glmast.account LIKE '".$gl_mask."%'"; 
}
$where = " WHERE ".$findb.".glmast.ctrlacc = 'N' and ".$findb.".glmast.blocked = 'N' and ".$findb.".glmast.accountno >= ".$beg." and ".$findb.".glmast.accountno <= ".$end.$wh;  

if ($drcr == 'dr') {
	$heading = 'Select General Ledger Account to Debit';
}
if ($drcr == 'cr') {
	$heading = 'Select General Ledger Account to Credit';
}
if ($drcr == '') {
	$heading = 'Select General Ledger Account';
}


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

$grid->SelectCommand = "SELECT ".$findb.".glmast.uid,".$findb.".glmast.account,".$findb.".glmast.accountno,".$findb.".glmast.branch,".$findb.".branch.branchname,".$findb.".glmast.sub from ".$findb.".glmast,".$findb.".branch".$where." and ".$findb.".glmast.branch = ".$findb.".branch.branch";

// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('selectgl.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
    "caption"=>$heading,
    "rowNum"=>5,
    "sortname"=>"accountno,branch,sub",
    "rowList"=>array(5,50,100,200),
	"height"=>120,
	"width"=>450
	));

// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"ID", "width"=>20, "hidden"=>true));
$grid->setColProperty("account", array("label"=>"Account Name", "width"=>100));
$grid->setColProperty("accountno", array("label"=>"Account", "width"=>35));
$grid->setColProperty("branch", array("label"=>"Br", "width"=>35, "hidden"=>true));
$grid->setColProperty("branchname", array("label"=>"Branch", "width"=>50));
$grid->setColProperty("sub", array("label"=>"Sub", "width"=>15));


// on select row populate the relevant text box and hide the div
$selectac = <<<ACC
function(rowid, selected)
{
    if(rowid != null) {
	var rowdata = $("#selectgllist").getRowData(rowid);
	var acname = rowdata.account;
	var ac = rowdata.accountno;
	var br = rowdata.branch;
	var sb = rowdata.sub;
	var acc = ac+'~'+br+'~'+sb+'~'+acname;
	setselect(acc,'gl');	
    }
}
ACC;
$grid->setGridEvent('onSelectRow', $selectac);


// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>false,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

$buttonoptions = array("#selectglpager",
    array("buttonicon"=>"ui-icon-plus","caption"=>"","position"=>"first","title"=>"Add an Account", "onClickButton"=>"js: function(){ad_updtgl();}")
);
$grid->callGridMethod("#selectgllist", "navButtonAdd", $buttonoptions); 

// Run the script
$grid->renderGrid('#selectgllist','#selectglpager',true, null, null, true,true);

?>

