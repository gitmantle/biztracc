<?php
session_start();
ini_set('display_errors', true);
$coyidno = $_SESSION['s_coyid'];

$usersession = $_SESSION['usersession'];

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subscriber = $row['subid'];
$user_id = $row['user_id'];

$crfile = 'ztmp'.$user_id.'_cr';

$findb = $_SESSION['s_findb'];
$cltdb = $_SESSION['s_cltdb'];

$db->query("drop table if exists ".$findb.".".$crfile);
$db->execute();

$db->query("create table ".$findb.".".$crfile." (account varchar(45),accountno int,Sub int default 0, blocked char(3) default 'No')  engine myisam");
$db->execute();

$db->query("insert into ".$findb.'.'.$crfile." select concat(".$cltdb.".members.lastname,' ',".$cltdb.".members.firstname,' ',".$cltdb.".client_company_xref.subname) as account,".$cltdb.".client_company_xref.crno as accountno,".$cltdb.".client_company_xref.crsub as sub,".$cltdb.".client_company_xref.blocked from ".$cltdb.".members left join ".$cltdb.".client_company_xref on ".$cltdb.".members.member_id = ".$cltdb.".client_company_xref.client_id where ".$cltdb.".client_company_xref.company_id = ".$coyidno." and ".$cltdb.".client_company_xref.crno != ''");
$db->execute();


// Add uid

$db->query("alter table ".$findb.".".$crfile." add `uid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY");
$db->execute();

$db->closeDB();

if(isset($_REQUEST["cr_mask"])) {
	$cr_mask = $_REQUEST['cr_mask'];
} else {
  	$cr_mask = ""; 
}


if (isset($_SESSION['s_select'])) {
	$s = $_SESSION['s_select'];
	$sel = explode('~',$s);
	$ac_mask = $sel[1];
	$drcr = $sel[0];
} else {
	$drcr = '';
}


//construct where clause 
$where = '';
if($cr_mask!='') {
	$where = " where account LIKE '".$cr_mask."%'"; 
}

if ($drcr == 'dr') {
	$heading = 'Select Creditor Ledger Account to Debit';
}
if ($drcr == 'cr') {
	$heading = 'Select Creditor Ledger Account to Credit';
}
if ($drcr == '') {
	$heading = 'Select Creditor Ledger Account';
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

$grid->SelectCommand = "select account,accountno,sub,blocked from ".$findb.".".$crfile.$where;

// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('selectcr.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
    "caption"=>$heading,
    "rowNum"=>5,
    "sortname"=>"account",
    "rowList"=>array(5,50,100,200),
	"height"=>120,
	"width"=>450
	));

// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"Uid", "width"=>20, "hidden"=>true));
$grid->setColProperty("account", array("label"=>"Account Name", "width"=>200));
$grid->setColProperty("accountno", array("label"=>"Account", "width"=>40));
$grid->setColProperty("sub", array("label"=>"Sub", "width"=>30));
$grid->setColProperty("blocked", array("label"=>"Blocked", "width"=>20, "hidden"=>true));

// on select row populate the relevant text box and hide the div
$selectac = <<<ACC
function(rowid, selected)
{
    if(rowid != null) {
	var rowdata = $("#selectcrlist").getRowData(rowid);
	var acname = rowdata.account;
	var ac = rowdata.accountno;
	var sb = rowdata.sub;
	var blk = rowdata.blocked;
	var acc = ac+'~'+' '+'~'+sb+'~'+acname+'~'+blk;
	setselect(acc,'cr');	
    }
}
ACC;
$grid->setGridEvent('onSelectRow', $selectac);


// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>false,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

$buttonoptions = array("#selectcrpager",
    array("buttonicon"=>"ui-icon-plus","caption"=>"","position"=>"first","title"=>"Add a Client", "onClickButton"=>"js: function(){ad_updtcr();}")
);
$grid->callGridMethod("#selectcrlist", "navButtonAdd", $buttonoptions); 
// Run the script
$grid->renderGrid('#selectcrlist','#selectcrpager',true, null, null, true,true);

?>

