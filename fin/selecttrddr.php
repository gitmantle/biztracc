<?php
session_start();
ini_set('display_errors', true);


$usersession = $_SESSION['usersession'];

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subscriber = $row['subid'];
$user_id = $row['user_id'];

$drfile = 'ztmp'.$user_id.'_drlist';

$findb = $_SESSION['s_findb'];
$cltdb = $_SESSION['s_cltdb'];



if(isset($_REQUEST["dr_mask"])) {
	$dr_mask = $_REQUEST['dr_mask'];
} else {
  	$dr_mask = ""; 
}


//construct where clause 
$where = '';
if($dr_mask!='') {
	$where = " where account LIKE '%".$dr_mask."%'"; 
}

$heading = 'Select Debtor Ledger Account to Debit';


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

// enable debugging
//$grid->debug = true;


$grid->SelectCommand = "select uid,account,accountno,sub,client_id,priceband,preferred,blocked from ".$findb.".".$drfile.$where;

// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('selecttrddr.php');
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
	var rowdata = $("#selecttrddrlist").getRowData(rowid);
	var acname = rowdata.account;
	var pname = rowdata.preferred;
	var ac = rowdata.accountno;
	var sb = rowdata.sub;
	var clientid = rowdata.client_id;
	var priceband = rowdata.priceband;
	var blk = rowdata.blocked
	var acc = ac+'~'+' '+'~'+sb+'~'+acname+'~'+clientid+'~'+priceband+'~'+pname+'~'+blk;
	settrdselect(acc,'dr');	
    }
}
ACC;
$grid->setGridEvent('onSelectRow', $selectac);


// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>false,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

$buttonoptions = array("#selecttrddrpager",
    array("buttonicon"=>"ui-icon-plus","caption"=>"","position"=>"first","title"=>"Add a Client", "onClickButton"=>"js: function(){ad_updtdr();}")
);
$grid->callGridMethod("#selecttrddrlist", "navButtonAdd", $buttonoptions); 

// Run the script
$grid->renderGrid('#selecttrddrlist','#selecttrddrpager',true, null, null, true,true);

?>

