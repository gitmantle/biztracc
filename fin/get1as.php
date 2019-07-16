<?php
session_start();
$usersession = $_SESSION['usersession'];

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];

$findb = $_SESSION['s_findb'];
$cltdb = $_SESSION['s_cltdb'];

$asfile = 'ztmp'.$user_id.'_1as';

$db->query("drop table if exists ".$findb.".".$asfile);
$db->execute();

$db->query("create table ".$findb.".".$asfile." ( ddate date, accountno int(11),branch char(4),sub int(11),accno int(11),br char(4),subbr int(11),otherleg varchar(50),debit decimal(16,2),credit decimal(16,2),runbal decimal(16,2), reference varchar(15),descript1 varchar(50)) engine myisam"); 
$db->execute();

$vac = $_SESSION['s_viewac'];
$va = explode('~',$vac);
$ac = $va[0];
$br = trim($va[2]);
$sb = $va[1];

$fromdate = $_SESSION['s_fromdate'];
$todate = $_SESSION['s_todate'];

$brcons = $_SESSION['s_brcons'];
$subcons = $_SESSION['s_subcons'];
$ob = $_SESSION['s_sob'];

// opening balance entry
$opbal = 0;
if ($ob == 'Y') {
	$db->query("select sum(debit - credit) as bal from ".$findb.".trmain where ddate < '".$fromdate."' and accountno = ".$ac." and branch = '".$br."' and sub = ".$sb);	
	$row = $db->single();
	extract($row);
	$opbal = $bal;
	if (is_null($opbal)) {
		$opbal = 0;
	}
	$db->query("insert into ".$findb.".".$asfile." (ddate,accountno,branch,sub,descript1,runbal) values ('".$fromdate."',".$ac.",'".$br."',".$sb.",'Opening Balance',".$opbal.")");
	$db->execute();
}


$where = " where 1";

// account < 1000, consolidated branches, consolidated sub accounts
if ($brcons == 'y' && $subcons == 'y') {
	$where .= ' and accountno = '.$ac;
}
// account < 1000, consolidated branches, detailed sub accounts
if ($brcons == 'y' && $subcons == 'n') {
	$where .= ' and accountno = '.$ac.' and sub = '.$sb;
}
// account < 1000, detailed branches, consolidated sub accounts
if ($brcons == 'n' && $subcons == 'y') {
	$where .= " and accountno = ".$ac." and branch = '".$br."'";
}
// account < 1000, detailed branches, detailed sub accounts
if ($brcons == 'n' && $subcons == 'n') {
	$where .= " and accountno = ".$ac." and branch = '".$br."' and sub = ".$sb;
}

$where .= " and ddate >= '".$fromdate."' and ddate <= '".$todate."'";
$where1 = $where.' order by ddate';

$db->query("select asset from ".$findb.".fixassets where accountno = ".$ac." and branch = '".$br."' and sub = ".$sb);
$row = $db->single();
extract($row);

$heading = $_SESSION['s_coyname'].' - '.$asset." - between '".$fromdate."' and '".$todate."'";

$db->query("insert into ".$findb.".".$asfile." select ddate,accountno,branch,sub,accno,br,subbr,' ',debit,credit,0 as runbal,reference,descript1 from ".$findb.".trmain ".$where1);
$db->execute();

// add uid and address fields
$db->query("alter table ".$findb.".".$asfile." add `uid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY");
$db->execute();

