<?php
session_start();
ini_set('display_errors', true);
require("../db.php");
$coyidno = $_SESSION['s_coyid'];

$usersession = $_SESSION['usersession'];

$admindb = $_SESSION['s_admindb'];
require("../db.php");
mysql_select_db($admindb) or die(mysql_error());
$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);

$crfile = 'ztmp'.$user_id.'_cr2';

$findb = $_SESSION['s_findb'];
mysql_select_db($findb) or die(mysql_error());

$query = "drop table if exists ".$crfile;
$result = mysql_query($query) or die(mysql_error());

$query = "create table ".$crfile." (account varchar(45),accountno int,Sub int default 0)  engine myisam";
$calc = mysql_query($query) or die(mysql_error());

$cltdb = $_SESSION['s_cltdb'];
mysql_select_db($cltdb) or die(mysql_error());

$q = "insert into ".$findb.'.'.$crfile." select concat(members.lastname,' ',members.firstname) as account,client_company_xref.crno as accountno,client_company_xref.crsub as sub from members left join client_company_xref on members.member_id = client_company_xref.client_id where client_company_xref.company_id = ".$coyidno." and client_company_xref.crno != 0 and client_company_xref.crsub = 0 and client_company_xref.blocked = 'No' ";
$r = mysql_query($q) or die(mysql_error().' '.$q);

$findb = $_SESSION['s_findb'];
mysql_select_db($findb) or die(mysql_error());

// Add uid
$q = "alter table ".$crfile." add `uid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY";
$r = mysql_query($q) or die(mysql_error().' '.$q);


if(isset($_REQUEST["cr_mask"])) {
	$cr_mask = $_REQUEST['cr_mask'];
} else {
  	$cr_mask = ""; 
}

$s = $_SESSION['s_select'];
$sel = explode('~',$s);
$ac_mask = $sel[1];
$drcr = $sel[0];


//construct where clause 
$where = '';
if($cr_mask!='') {
	$where = " where account like '".$cr_mask."%'"; 
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

$grid->SelectCommand = "select account,accountno,sub from ".$crfile.$where;

// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('selectcr2.php');
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
$grid->setColProperty("sub", array("label"=>"Sub", "width"=>30, "hidden"=>true));


// on select row populate the relevant text box and hide the div
$selectac = <<<ACC
function(rowid, selected)
{
    if(rowid != null) {
	var rowdata = $("#selectcrlist2").getRowData(rowid);
	var acname = rowdata.account;
	var ac = rowdata.accountno;
	var sb = rowdata.sub;
	var acc = ac+'~'+' '+'~'+sb+'~'+acname;
	setselectm2(acc,'cr');	
    }
}
ACC;
$grid->setGridEvent('onSelectRow', $selectac);


// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>false,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

// Run the script
$grid->renderGrid('#selectcrlist2','#selectcrpager2',true, null, null, true,true);

?>

