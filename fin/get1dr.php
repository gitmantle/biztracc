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
$coyid = $_SESSION['s_coyid'];

$drfile = 'ztmp'.$user_id.'_1dr';

$db->query("drop table if exists ".$findb.".".$drfile);
$db->execute();

$db->query("create table ".$findb.".".$drfile." (ddate date, accountno int(11),branch char(4),sub int(11),accno int(11),br char(4),subbr int(11),otherleg varchar(50),debit decimal(16,2) default 0,credit decimal(16,2) default 0,runbal decimal(16,2) default 0, reference varchar(15), your_ref varchar(30), xref varchar(15),descript1 varchar(50)) engine myisam"); 
$db->execute();

$vac = $_SESSION['s_viewac'];
$va = explode('~',$vac);
$ac = $va[0];
$br = trim($va[1]);
$sb = $va[2];

$fromdate = $_SESSION['s_fromdate'];
$todate = $_SESSION['s_todate'];
$ob = $_SESSION['s_sob'];

// opening balance entry
$opbal = 0;
if ($ob == 'Y') {
	$db->query("select sum(debit - credit) as bal from ".$findb.".trmain where ddate < '".$fromdate."' and accountno = ".$ac." and sub = ".$sb);	
	$row = $db->single();
	extract($row);
	$opbal = $bal;
	if (is_null($opbal)) {
		$opbal = 0;
	}
	if ($opbal > 0) {
		$db->query("insert into ".$findb.".".$drfile." (ddate,accountno,branch,sub,descript1,debit,runbal) values ('".$fromdate."',".$ac.",'".$br."',".$sb.",'Calculated Opening Balance',".$opbal.",".$opbal.")");
	} else {
		$cbal = $opbal *-1;
		$db->query("insert into ".$findb.".".$drfile." (ddate,accountno,branch,sub,descript1,credit,runbal) values ('".$fromdate."',".$ac.",'".$br."',".$sb.",'Calculated Opening Balance',".$cbal.",".$opbal.")");
	}
	$db->execute();
}

$where = " where 1";
$where .= " and accountno = ".$ac." and sub = ".$sb;
$where .= " and ddate >= '".$fromdate."' and ddate <= '".$todate."'";
$where1 = $where.' order by ddate,uid';

$accname = $_SESSION['s_clientname'];

$heading = $_SESSION['s_coyname'].' - '.$accname." - between '".$fromdate."' and '".$todate."'";

$db->query("insert into ".$findb.".".$drfile." select ddate,accountno,branch,sub,accno,br,subbr,' ',debit,credit,0 as runbal,reference,your_ref,inv,descript1 from ".$findb.".trmain ".$where1);
$db->execute();

// add uid and address fields
$db->query("alter table ".$findb.".".$drfile." add `uid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY");
$db->execute();