$db->query("select uid,debit,credit,accno,br,subbr,descript1 from ".$findb.".".$asfile);
$rows = $db->resultset();
$rbal = $opbal;
foreach ($rows as $row) {
	extract($row);
	$id = $uid;
	$a = $accno;
	$b = $br;
	$s = $subbr;
	$d = $descript1;
	if ($d <> 'Opening Balance') {
		$rbal = $rbal + $debit - $credit;
		$db->query("update ".$findb.".".$asfile. " set runbal = ".$rbal." where uid = ".$id);
		$db->execute();
		
		if ($a < 1000) {	
			$db->query("select account from ".$findb.".glmast where accountno = ".$a." and branch = '".$b."' and sub = ".$s);
			$row = $db->single();
		} elseif ($a >= 10000000 && $a < 20000000) {
			$db->query("select asset as account from ".$findb.".fixassets where accountno = ".$a." and branch = '".$b."' and sub = ".$s);
			$row = $db->single();
		} elseif ($a >= 20000000 && $a < 30000000) {
			$db->query("select concat(members.firstname,' ',members.lastname) as account from ".$cltdb.".members,".$cltdb.".client_company_xref where client_company_xref.crno = ".$a." and client_company_xref.crsub = ".$s." and members.member_id = client_company_xref.client_id");
			$row = $db->single();
		} elseif ($a >= 30000000) {
			$db->query("select concat(members.firstname,' ',members.lastname) as account from ".$cltdb.".members,".$cltdb.".client_company_xref where client_company_xref.drno = ".$a." and client_company_xref.drsub = ".$s." and members.member_id = client_company_xref.client_id");
			$row = $db->single();
		}
		if (empty($row)) {
			$acct = "Unknown";
		} else {
			extract($row);
			$acct = $account;
			
			if ($a < 2000000) {
				$db->query("select branchname from ".$findb.".branch where branch = '".$b."'");
				$row = $db->single();
				extract($row);
				
				$acct = $acct.' '.$branchname;
			}
			
		}
		
		$db->query("update ".$findb.".".$asfile. " set otherleg = '".$acct."' where uid = ".$id);
		$db->execute();
	}
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
$grid->SelectCommand = "select ddate,otherleg,debit,credit,runbal,reference,descript1 from ".$findb.".".$asfile.$where;

// set the ouput format to json
$grid->dataType = 'json';

// Let the grid create the model
$grid->setColModel(null);

// Set the url from where we obtain the data
$grid->setUrl('get1as.php');


// Set some grid options
$grid->setGridOptions(array(
    "caption"=>$heading,
    "rowNum"=>20,
    "sortname"=>"uid",
    "rowList"=>array(20,100,200),
	"height"=>465,
	"width"=>900
    ));

// Enable footerdata an tell the grid to obtain it from the request
$grid->setGridOptions(array("footerrow"=>true,"userDataOnFooter"=>true));



// Change some property of the field(s)
$grid->setColProperty("ddate", array("label"=>"Date", "width"=>35));
$grid->setColProperty("otherleg", array("label"=>"Corresponding Entry", "width"=>100));
$grid->setColProperty("debit", array("label"=>"Debit", "width"=>40, "align"=>"right", "formatter"=>"number","formatoptions"=>array("thousandsSeparator"=>",","decimalPlaces"=>2)));
$grid->setColProperty("credit", array("label"=>"Credit", "width"=>40, "align"=>"right", "formatter"=>"number","formatoptions"=>array("thousandsSeparator"=>",","decimalPlaces"=>2)));
$grid->setColProperty("runbal", array("label"=>"Balance", "width"=>40, "align"=>"right", "formatter"=>"number","formatoptions"=>array("thousandsSeparator"=>",","decimalPlaces"=>2)));
$grid->setColProperty("reference", array("label"=>"Ref.", "width"=>25));
$grid->setColProperty("descript1", array("label"=>"Description", "width"=>100));


// At end call footerData to put total  label
$grid->callGridMethod('#as1list', 'footerData', array("set",array("acc"=>"Balances:")));
// Set which parameter to be sumarized
$summaryrows = array("debit"=>array("debit"=>"SUM"),"credit"=>array("credit"=>"SUM"));


// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>true,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

$buttonoptions1 = array("#as1pager",
    array("buttonicon"=>"ui-icon-print","caption"=>"","position"=>"last","title"=>"Output to PDF.", "onClickButton"=>"js: function(){as12pdf();}")
);
$grid->callGridMethod("#as1list", "navButtonAdd", $buttonoptions1); 

$buttonoptions2 = array("#as1pager",
    array("buttonicon"=>"ui-icon-calculator","caption"=>"","position"=>"last","title"=>"Calculators", "onClickButton"=>"js: function(){showCalculators();}")
);
$grid->callGridMethod("#as1list", "navButtonAdd", $buttonoptions2); 

// Run the script
$grid->renderGrid('#as1list','#as1pager',true, $summaryrows, null,true,true,true);
$conn = null;

?>




