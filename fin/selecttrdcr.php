<?php
session_start();
ini_set('display_errors', true);
$coyidno = $_SESSION['s_coyid'];
$findb = $_SESSION['s_findb'];

$usersession = $_SESSION['usersession'];

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subscriber = $row['subid'];
$user_id = $row['user_id'];
$crfile = 'ztmp'.$user_id.'_crlist';

if(isset($_REQUEST["cr_mask"])) {
	$cr_mask = $_REQUEST['cr_mask'];
} else {
  	$cr_mask = ""; 
}

$db->closeDB();

//construct where clause 
$where = '';
if($cr_mask!='') {
	$where = " where upper(account) LIKE '%".$cr_mask."%'"; 
}

$heading = 'Select Creditor Ledger Account to Credit';


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

$grid->SelectCommand = "select account,accountno,sub,client_id,priceband,preferred,blocked from ".$findb.".".$crfile.$where;

// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('selecttrdcr.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
    "caption"=>$heading,
    "rowNum"=>5,
    "sortname"=>"account",
    "rowList"=>array(5,50,100,200),
	"height"=>120,
	"width"=>448
	));

// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"Uid", "width"=>20, "hidden"=>true));
$grid->setColProperty("account", array("label"=>"Account Name", "width"=>200));
$grid->setColProperty("accountno", array("label"=>"Account", "width"=>40));
$grid->setColProperty("sub", array("label"=>"Sub", "width"=>30));
$grid->setColProperty("client_id", array("label"=>"clientid", "width"=>20, "hidden"=>true));
$grid->setColProperty("priceband", array("label"=>"priceband", "width"=>20, "hidden"=>true));
$grid->setColProperty("preferred", array("label"=>"preferred", "width"=>45, "hidden"=>true));
$grid->setColProperty("blocked", array("label"=>"Blocked", "width"=>20, "hidden"=>true));


// on select row populate the relevant text box and hide the div
$selectac = <<<ACC
function(rowid, selected)
{
    if(rowid != null) {
	var rowdata = $("#selecttrdcrlist").getRowData(rowid);
	var acname = rowdata.account;
	var pname = rowdata.preferred;
	var ac = rowdata.accountno;
	var sb = rowdata.sub;
	var clientid = rowdata.client_id;
	var priceband = rowdata.priceband;
	var blk = rowdata.blocked
	var acc = ac+'~'+' '+'~'+sb+'~'+acname+'~'+clientid+'~'+priceband+'~'+pname+'~'+blk;
	settrdselect(acc,'cr');	
    }
}
ACC;
$grid->setGridEvent('onSelectRow', $selectac);


// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>false,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

$buttonoptions = array("#selecttrdcrpager",
    array("buttonicon"=>"ui-icon-plus","caption"=>"","position"=>"first","title"=>"Add a Client", "onClickButton"=>"js: function(){ad_updtcr();}")
);
$grid->callGridMethod("#selecttrdcrlist", "navButtonAdd", $buttonoptions); 

// Run the script
$grid->renderGrid('#selecttrdcrlist','#selecttrdcrpager',true, null, null, true,true);


?>