$db->query("select uid,debit,credit,accno,br,subbr,reference,descript1 from ".$findb.".".$drfile);
$rows = $db->resultset();
$rbal = $opbal;
foreach ($rows as $row) {
	extract($row);
	$id = $uid;
	$a = $accno;
	$b = $br;
	$s = $subbr;
	$ref = substr($reference,0,3);
	$d = $descript1;
	if ($d <> 'Calculated Opening Balance') {
		
		$rbal = $rbal + $debit - $credit;
		$db->query("update ".$findb.".".$drfile. " set runbal = ".$rbal." where uid = ".$id);
		$db->execute();
		
		if ($a < 1000) {	
			$db->query("select account from ".$findb.".glmast where accountno = ".$a." and branch = '".$b."' and sub = ".$s);
		} elseif ($a >= 10000000 && $a < 20000000) {
			$db->query("select asset as account from ".$findb.".fixassets where accountno = ".$a." and branch = '".$b."' and sub = ".$s);
		} elseif ($a >= 20000000 && $a < 30000000) {
			$db->query("select concat(".$cltdb.".members.firstname,' ',".$cltdb.".members.lastname) as account from ".$cltdb.".members,".$cltdb.".client_company_xref where ".$cltdb.".client_company_xref.crno = ".$a." and ".$cltdb.".client_company_xref.crsub = ".$s." and ".$cltdb.".members.member_id = ".$cltdb.".client_company_xref.client_id and ".$cltdb.".client_company_xref.company_id = ".$coyid);
		} elseif ($a >= 30000000) {
			$db->query("select concat(".$cltdb.".members.firstname,' ',".$cltdb.".members.lastname) as account from ".$cltdb.".members,".$cltdb.".client_company_xref where ".$cltdb.".client_company_xref.drno = ".$a." and ".$cltdb.".client_company_xref.drsub = ".$s." and ".$cltdb.".members.member_id = ".$cltdb.".client_company_xref.client_id and ".$cltdb.".client_company_xref.company_id = ".$coyid);
		}
		$row = $db->single();
		if ($a == 0) {
			switch ($ref) {
				case 'INV':
				$acct = 'Income';
				break;
				case 'C_S':
				$acct = 'Income';
				break;
				case 'NSS':
				$acct = 'Income';
				break;
				case 'GRN':
				$acct = 'Purchases';
				break;
				case 'C_P':
				$acct = 'Purchases';
				break;
				case 'NSP':
				$acct = 'Purchases';
				break;
				default:
				$acct = "Unknown";
				break;
			}
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
		
		$db->query("update ".$findb.".".$drfile. " set otherleg = '".$acct."' where uid = ".$id);
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
$grid->SelectCommand = "select reference,your_ref,ddate,otherleg,debit,credit,runbal,descript1 from ".$findb.".".$drfile.$where;

// set the ouput format to json
$grid->dataType = 'json';

// Let the grid create the model
$grid->setColModel(null);

// Set the url from where we obtain the data
$grid->setUrl('get1dr.php');


// Set some grid options
$grid->setGridOptions(array(
    "caption"=>$heading,
    "rowNum"=>20,
    "sortname"=>"ddate,uid",
    "rowList"=>array(20,100,200),
	"height"=>465,
	"width"=>900
    ));

// Enable footerdata an tell the grid to obtain it from the request
$grid->setGridOptions(array("footerrow"=>true,"userDataOnFooter"=>true));

$grid->addCol(array("name"=>"act"),"last");


// Change some property of the field(s)
$grid->setColProperty("reference", array("label"=>"Ref.", "width"=>25));
$grid->setColProperty("your_ref", array("label"=>"Our Ref.", "width"=>60));
$grid->setColProperty("ddate", array("label"=>"Date", "width"=>50, "formatter"=>"date", "formatoptions"=>array("srcformat"=> "Y-m-d", "newformat"=>"d/m/Y")));
$grid->setColProperty("otherleg", array("label"=>"Corresponding Entry", "width"=>100));
$grid->setColProperty("debit", array("label"=>"Debit", "width"=>40, "align"=>"right", "formatter"=>"number","formatoptions"=>array("thousandsSeparator"=>",","decimalPlaces"=>2)));
$grid->setColProperty("credit", array("label"=>"Credit", "width"=>40, "align"=>"right", "formatter"=>"number","formatoptions"=>array("thousandsSeparator"=>",","decimalPlaces"=>2)));
$grid->setColProperty("runbal", array("label"=>"Balance", "width"=>40, "align"=>"right", "formatter"=>"number","formatoptions"=>array("thousandsSeparator"=>",","decimalPlaces"=>2)));
$grid->setColProperty("descript1", array("label"=>"Description", "width"=>100));
$grid->setColProperty("act", array("label"=>"Actions",  "width"=>50, "sortable"=>false));

$grid->setSubGrid("getAllocations.php",
        array('Date', 'From_Ref', 'To_Ref', 'Amount'),
        array(80,80,70,70),
        array('left','left','left','right'));

$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#dr1list").getDataIDs();
	for(var i=0;i<ids.length;i++){
			var rowd = $("#dr1list").getRowData(ids[i]);
			var cl = ids[i];
			var rf = "'"+rowd.reference+"'";
			if (rowd.descript1 == 'Calculated Opening Balance') {
				be = '&nbsp';
				se = '&nbsp;';
			} else {
				be = '<img src="../images/into.png" title="View Transaction Details" onclick="javascript:viewtrans('+rf+')" ></ids>';
				se = '<img src="../images/printer.gif" title="Print Document" onclick="javascript:printdoc('+rf+')" ></ids>';
			}
			jQuery("#dr1list").setRowData(ids[i],{act:be+'&nbsp;&nbsp;&nbsp;'+se}); 
	}
}
LOADCOMPLETE;

$grid->setGridEvent("loadComplete",$loadevent);


// At end call footerData to put total  label
$grid->callGridMethod('#dr1list', 'footerData', array("set",array("acc"=>"Balances:")));
// Set which parameter to be sumarized
$summaryrows = array("debit"=>array("debit"=>"SUM"),"credit"=>array("credit"=>"SUM"));
// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>false,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

$buttonoptions = array("#dr1pager",
    array("buttonicon"=>"ui-icon-newwin","caption"=>"","position"=>"last","title"=>"Output to Excel.", "onClickButton"=>"js: function(){dr12xl();}")
);
$grid->callGridMethod("#dr1list", "navButtonAdd", $buttonoptions); 

$buttonoptions1 = array("#dr1pager",
    array("buttonicon"=>"ui-icon-print","caption"=>"","position"=>"last","title"=>"Output to PDF.", "onClickButton"=>"js: function(){dr12pdf();}")
);
$grid->callGridMethod("#dr1list", "navButtonAdd", $buttonoptions1); 

$buttonoptions2 = array("#dr1pager",
    array("buttonicon"=>"ui-icon-calculator","caption"=>"","position"=>"last","title"=>"Calculators", "onClickButton"=>"js: function(){showCalculators();}")
);
$grid->callGridMethod("#dr1list", "navButtonAdd", $buttonoptions2); 

// Run the script
$grid->renderGrid('#dr1list','#dr1pager',true, $summaryrows, null,true,true,true);
$conn = null;

?>




