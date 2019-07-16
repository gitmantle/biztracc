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

$drfile = 'ztmp'.$user_id.'_dr';

$findb = $_SESSION['s_findb'];
mysql_select_db($findb) or die(mysql_error());

$query = "drop table if exists ".$drfile;
$result = mysql_query($query) or die(mysql_error());

$query = "create table ".$drfile." (account varchar(45),accountno int,branch char(4) default '',branchname varchar(25) default '',sub int default 0)  engine myisam";
$calc = mysql_query($query) or die(mysql_error());

$q = "insert into ".$findb.'.'.$drfile." select glmast.account,glmast.accountno,glmast.branch,branch.branchname,glmast.sub from glmast,branch where (glmast.branch = branch.branch) and (glmast.blocked = 'N') and (glmast.accountno >= 101) and (glmast.accountno <= 200)";
$r = mysql_query($q) or die(mysql_error().' '.$q);

// Add uid
$q = "alter table ".$drfile." add `uid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY";
$r = mysql_query($q) or die(mysql_error().' '.$q);


$heading = 'Select Cost of Sales Account to Debit';

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

$grid->SelectCommand = "select uid,account,accountno,sub,branch,branchname from ".$drfile;

// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('selecttrdcos.php');
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
$grid->setColProperty("account", array("label"=>"Account Name", "width"=>150));
$grid->setColProperty("accountno", array("label"=>"Account", "width"=>40));
$grid->setColProperty("sub", array("label"=>"Sub", "width"=>30));
$grid->setColProperty("branch", array("label"=>"Branch", "width"=>30));
$grid->setColProperty("branchname", array("label"=>" ", "width"=>80));


// on select row populate the relevant text box and hide the div
$selectac = <<<ACC
function(rowid, selected)
{
    if(rowid != null) {
	var rowdata = $("#selectcoslist").getRowData(rowid);
	var acname = rowdata.account;
	var ac = rowdata.accountno;
	var br = rowdata.branch;
	var sb = rowdata.sub;
	var acc = ac+'~'+br+'~'+sb+'~'+acname;
	setcosselect(acc);	
    }
}
ACC;
$grid->setGridEvent('onSelectRow', $selectac);


// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>false,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

// Run the script
$grid->renderGrid('#selectcoslist','#selectcospager',true, null, null, true,true);

?>

