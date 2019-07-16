<?php
session_start();
ini_set('display_errors', true);

$findb = $_SESSION['s_findb'];

if(isset($_REQUEST["gl_mask"])) {
	$gl_mask = $_REQUEST['gl_mask'];
} else {
  	$gl_mask = ""; 
}

$beg = 1;
$end = 100;
//$expbeg = 201;
//$expend = 700;

//construct where clause 
$wh = '';
if($gl_mask!='') {
	$wh = " AND account LIKE '".$gl_mask."%'"; 
}
$where = " WHERE accountno = 1000 or (ctrlacc = 'N' and blocked = 'N' and accountno >= ".$beg." and accountno <= ".$end.") ".$wh;  
//$where = " WHERE accountno = 1000 or (ctrlacc = 'N' and blocked = 'N' and accountno >= ".$beg." and accountno <= ".$end.") or (ctrlacc = 'N' and blocked = 'N' and accountno >= ".$expbeg." and accountno <= ".$expend.")".$wh;  

$heading = 'Select Income Account';


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

$grid->SelectCommand = "SELECT distinct glmast.account,glmast.accountno,glmast.sub from ".$findb.".glmast".$where;

// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('selectinc.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
    "caption"=>$heading,
    "rowNum"=>5,
    "sortname"=>"accountno,sub",
    "rowList"=>array(5,50,100,200),
	"height"=>120,
	"width"=>450
	));

// Change some property of the field(s)
$grid->setColProperty("account", array("label"=>"Account Name", "width"=>100));
$grid->setColProperty("accountno", array("label"=>"Account", "width"=>40));
$grid->setColProperty("sub", array("label"=>"Sub", "width"=>30));


// on select row populate the relevant text box and hide the div
$selectac = <<<ACC
function(rowid, selected)
{
    if(rowid != null) {
	var rowdata = $("#selectinclist").getRowData(rowid);
	var acname = rowdata.account;
	var ac = rowdata.accountno;
	var sb = rowdata.sub;
	var acc = ac+'~'+sb+'~'+acname;
	setinc(acc);	
    }
}
ACC;
$grid->setGridEvent('onSelectRow', $selectac);


// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>false,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

// Run the script
$grid->renderGrid('#selectinclist','#selectincpager',true, null, null, true,true);

?>

