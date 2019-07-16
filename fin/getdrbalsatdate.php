<?php
session_start();
$coyid = $_SESSION['s_coyid'];

$usersession = $_SESSION['usersession'];

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];

$dt = $_SESSION['s_dt'];
$fromac = $_SESSION['s_fno'];
$toac = $_SESSION['s_tno'];

$drbfile = 'ztmp'.$user_id.'_drbals';

$findb = $_SESSION['s_findb'];
$cltdb = $_SESSION['s_cltdb'];

$db->query("drop table if exists ".$findb.".".$drbfile);
$db->execute();

$db->query("create table ".$findb.".".$drbfile." (uid integer(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, clname varchar(50) default '', balance decimal(16,2) default 0, acno int(11) default 0, sbno int(11) default 0 ) engine myisam"); 
$db->execute();

$db->query('select member,drno,drsub from '.$cltdb.'.client_company_xref where drno >= '.$fromac.' and drno <= '.$toac.' and company_id = '.$coyid);
$rows = $db->resultset();
foreach ($rows as $row) {
	extract($row);
	$m = $member;
	$a = $drno;
	$s = $drsub;
	
	$db->query('insert into '.$findb.'.'.$drbfile.' (clname,acno,sbno) values ("'.$m.'",'.$a.','.$s.')');
	$db->execute();
}

$db->query('select uid,acno,sbno from '.$findb.'.'.$drbfile);
$rows = $db->resultset();
foreach ($rows as $row) {
	extract($row);
	$id = $uid;
	$ac = $acno;
	$sb = $sbno;
	
	$db->query('select sum(debit-credit) as bal from '.$findb.'.trmain where accountno = '.$ac.' and sub = '.$sb.' and ddate <= "'.$dt.'"');
	$row = $db->single();
	extract($row);
	if ($bal == NULL) {
		$b = 0;
	} else {
		$b = $bal;
	}
	
	$db->query('update '.$findb.'.'.$drbfile.' set balance = '.$b.' where uid = '.$id);
	$db->execute();
	
}

$db->closeDB();

include 'jq-config.php';

// include the jqGrid Class
require_once "../includes/jquery/php/jqGrid.php";
// include the PDO driver class
require_once "../includes/jquery/php/jqGridPdo.php";
// Connection to the server
$conn = new PDO(DB_DSN,DB_USER,DB_PASSWORD);

// Create the jqGrid instance
$grid = new jqGridRender($conn);


// the actual query for the grid data
$grid->SelectCommand = "select uid,clname,balance from ".$findb.".".$drbfile;

// set the ouput format to json
$grid->dataType = 'json';

// Let the grid create the model
$grid->setColModel(null);

// Set the url from where we obtain the data
$grid->setUrl('getdrbalsatdate.php');


// Set some grid options
$grid->setGridOptions(array(
    "caption"=>"Debtors Balances at ".$dt,
    "rowNum"=>12,
    "sortname"=>"clname",
    "rowList"=>array(12,50,100,200),
	"height"=>290,
	"width"=>700
    ));

// Enable footerdata an tell the grid to obtain it from the request
$grid->setGridOptions(array("footerrow"=>true,"userDataOnFooter"=>true));


// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"ID", "width"=>20, "hidden"=>true));
$grid->setColProperty("clname", array("label"=>"Debtor", "width"=>200));
$grid->setColProperty("balance", array("label"=>"Balance", "width"=>50, "align"=>"right","formatter"=>"number"));

// Set which parameter to be sumarized
$summaryrows = array("balance"=>array("balance"=>"SUM"));

// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>true,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));


// Run the script
$grid->renderGrid('#drbalslist','#drbalspager',true, $summaryrows, null,true,true,true);
$conn = null;

?>




