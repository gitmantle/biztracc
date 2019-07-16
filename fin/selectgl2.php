<?php
session_start();
ini_set('display_errors', true);
require("../db.php");

$moduledb = $_SESSION['s_findb'];
mysql_select_db($moduledb) or die(mysql_error());

if(isset($_REQUEST["gl_mask"])) {
	$gl_mask = $_REQUEST['gl_mask'];
} else {
  	$gl_mask = ""; 
}

$s = $_SESSION['s_select'];
$sel = explode('~',$s);
$ac_mask = $sel[1];
$drcr = $sel[0];

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
	$wh = " AND account LIKE '".$gl_mask."%'"; 
}
$where = " WHERE ctrlacc = 'N' and blocked = 'N' and accountno >= ".$beg." and accountno <= ".$end.$wh;  

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

$grid->SelectCommand = "SELECT glmast.uid,glmast.account,glmast.accountno,glmast.branch,branch.branchname,glmast.sub from glmast,branch".$where." and glmast.branch = branch.branch";

// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('selectgl2.php');
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
	var rowdata = $("#selectgllist2").getRowData(rowid);
	var acname = rowdata.account;
	var ac = rowdata.accountno;
	var br = rowdata.branch;
	var sb = rowdata.sub;
	var acc = ac+'~'+br+'~'+sb+'~'+acname;
	setselect2(acc,'gl');	
    }
}
ACC;
$grid->setGridEvent('onSelectRow', $selectac);


// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>false,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

// Run the script
$grid->renderGrid('#selectgllist2','#selectglpager2',true, null, null, true,true);

?>

