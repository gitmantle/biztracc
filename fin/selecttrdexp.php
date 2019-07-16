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

$drfile = 'ztmp'.$user_id.'_dr';

$findb = $_SESSION['s_findb'];

$db->query("drop table if exists ".$findb.".".$drfile);
$db->execute();

$db->query("create table ".$findb.".".$drfile." (account varchar(45),accountno int,branch char(4) default '',branchname varchar(25) default '',sub int default 0)  engine myisam");
$db->execute();

$db->query("insert into ".$findb.'.'.$drfile." select glmast.account,glmast.accountno,glmast.branch,branch.branchname,glmast.sub from ".$findb.".glmast,".$findb.".branch where (glmast.branch = branch.branch) and (glmast.blocked = 'N') and (glmast.accountno >= 200) and (glmast.accountno <= 701)");
$db->execute();

// Add uid
$db->query("alter table ".$findb.".".$drfile." add `uid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY");
$db->execute();

$db->closeDB();

$heading = 'Select Expense Account to Debit';

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

$grid->SelectCommand = "select uid,account,accountno,sub,branch,branchname from ".$findb.".".$drfile;

// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('selecttrdexp.php');
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

