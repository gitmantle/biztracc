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
$subscriber = $subid;
$sname = $row['uname'];

$cltdb = $_SESSION['s_cltdb'];

$filterfile = "ztmp".$user_id."_dupfilter";

$db->query("drop table if exists ".$cltdb.".".$filterfile);
$db->execute();

$db->query("create table ".$cltdb.".".$filterfile." (uid integer(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, member_id int, lastname varchar(45) default ' ', firstname varchar(45) default ' ',preferredname varchar(45) default ' ',checked char(3) default 'N', dob date)  engine myisam");
$db->execute();

$db->query("select member_id as mid,firstname as fname,lastname as lname,preferredname as pname,checked as chk, dob as birth from ".$cltdb.".members where sub_id = ".$subscriber);
$rows = $db->resultset();
foreach ($rows as $row) {
	extract($row);
	
	$db->query('select member_id as dmid,firstname as dfname,lastname as dlname from '.$cltdb.'.members where sub_id = '.$subscriber.' and lastname = "'.$lname.'" and firstname like "'.$fname.'%"');
	$r = $db->resultset();
	$numrows = $db->rowCount();
		
	if ($numrows > 1) {
		foreach ($r as $rw) {
			extract($rw);
			$db->query("insert into ".$cltdb.".".$filterfile." (member_id,lastname,firstname,preferredname,checked,dob) values (:member_id,:lastname,:firstname,:preferredname,:checked,:dob)");
			$db->bind(':member_id', $dmid);
			$db->bind(':lastname', $dlname);
			$db->bind(':firstname', $dfname);
			$db->bind(':preferredname', $pname);
			$db->bind(':checked', $chk);
			$db->bind(':dob', $birth);
			
			$db->execute();
		}
	}
	
}

$db->query("alter ignore table ".$cltdb.".".$filterfile." add unique index dupidx (member_id)");
$db->execute();
$db->query("alter table ".$cltdb.".".$filterfile." drop index dupidx");
$db->execute();

$db->closeDB();

require_once '../includes/jquery/jq-config.php';
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

// the actual query for the grid data
$grid->SelectCommand = "select uid,member_id,firstname,lastname,preferredname,checked,dob from ".$cltdb.".".$filterfile;

// set the ouput format to json
$grid->dataType = 'json';

// Let the grid create the model
$grid->setColModel(null);

// Set the url from where we obtain the data
$grid->setUrl('getduplicates.php');

// Set grid caption using the option caption
$grid->setGridOptions(array(
    "caption"=>"Duplicate Members",
    "rowNum"=>12,
    "sortname"=>"lastname",
    "rowList"=>array(12,30,50),
	"height"=>270,
	"width"=>800
    ));

$grid->addCol(array("name"=>"act"),"first");

// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"ID", "width"=>20, "hidden"=>true));
$grid->setColProperty("member_id", array("label"=>"ID", "width"=>50));
$grid->setColProperty("act", array("label"=>"Actions", "width"=>70));
$grid->setColProperty("checked", array("label"=>"Checked", "width"=>20, "hidden"=>true));
$grid->setColProperty("lastname", array("label"=>"Last Name", "width"=>210));
$grid->setColProperty("firstname", array("label"=>"First Name", "width"=>100));
$grid->setColProperty("preferredname", array("label"=>"Preferred", "width"=>75));
$grid->setColProperty("dob", array("label"=>"Age", "width"=>30));

// point to Age column to use the function named getAge
$grid->setColProperty('dob', array('formatter'=>'js:getAge'));

$getAge = <<<GETAGE
function getAge (cellValue, options, rowdata) 
{

	var dt = cellValue.split('-');
	var by = dt[0];
	var bm = dt[1];
	var bd = dt[2]; 
	
	var d = new Date();
	var curr_day = d.getDate();
	var curr_month = d.getMonth()+1;
	var curr_year = d.getFullYear();
	var birthYear = parseInt(by);
	var birthMonth = parseInt(bm);
	var birthDay = parseInt(bd);
	var age = curr_year - birthYear;
	if (curr_month < birthMonth || (curr_month == birthMonth && curr_day < birthDay)) {
		age = age - 1;
	}
	
	if(by == '0000') {
		age = 0;
	}
	return age;

}
GETAGE;
$grid->setJSCode($getAge); 

$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#memlistd").getDataIDs();
	for(var i=0;i<ids.length;i++){
			var rowd = $("#memlistd").getRowData(ids[i]);
			var chk = rowd.checked;
			var memid = rowd.member_id;
			var cl = ids[i];
			be = '<img src="../images/edit.png" title="Edit Member" onclick="javascript:editmem('+memid+')" ></ids>';
			if (chk == 'Yes') {
				ck = '<img src="../images/tick.png" title="Checked">'; 
			} else {
				ck = '<img src="../images/close.png" title="Not Checked">'; 
			}
			se = '<img src="../images/delete.png" title="Delete Member" onclick="javascript:delduplicate('+memid+')" >'; 
			jQuery("#memlistd").setRowData(ids[i],{act:be+'&nbsp;&nbsp;&nbsp'+ck+'&nbsp;&nbsp;&nbsp;'+se}); 
	}
}
LOADCOMPLETE;

$grid->setGridEvent("loadComplete",$loadevent);

$dcevent = <<<DBLCLICK
function(rowid) {
	var rowdata = $("#memlistd").getRowData(rowid);
	var memberid = rowdata.member_id;
	editmem(memberid);
}
DBLCLICK;

$grid->setGridEvent('ondblClickRow',$dcevent);


$grid->gSQLMaxRows = 4000;

// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>false,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

// Run the script
$grid->renderGrid('#memlistd','#mempagerd',true, null, null, true,true);


?>

