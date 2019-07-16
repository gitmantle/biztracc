<?php
session_start();
ini_set('display_errors', true);
$coyidno = $_SESSION['s_coyid'];
$source = $_SESSION['s_source'];

$usersession = $_SESSION['usersession'];

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];

$crfile = 'ztmp'.$user_id.'_cr';

$findb = $_SESSION['s_findb'];
$cltdb = $_SESSION['s_cltdb'];

$db->query("drop table if exists ".$findb.".".$crfile);
$db->execute();

$db->query("create table ".$findb.".".$crfile." (account varchar(45),accountno int,Sub int default 0,client_id int default 0)  engine myisam");
$db->execute();

$db->query("insert into ".$findb.'.'.$crfile." select concat(members.lastname,' ',members.firstname,' ',client_company_xref.subname) as account,client_company_xref.crno as accountno,client_company_xref.crsub as sub,client_company_xref.client_id from ".$cltdb.".members left join ".$cltdb.".client_company_xref on members.member_id = client_company_xref.client_id where client_company_xref.company_id = ".$coyidno." and client_company_xref.crno != '' and client_company_xref.blocked = 'No' ");
$db->execute();

// Add uid
$db->query("alter table ".$findb.'.'.$crfile." add `uid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY");
$db->execute();

$db->closeDB();

if(isset($_REQUEST["cr_mask"])) {
	$cr_mask = $_REQUEST['cr_mask'];
} else {
  	$cr_mask = ""; 
}

//construct where clause 
$where = '';
if($cr_mask!='') {
	$where = " where account LIKE '".$cr_mask."%'"; 
}

	$heading = 'Select Creditor Ledger Account';


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

$grid->SelectCommand = "select account,accountno,sub,client_id from ".$findb.".".$crfile.$where;

// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('selectpayunallocate.php');
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


// on select row populate the relevant text box and hide the div
$selectac = <<<ACC
function(rowid, selected)
{
    if(rowid != null) {
	var rowdata = $("#selectpayunlist").getRowData(rowid);
	var acname = rowdata.account;
	var ac = rowdata.accountno;
	var sb = rowdata.sub;
	var clientid = rowdata.client_id;
	var acc = acname+'~'+ac+'~'+sb;
	setpayunallocation(acc,'cr');	
    }
}
ACC;
$grid->setGridEvent('onSelectRow', $selectac);


// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>false,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

// Run the script
$grid->renderGrid('#selectpayunlist','#selectpayunpager',true, null, null, true,true);

?>

