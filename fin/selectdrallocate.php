<?php
session_start();
ini_set('display_errors', true);
$coyidno = $_SESSION['s_coyid'];
if (isset($_SESSION['s_source'])) {
	$source = $_SESSION['s_source'];
} else {
	$source = '';
}

$usersession = $_SESSION['usersession'];

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];

$drfile = 'ztmp'.$user_id.'_dr';

$findb = $_SESSION['s_findb'];
$cltdb = $_SESSION['s_cltdb'];

$db->query("drop table if exists ".$findb.".".$drfile);
$db->execute();

$db->query("create table ".$findb.".".$drfile." (account varchar(45),priceband int,accountno int,Sub int default 0,client_id int default 0,blocked char(3) default 'No')  engine myisam");
$db->execute();

$db->query("insert into ".$findb.".".$drfile." select trim(concat(".$cltdb.".members.firstname,' ',".$cltdb.".members.lastname,' ',".$cltdb.".client_company_xref.subname)) as account,".$cltdb.".client_company_xref.priceband,".$cltdb.".client_company_xref.drno as accountno,".$cltdb.".client_company_xref.drsub as sub,".$cltdb.".client_company_xref.client_id,".$cltdb.".client_company_xref.blocked from ".$cltdb.".members left join ".$cltdb.".client_company_xref on ".$cltdb.".members.member_id = ".$cltdb.".client_company_xref.client_id where ".$cltdb.".client_company_xref.company_id = ".$coyidno." and ".$cltdb.".client_company_xref.drno != '' ");
$db->execute();


// Add uid
$db->query("alter table ".$findb.'.'.$drfile." add `uid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY");
$db->execute();

if(isset($_REQUEST["dr_mask"])) {
	$dr_mask = $_REQUEST['dr_mask'];
} else {
  	$dr_mask = ""; 
}

//construct where clause 
$where = '';
if($dr_mask!='') {
	$where = " where account LIKE '".$dr_mask."%'"; 
}

$heading = 'Select Debtor Ledger Account to Credit';
	
$db->closeDB();


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

$grid->SelectCommand = "select account,accountno,sub,client_id,priceband,blocked from ".$drfile.$where;

// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('selectdrallocate.php');
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
$grid->setColProperty("client_id", array("label"=>"clientid", "width"=>20, "hidden"=>true));
$grid->setColProperty("priceband", array("label"=>"priceband", "width"=>20, "hidden"=>true));
$grid->setColProperty("blocked", array("label"=>"Blocked", "width"=>20, "hidden"=>true));


// on select row populate the relevant text box and hide the div
$selectac = <<<ACC
function(rowid, selected)
{
    if(rowid != null) {
	var rowdata = $("#selectdrlist").getRowData(rowid);
	var acname = rowdata.account;
	var ac = rowdata.accountno;
	var sb = rowdata.sub;
	var clientid = rowdata.client_id;
	var priceband = rowdata.priceband;
	var blk = rowdata.blocked;
	var acc = acname+'~'+ac+'~'+sb+'~'+blk;
	setdrallocation(acc,'dr');	
    }
}
ACC;
$grid->setGridEvent('onSelectRow', $selectac);


// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>false,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

// Run the script
$grid->renderGrid('#selectdrlist','#selectdrpager',true, null, null, true,true);

?>

